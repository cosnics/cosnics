<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Display\Manager as DisplayManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\ListTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Display\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeNodeTableRenderer extends ListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_LAST_START_TIME = 'last_start_time';
    public const PROPERTY_SCORE = 'score';
    public const PROPERTY_STATUS = 'status';
    public const PROPERTY_TIME = 'time';

    public const TABLE_IDENTIFIER = Manager::PARAM_CHILD_ID;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     * @var ?\Chamilo\Libraries\Architecture\Application\Application
     */
    protected ?Application $application = null;

    protected DatetimeUtilities $datetimeUtilities;

    public function __construct(
        DatetimeUtilities $datetimeUtilities, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->datetimeUtilities = $datetimeUtilities;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    protected function getCurrentTreeNode(): ?TreeNode
    {
        return $this->application->getCurrentTreeNode();
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->application->canEditTreeNode(
            $this->getCurrentTreeNode()
        ))
        {
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [Manager::PARAM_ACTION => DisplayManager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM],
                        [Manager::PARAM_CHILD_ID]
                    ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
                )
            );

            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [Manager::PARAM_ACTION => Manager::ACTION_MOVE]
                    ), $translator->trans('MoveSelected', [], StringUtilities::LIBRARIES), false
                )
            );
        }

        return $actions;
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE));
        $this->addColumn(new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_CREATION_DATE));
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $treeNode): string
    {
        $translator = $this->getTranslator();
        $datetimeUtiltities = $this->getDatetimeUtilities();

        $contentObject = $treeNode->getContentObject();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_CREATION_DATE :
                return $datetimeUtiltities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $contentObject->get_creation_date()
                );
            case ContentObject::PROPERTY_MODIFICATION_DATE :
                return $datetimeUtiltities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $contentObject->get_modification_date()
                );
        }

        return $contentObject->getDefaultProperty($column->get_name());
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    protected function renderIdentifierCell($treeNode): string
    {
        return (string) $treeNode->getId();
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $treeNode
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $treeNode): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $toolbar = new Toolbar();

        if ($this->application->get_parent()->is_allowed_to_view_content_object($treeNode))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewerComponent', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('desktop', [], null, 'fas'), $urlGenerator->fromRequest(
                    [
                        Manager::PARAM_ACTION => DisplayManager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                        Manager::PARAM_CHILD_ID => $treeNode->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewNotAllowed', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('desktop', ['text-muted'], null, 'fas'), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->application->canEditTreeNode($treeNode->getParentNode()))
        {
            $variable = $treeNode->getContentObject() instanceof LearningPath ? 'MoveFolder' : 'MoverComponent';

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans($variable, [], Manager::CONTEXT),
                    new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas'),
                    $urlGenerator->fromRequest(
                        [Manager::PARAM_ACTION => Manager::ACTION_MOVE, Manager::PARAM_CHILD_ID => $treeNode->getId()]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );

            $variable = $treeNode->getContentObject() instanceof LearningPath ? 'DeleteFolder' : 'DeleterComponent';

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans($variable, [], Manager::CONTEXT), new FontAwesomeGlyph('times', [], null, 'fas'),
                    $urlGenerator->fromRequest(
                        [
                            Manager::PARAM_ACTION => DisplayManager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            Manager::PARAM_CHILD_ID => $treeNode->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}
