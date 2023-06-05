<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportService;
use Chamilo\Core\Repository\Common\Import\ImportTypeSelector;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Action;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\ActionGroup;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Generates the actions for a given TreeNode
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NodeBaseActionGenerator extends NodeActionGenerator
{

    /**
     * Node action generators for specific content object types
     *
     * @var NodeActionGenerator[]
     */
    protected $contentObjectTypeNodeActionGenerators;

    /**
     * Caching for import actions
     *
     * @var string[]
     */
    protected $importTypes;

    /**
     * Caching for create actions
     *
     * @var TypeSelectorOption[]
     */
    protected $typeSelectorOptions;

    /**
     * NodeActionGenerator constructor.
     *
     * @param Translation $translator
     * @param array $baseParameters
     * @param NodeActionGenerator[] $contentObjectTypeNodeActionGenerators
     */
    public function __construct(
        Translation $translator, array $baseParameters = [], $contentObjectTypeNodeActionGenerators = []
    )
    {
        parent::__construct($translator, $baseParameters);

        $this->contentObjectTypeNodeActionGenerators = $contentObjectTypeNodeActionGenerators;
    }

    /**
     * @param array $actions
     * @param TreeNode $treeNode
     */
    protected function addSelectFromActions(&$actions = [], TreeNode $treeNode)
    {
        $repoViewerContext = 'Chamilo\Core\Repository\Viewer';

        $baseParameters = [
            Manager::PARAM_ACTION => Manager::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER
        ];

        $url = $this->getUrlForNode($baseParameters, $treeNode->getId());

        $actions[] = new Action(
            'browse_repository', $this->translator->getTranslation('SelectFromRepository', null, $repoViewerContext),
            $url, ''
        );

        $workspaceParameters = $baseParameters;
        $workspaceParameters[\Chamilo\Core\Repository\Viewer\Manager::PARAM_IN_WORKSPACES] = true;

        $url = $this->getUrlForNode($workspaceParameters, $treeNode->getId());

        $actions[] = new Action(
            'browse_workspaces', $this->translator->getTranslation('SelectFromWorkspaces', null, $repoViewerContext),
            $url, ''
        );
    }

    /**
     * Generates the acions for a given TreeNode
     *
     * @param TreeNode $treeNode
     * @param bool $canEditTreeNode
     *
     * @return array|ActionInterface[]
     */
    public function generateNodeActions(TreeNode $treeNode, $canEditTreeNode = false): array
    {
        $actions = [];

        if ($canEditTreeNode)
        {
            $actions[] = $this->getUpdateNodeAction($treeNode);
            $actions[] = $this->getNodeReportingAction($treeNode);

            if (!$treeNode->isInDefaultTraversingOrder())
            {
                if (!$treeNode->isRootNode())
                {
                    $actions[] = $this->getBlockOrUnblockNodeAction($treeNode);
                }

                if ($treeNode->getContentObject() instanceof Section || $treeNode->isRootNode())
                {
                    $actions[] = $this->getToggleDefaultTraversingOrderAction($treeNode);
                }
            }

            if (!$treeNode->isRootNode())
            {
                $actions[] = $this->getDeleteNodeAction($treeNode);
                $actions[] = $this->getMoveNodeAction($treeNode);
            }

            $actions[] = $this->getCreatorActions($treeNode);
            $this->addSelectFromActions($actions, $treeNode);
            $actions[] = $this->getImportActions($treeNode);
        }

        $actions[] = $this->getMyProgressNodeAction($treeNode);
        $actions[] = $this->getNodeActivityAction($treeNode);

        if ($treeNode->hasChildNodes())
        {
            $actions[] = $this->getManageNodesAction($treeNode);
        }

        $actions[] = $this->getViewNodeAction($treeNode);

        $nodeSpecificActions = $this->getNodeSpecificActions($treeNode, $canEditTreeNode);

        if (is_array($nodeSpecificActions) && !empty($nodeSpecificActions))
        {
            $actions = array_merge($actions, $nodeSpecificActions);
        }

        return $actions;
    }

    /**
     * Returns the action to block or unblock a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getBlockOrUnblockNodeAction(TreeNode $treeNode)
    {
        $translationVariable =
            ($treeNode->getTreeNodeData() && $treeNode->getTreeNodeData()->isBlocked()) ? 'MarkAsOptional' :
                'MarkAsRequired';

        $icon = ($treeNode->getTreeNodeData() && $treeNode->getTreeNodeData()->isBlocked()) ? 'unlock' : 'ban';

        $title = $this->translator->getTranslation($translationVariable, null, Manager::CONTEXT);
        $url = $this->getUrlForNode(
            [Manager::PARAM_ACTION => Manager::ACTION_TOGGLE_BLOCKED_STATUS], $treeNode->getId()
        );

        return new Action('block', $title, $url, 'fas fa-' . $icon . '  fa-fw');
    }

    /**
     * Generates the several available create actions
     *
     * @param TreeNode $treeNode
     *
     * @return ActionGroup
     */
    protected function getCreatorActions(TreeNode $treeNode)
    {
        $baseParameters = [
            Manager::PARAM_ACTION => Manager::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
            \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager::ACTION_CREATOR
        ];

        if (!isset($this->typeSelectorOptions))
        {
            /** @var LearningPath $learningPath */
            $learningPath = $treeNode->getTree()->getRoot()->getContentObject();
            $typeSelectorFactory = new TypeSelectorFactory(
                $learningPath->get_allowed_types(),
                $this->getSession()->get(\Chamilo\Core\User\Manager::SESSION_USER_IO),
                TypeSelectorFactory::MODE_FLAT_LIST, false
            );

            $typeSelector = $typeSelectorFactory->getTypeSelector();

            $this->typeSelectorOptions = $typeSelector->getAllTypeSelectorOptions();
        }

        $createAction = new ActionGroup('create');

        foreach ($this->typeSelectorOptions as $option)
        {
            $id = $option->get_template_registration_id();
            $baseParameters[TypeSelector::PARAM_SELECTION] = $id;
            $url = $this->getUrlForNode($baseParameters, $treeNode->getId());

            $createAction->addAction(
                new Action('create_' . $id, $option->get_label(), $url, '')
            );
        }

        return $createAction;
    }

    /**
     * Returns the action to delete a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getDeleteNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('DeleterComponent', null, Manager::CONTEXT);
        $url = $this->getUrlForNode(
            [Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM], $treeNode->getId()
        );

        return new Action(
            'delete', $title, $url, 'fas fa-times fa-fw',
            $this->translator->getTranslation('Confirm', null, StringUtilities::LIBRARIES)
        );
    }

    /**
     * Returns the import actions
     *
     * @param TreeNode $treeNode
     *
     * @return ActionGroup
     */
    protected function getImportActions(TreeNode $treeNode)
    {
        $creatorParameters = $this->baseParameters;
        $creatorParameters[Manager::PARAM_ACTION] = Manager::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
        $creatorParameters[Manager::PARAM_CHILD_ID] = $treeNode->getId();
        $creatorParameters[\Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION] =
            \Chamilo\Core\Repository\Viewer\Manager::ACTION_IMPORTER;

        if (!isset($this->importTypes))
        {
            /** @var LearningPath $learningPath */
            $learningPath = $treeNode->getTree()->getRoot()->getContentObject();
            $importTypeSelector = new ImportTypeSelector($creatorParameters, $learningPath->get_allowed_types());
            $this->importTypes = $importTypeSelector->getImportTypes();
        }

        $actionGroup = new ActionGroup('import');

        foreach ($this->importTypes as $importType => $importPropertes)
        {
            $creatorParameters[ContentObjectImportService::PARAM_IMPORT_TYPE] = $importType;

            $actionGroup->addAction(
                new Action(
                    'import_' . $importType, $importPropertes['label'], $this->getUrlForNode($creatorParameters), ''
                )
            );
        }

        return $actionGroup;
    }

    /**
     * Returns the action to view the activity of a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getManageNodesAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('ManagerComponent', null, Manager::CONTEXT);
        $url = $this->getUrlForNode([Manager::PARAM_ACTION => Manager::ACTION_MANAGE], $treeNode->getId());

        return new Action('manage', $title, $url, 'fas fa-bars fa-fw');
    }

    /**
     * Returns the action to move a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getMoveNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('Move', null, Manager::CONTEXT);
        $url = $this->getUrlForNode([Manager::PARAM_ACTION => Manager::ACTION_MOVE], $treeNode->getId());

        return new Action('move', $title, $url, 'fas fa-random fa-fw');
    }

    /**
     * Returns the action to view the progress for a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getMyProgressNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('MyProgress', null, Manager::CONTEXT);
        $url = $this->getUrlForNode([Manager::PARAM_ACTION => Manager::ACTION_REPORTING], $treeNode->getId());

        return new Action('progress', $title, $url, 'fas fa-chart-pie fa-fw');
    }

    /**
     * Returns the action to view the activity of a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getNodeActivityAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('ActivityComponent', null, Manager::CONTEXT);
        $url = $this->getUrlForNode([Manager::PARAM_ACTION => Manager::ACTION_ACTIVITY], $treeNode->getId());

        return new Action('activity', $title, $url, 'fas fa-mouse-pointer fa-fw');
    }

    /**
     * Returns the action to view the reporting for a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getNodeReportingAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('Reporting', null, Manager::CONTEXT);
        $url = $this->getUrlForNode(
            [Manager::PARAM_ACTION => Manager::ACTION_VIEW_USER_PROGRESS], $treeNode->getId()
        );

        return new Action('reporting', $title, $url, 'fas fa-chart-bar fa-fw');
    }

    /**
     * Generates the node specific actions for the given TreeNode
     *
     * @param TreeNode $treeNode
     * @param bool $canEditTreeNode
     *
     * @return array|Action[]
     */
    protected function getNodeSpecificActions(TreeNode $treeNode, $canEditTreeNode = false)
    {
        $contentObjectType = $treeNode->getContentObject()->getType();
        if (array_key_exists($contentObjectType, $this->contentObjectTypeNodeActionGenerators))
        {
            return $this->contentObjectTypeNodeActionGenerators[$contentObjectType]->generateNodeActions(
                $treeNode, $canEditTreeNode
            );
        }

        return [];
    }

    public function getSession(): SessionInterface
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SessionInterface::class);
    }

    /**
     * Returns the action to toggle the default traversing order for a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getToggleDefaultTraversingOrderAction(TreeNode $treeNode)
    {
        $translationVariable =
            ($treeNode->getTreeNodeData() && $treeNode->getTreeNodeData()->enforcesDefaultTraversingOrder()) ?
                'DisableDefaultTraversingOrder' : 'EnableDefaultTraversingOrder';

        $icon = ($treeNode->getTreeNodeData() && $treeNode->getTreeNodeData()->enforcesDefaultTraversingOrder()) ?
            'sitemap' : 'sitemap';

        $title = $this->translator->getTranslation($translationVariable, null, Manager::CONTEXT);
        $url = $this->getUrlForNode(
            [Manager::PARAM_ACTION => Manager::ACTION_TOGGLE_ENFORCE_DEFAULT_TRAVERSING_ORDER], $treeNode->getId()
        );

        return new Action('default_traversing_order', $title, $url, 'fas fa-' . $icon . '  fa-fw');
    }

    /**
     * Returns the action to update a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getUpdateNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('UpdaterComponent', null, Manager::CONTEXT);
        $url = $this->getUrlForNode(
            [Manager::PARAM_ACTION => Manager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM], $treeNode->getId()
        );

        return new Action('edit', $title, $url, 'fas fa-pencil fa-fw');
    }

    /**
     * Returns the action to view a given TreeNode
     *
     * @param TreeNode $treeNode
     *
     * @return Action
     */
    protected function getViewNodeAction(TreeNode $treeNode)
    {
        $title = $this->translator->getTranslation('ReturnToLearningPath', null, Manager::CONTEXT);
        $url = $this->getUrlForNode(
            [Manager::PARAM_ACTION => Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT], $treeNode->getId()
        );

        return new Action('view', $title, $url, 'fas fa-file fa-fw');
    }
}
