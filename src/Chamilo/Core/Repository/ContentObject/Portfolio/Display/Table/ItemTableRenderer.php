<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Table;

use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component\ManagerComponent;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Display\Manager as DisplayManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ItemTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_VERSIONS = 'versions';
    public const TABLE_IDENTIFIER = Manager::PARAM_STEP;

    protected DatetimeUtilities $datetimeUtilities;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     * @var ?\Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component\ManagerComponent
     */
    protected ?ManagerComponent $managerComponent = null;

    protected StringUtilities $stringUtilities;

    protected User $user;

    public function __construct(
        StringUtilities $stringUtilities, DatetimeUtilities $datetimeUtilities, User $user, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->user = $user;

        parent::__construct($translator, $urlGenerator, $htmlTableRenderer, $pager);
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->managerComponent->canEditComplexContentObjectPathNode($this->managerComponent->get_current_node()))
        {
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest(
                        [DisplayManager::PARAM_ACTION => DisplayManager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM]
                    ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
                )
            );

            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest([DisplayManager::PARAM_ACTION => Manager::ACTION_MOVE]),
                    $translator->trans('MoveSelected', [], StringUtilities::LIBRARIES), false
                )
            );
        }

        if ($this->managerComponent->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $actions->addAction(
                new TableAction(
                    $urlGenerator->fromRequest([DisplayManager::PARAM_ACTION => Manager::ACTION_RIGHTS]),
                    $translator->trans('ConfigureRightsSelected', [], StringUtilities::LIBRARIES), false
                )
            );
        }

        return $actions;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @throws \ReflectionException
     */
    private function get_publication_from_complex_content_object_item($clo_item)
    {
        return DataManager::retrieve_by_id(
            ContentObject::class, $clo_item->get_ref()
        );
    }

    protected function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_CREATION_DATE)
        );
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
        ManagerComponent $managerComponent, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->managerComponent = $managerComponent;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Portfolio\ComplexContentObjectPathNode $node
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $node): string
    {
        $datetimeUtilities = $this->getDatetimeUtilities();
        $translator = $this->getTranslator();

        $content_object = $node->get_content_object();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                return $content_object->get_title();
            case ContentObject::PROPERTY_CREATION_DATE :
                return $datetimeUtilities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $content_object->get_creation_date()
                );
            case ContentObject::PROPERTY_MODIFICATION_DATE :
                return $datetimeUtilities->formatLocaleDate(
                    $translator->trans('DateTimeFormatLong', [], StringUtilities::LIBRARIES),
                    $content_object->get_modification_date()
                );
        }

        return parent::renderCell($column, $resultPosition, $node);
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Portfolio\ComplexContentObjectPathNode $node
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $node): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $toolbar = new Toolbar();

        if ($this->managerComponent->get_parent()->is_allowed_to_view_content_object($node))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewerComponent'), new FontAwesomeGlyph('desktop', [], null, 'fas'),
                    $urlGenerator->fromRequest(
                        [
                            DisplayManager::PARAM_ACTION => DisplayManager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT,
                            Manager::PARAM_STEP => $node->get_id()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('ViewNotAllowed'), new FontAwesomeGlyph('desktop', ['text-muted'], null, 'fas'),
                    null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->managerComponent->canEditComplexContentObjectPathNode($node->get_parent()))
        {
            $variable = $node->get_content_object() instanceof Portfolio ? 'MoveFolder' : 'MoverComponent';

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans($variable),
                    new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas'),
                    $urlGenerator->fromRequest(
                        [DisplayManager::PARAM_ACTION => Manager::ACTION_MOVE, Manager::PARAM_STEP => $node->get_id()]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->managerComponent->get_parent()->is_allowed_to_set_content_object_rights())
        {
            $variable = $node->get_content_object() instanceof Portfolio ? 'RightsFolder' : 'RightsComponent';

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans($variable), new FontAwesomeGlyph('lock', [], null, 'fas'),
                    $urlGenerator->fromRequest(
                        [DisplayManager::PARAM_ACTION => Manager::ACTION_RIGHTS, Manager::PARAM_STEP => $node->get_id()]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->managerComponent->canEditComplexContentObjectPathNode($node->get_parent()))
        {
            $variable = $node->get_content_object() instanceof Portfolio ? 'DeleteFolder' : 'DeleterComponent';

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans($variable), new FontAwesomeGlyph('times', [], null, 'fas'),
                    $urlGenerator->fromRequest(
                        [
                            DisplayManager::PARAM_ACTION => DisplayManager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            Manager::PARAM_STEP => $node->get_id()
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->render();
    }
}
