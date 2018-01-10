<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form\DirectMoverForm;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Translation\Translation;

class ViewerComponent extends BaseHtmlTreeComponent
{

    /**
     * The button toolbar
     *
     * @var ButtonToolbar
     */
    protected $buttonToolbar;

    public function build()
    {
        $translator = Translation::getInstance();

        $learning_path = $this->learningPath;

        if (! $learning_path)
        {
            throw new ObjectNotExistException($translator->getTranslation('LearningPath'));
        }

        $trackingService = $this->getTrackingService();

        $trackingService->trackAttemptForUser(
            $this->learningPath,
            $this->getCurrentTreeNode(),
            $this->getUser());

        if (! $this->canEditCurrentTreeNode() && $trackingService->isCurrentTreeNodeBlocked(
            $learning_path,
            $this->getUser(),
            $this->getCurrentTreeNode()))
        {
            $html = array();

            $html[] = parent::render_header();
            $html[] = '<div class="alert alert-danger">';
            $html[] = Translation::get('NotYetAllowedToView');

            $responsibleNodes = $trackingService->getResponsibleNodesForBlockedTreeNode(
                $learning_path,
                $this->getUser(),
                $this->getCurrentTreeNode());

            $html[] = '<br /><br />';
            $html[] = '<ul>';

            $automaticNumberingService = $this->getAutomaticNumberingService();

            foreach ($responsibleNodes as $responsibleNode)
            {
                $nodeUrl = $this->get_url(array(self::PARAM_CHILD_ID => $responsibleNode->getId()));

                $html[] = '<li>';
                $html[] = '<a href="' . $nodeUrl . '">';

                $html[] = $automaticNumberingService->getAutomaticNumberedTitleForTreeNode($responsibleNode);

                $html[] = '</a>';

                if ($responsibleNode->getContentObject() instanceof Assessment)
                {
                    $masteryScore = $responsibleNode->getTreeNodeData()->getMasteryScore();
                    if ($masteryScore > 0)
                    {
                        $html[] = ' (' . $translator->getTranslation('MasteryScore') . ': ' . $masteryScore . '%)';
                    }
                }

                $html[] = '</li>';
            }

            $html[] = '</ol>';
            $html[] = '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }

        $embedder = Embedder::factory(
            $this,
            $this->getTrackingService(),
            $this->learningPath,
            $this->getCurrentTreeNode());

        return $embedder->run();
    }

    public function render_header()
    {
        $buttonToolbarRenderer = new ButtonToolBarRenderer($this->getButtonToolbar());
        $translator = Translation::getInstance();

        $html = array();

        $html[] = parent::render_header();
        $html[] = $buttonToolbarRenderer->render();

        if ($this->canEditCurrentTreeNode())
        {
            $html[] = $this->renderMovePanel();
        }

        if ($this->canEditCurrentTreeNode() &&
            (
                (
                    $this->getCurrentTreeNode()->getTreeNodeData() &&
                    $this->getCurrentTreeNode()->getTreeNodeData()->isBlocked()
                ) ||
                $this->getCurrentTreeNode()->isInDefaultTraversingOrder()
            )
        )
        {
            $firstParent = $this->getCurrentTreeNode()->getFirstParentThatEnforcesDefaultTraversingOrder();
            $title = ($firstParent instanceof TreeNode) ?
                $this->getAutomaticNumberingService()->getAutomaticNumberedTitleForTreeNode($firstParent) : '';

            $message = $this->getCurrentTreeNode()->isInDefaultTraversingOrder() ?
                'LearningPathEnforcesDefaultTraversingOrder' : 'ThisStepIsRequired';

            $html[] = '<div class="alert alert-warning">' .
                $translator->getTranslation($message, array('DefaultTraversingOrderParent' => $title)) . '</div>';
        }

        if ($this->canEditCurrentTreeNode() &&
            $this->getCurrentTreeNode()->getTreeNodeData()->enforcesDefaultTraversingOrder())
        {
            $html[] = '<div class="alert alert-warning">' .
                $translator->getTranslation('ThisStepEnforcesDefaultTraversingOrder') . '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(Manager::package(), true) . 'KeyboardNavigation.js');

        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the button toolbar
     */
    protected function getButtonToolbar()
    {
        $translator = Translation::getInstance();

        if (! isset($this->buttonToolbar))
        {
            $buttonToolbar = new ButtonToolBar();
            $this->buttonToolbar = $buttonToolbar;

            if (! $this->canEditCurrentTreeNode())
            {
                return $this->buttonToolbar;
            }

            $primaryActions = new ButtonGroup();
            $secondaryActions = new ButtonGroup();
            $tertiaryActions = new ButtonGroup();

            $this->addCreatorButtons($primaryActions, $translator);
            $this->addManageContentObjectButton($secondaryActions, $translator);
            $this->addNodeSpecificButtons($primaryActions, $secondaryActions);

            if ($this->get_action() != self::ACTION_REPORTING)
            {
                $this->addReportingButtons($tertiaryActions, $translator);
            }

            $buttonToolbar->addButtonGroup($primaryActions);
            $buttonToolbar->addButtonGroup($secondaryActions);
            $buttonToolbar->addButtonGroup($tertiaryActions);
        }

        return $this->buttonToolbar;
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     */
    public function addNodeSpecificButtons(ButtonGroup $primaryActions, ButtonGroup $secondaryActions)
    {
        $object_namespace = $this->getCurrentTreeNode()->getContentObject()->package();
        $integration_class_name = $object_namespace . '\Integration\\' . self::package() . '\Manager';

        if (class_exists($integration_class_name))
        {
            try
            {
                $application = $this->getApplicationFactory()->getApplication(
                    $integration_class_name::context(),
                    new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
                $application->get_node_tabs($primaryActions, $secondaryActions, $this->getCurrentTreeNode());
            }
            catch (\Exception $exception)
            {
            }
        }
    }

    /**
     * Builds the attachment url TODO: Currently moved the complex content object item to the selected complex content
     * object item because the wrong parameter was used in the viewer
     *
     * @param $attachment ContentObject
     *
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment)
    {
        return parent::get_content_object_display_attachment_url($attachment, $this->getCurrentTreeNode()->getId());
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addCreatorButtons($buttonGroup, $translator)
    {
        if ($this->canEditCurrentTreeNode())
        {
            $parameters = $this->get_parameters();
            $parameters[self::PARAM_ACTION] = self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
            $parameters[self::PARAM_CHILD_ID] = $this->getCurrentTreeNode()->getId();

            $allowedTypes = $this->learningPath->get_allowed_types();

            $actionSelector = new \Chamilo\Core\Repository\ContentObject\LearningPath\Display\ActionSelector(
                $this,
                $this->getUser()->getId(),
                $allowedTypes,
                $parameters,
                array(),
                'btn-primary');

            /** @var ClassnameUtilities $classNameUtilities */
            $classNameUtilities = $this->getService('chamilo.libraries.architecture.classname_utilities');
            $firstItemContext = $classNameUtilities->getNamespaceParent(array_shift($allowedTypes), 3);
            $itemTranslation = $translator->getTranslation('TypeName', null, $firstItemContext);

            $actionButton = $actionSelector->getActionButton(
                $translator->getTranslation(
                    'CreateItem',
                    array('ITEM' => lcfirst($itemTranslation)),
                    Manager::context()),
                new FontAwesomeGlyph('plus'));

            $buttonGroup->addButton($actionButton);

            $parameters[CreatorComponent::PARAM_CREATE_MODE] = CreatorComponent::CREATE_MODE_FOLDER;

            $folderSelector = new ActionSelector(
                $this,
                $this->getUser()->getId(),
                array(Section::class_name()),
                $parameters);

            $folderButton = $folderSelector->getActionButton(
                $translator->getTranslation('CreateFolder', null, Manager::context()),
                new FontAwesomeGlyph('plus'));

            $folderButton->addSubButton(new SubButtonDivider());
            $folderButton->addSubButton(
                new SubButton(
                    $translator->getTranslation('CopyFromOtherLearningPaths'),
                    new FontAwesomeGlyph('copy'),
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_COPY_SECTIONS))));

            $buttonGroup->addButton($folderButton);
        }
    }

    /**
     * Adds the buttons to manage the current content object
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addManageContentObjectButton($buttonGroup, $translator)
    {
        if ($this->canEditCurrentTreeNode())
        {
            $editTitle = $translator->getTranslation('UpdaterComponent', null, Manager::context());
            $editImage = new FontAwesomeGlyph('pencil');
            $editURL = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId()));

            $editButton = new SplitDropdownButton($editTitle, $editImage, $editURL);

            $this->addDeleteButton($editButton, $translator);
            $this->addMoveButton($editButton, $translator);
            $this->addBlockedStatusButton($editButton, $translator, $this->getCurrentTreeNode());
            $this->addManageButton($editButton, $translator);

            $buttonGroup->addButton($editButton);
        }
    }

    /**
     * Adds the delete button
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addDeleteButton($button, $translator)
    {
        if (! $this->getCurrentTreeNode()->isRootNode() &&
             $this->canEditTreeNode($this->getCurrentTreeNode()->getParentNode()))
        {
            $button->addSubButton(
                new SubButton(
                    $translator->getTranslation('DeleterComponent', null, Manager::context()),
                    new FontAwesomeGlyph('times'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId())),
                    SubButton::DISPLAY_ICON_AND_LABEL,
                    true));
        }
    }

    /**
     * Adds the manage button
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addManageButton($button, $translator)
    {
        if ($this->canEditCurrentTreeNode())
        {
            if ($this->getCurrentTreeNode()->hasChildNodes())
            {
                if (! $this->getCurrentTreeNode()->isRootNode())
                {
                    $button->addSubButton(new SubButtonDivider());
                }

                $button->addSubButton(
                    new SubButton(
                        $translator->getTranslation('ManagerComponent', null, Manager::context()),
                        new FontAwesomeGlyph('bars'),
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_MANAGE,
                                self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId()))));
            }
        }
    }

    /**
     * Adds a button to block / unblock the status of the given learning path tree node
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     * @param TreeNode $treeNode
     */
    protected function addBlockedStatusButton(SplitDropdownButton $button, Translation $translator, TreeNode $treeNode)
    {
        /** @var LearningPath $learningPath */
        $learningPath = $this->learningPath;

        if (!$this->canEditCurrentTreeNode()
            || $treeNode->isInDefaultTraversingOrder()
        )
        {
            return;
        }

        if (!$treeNode->isRootNode())
        {
            $translationVariable = ($treeNode->getTreeNodeData() &&
                $treeNode->getTreeNodeData()->isBlocked()) ?
                'MarkAsOptional' : 'MarkAsRequired';

            $icon = ($treeNode->getTreeNodeData() &&
                $treeNode->getTreeNodeData()->isBlocked()) ?
                'unlock' : 'ban';

            $blockNode = new SubButton(
                $translator->getTranslation($translationVariable, null, Manager::context()),
                new FontAwesomeGlyph($icon),
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_TOGGLE_BLOCKED_STATUS,
                        self::PARAM_CHILD_ID => $treeNode->getId()
                    )
                ),
                Button::DISPLAY_ICON_AND_LABEL
            );

            $button->addSubButton($blockNode);
        }

        if ($treeNode->getContentObject() instanceof Section || $treeNode->isRootNode())
        {
            $translationVariable = ($treeNode->getTreeNodeData() &&
                $treeNode->getTreeNodeData()->enforcesDefaultTraversingOrder()) ?
                'DisableDefaultTraversingOrder' : 'EnableDefaultTraversingOrder';

            $icon = ($treeNode->getTreeNodeData() &&
                $treeNode->getTreeNodeData()->enforcesDefaultTraversingOrder()) ?
                'sitemap' : 'sitemap';

            $blockNode = new SubButton(
                $translator->getTranslation($translationVariable, null, Manager::context()),
                new FontAwesomeGlyph($icon),
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_TOGGLE_ENFORCE_DEFAULT_TRAVERSING_ORDER,
                        self::PARAM_CHILD_ID => $treeNode->getId()
                    )
                ),
                Button::DISPLAY_ICON_AND_LABEL
            );

            $button->addSubButton($blockNode);
        }
    }

    /**
     * Adds a move button where you can directly select a parent / position to which you want to move the selected
     * item
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addMoveButton(SplitDropdownButton $button, Translation $translator)
    {
        if ($this->getCurrentTreeNode()->isRootNode() ||
             ! $this->canEditTreeNode($this->getCurrentTreeNode()->getParentNode()))
        {
            return;
        }

        $moveButton = new SubButton(
            $translator->getTranslation('Move', null, Manager::context()),
            new FontAwesomeGlyph('random'),
            '#',
            Button::DISPLAY_ICON_AND_LABEL,
            false,
            'mover-open');

        $button->addSubButton($moveButton);
    }

    /**
     *
     * @param ButtonGroup $buttonGroup
     * @param Translation $translator
     */
    protected function addReportingButtons($buttonGroup, $translator)
    {
        $label = $translator->getTranslation('MyProgress', null, Manager::context());

        $url = $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_REPORTING,
                self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId()));

        $icon = new FontAwesomeGlyph('pie-chart');

        if (! $this->canEditCurrentTreeNode())
        {
            $splitDropDownButton = new SplitDropdownButton($label, $icon, $url);
        }
        else
        {
            $splitDropDownButton = new SplitDropdownButton(
                $translator->getTranslation('Reporting', null, Manager::context()),
                new FontAwesomeGlyph('bar-chart'),
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_USER_PROGRESS,
                        self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId())));

            $splitDropDownButton->addSubButton(new SubButton($label, $icon, $url));
        }

        $this->addActivityButton($splitDropDownButton, $translator);
        $this->addStudentViewButton($splitDropDownButton, $translator);
        $buttonGroup->addButton($splitDropDownButton);
    }

    /**
     * Adds the activity button
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addActivityButton(SplitDropdownButton $button, $translator)
    {
        $extraButton = new SubButton(
            $translator->getTranslation('ActivityComponent', null, Manager::context()),
            new FontAwesomeGlyph('mouse-pointer'),
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_ACTIVITY,
                    self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId())));

        $button->addSubButton($extraButton);
    }

    /**
     * Adds the activity button
     *
     * @param SplitDropdownButton $button
     * @param Translation $translator
     */
    protected function addStudentViewButton(SplitDropdownButton $button, $translator)
    {
        $extraButton = new SubButton(
            $translator->getTranslation('ShowStudentView', null, Manager::context()),
            new FontAwesomeGlyph('user'),
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_SHOW_STUDENT_VIEW,
                    self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId())));

        $button->addSubButton($extraButton);
    }

    /**
     * Renders the move panel that will be made visible when the move button is pressed
     *
     * @return string
     */
    protected function renderMovePanel()
    {
        $translator = Translation::getInstance();

        $form = new DirectMoverForm(
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_MOVE_DIRECTLY,
                    self::PARAM_CHILD_ID => $this->getCurrentTreeNodeDataId(),
                    self::PARAM_CONTENT_OBJECT_ID => $this->getCurrentContentObject()->getId())),
            $this->getTree(),
            $this->getCurrentTreeNode(),
            $this->getAutomaticNumberingService());

        $html = array();

        $html[] = '<div class="panel panel-default" id="mover" style="display: none;">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = '<div class="pull-right">';
        $html[] = '<a href="#" id="mover-close" style="color: black; opacity: 0.5">';
        $html[] = '<span class="inline-glyph glyphicon glyphicon-remove"></span>';
        $html[] = '</a>';
        $html[] = '</div>';
        $html[] = $translator->getTranslation('Move', null, Manager::context());
        $html[] = '</h3>';
        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $form->toHtml();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function get_root_content_object()
    {
        return $this->getCurrentTreeNode()->getContentObject();
    }

}
