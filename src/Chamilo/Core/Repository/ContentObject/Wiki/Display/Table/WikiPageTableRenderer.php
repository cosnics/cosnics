<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display\Table;

use Chamilo\Core\Repository\ContentObject\Wiki\Display\Component\WikiBrowserComponent;
use Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager;
use Chamilo\Core\Repository\Display\Manager as DisplayManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
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
class WikiPageTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_VERSIONS = 'versions';
    public const TABLE_IDENTIFIER = DisplayManager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID;

    protected DatetimeUtilities $datetimeUtilities;

    protected StringUtilities $stringUtilities;

    protected User $user;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected ?WikiBrowserComponent $wikiBrowserComponent = null;

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

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest(
                    [DisplayManager::PARAM_ACTION => DisplayManager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM]
                ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

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
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE)
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_VERSIONS, $this->getTranslator()->trans('Versions', [], Manager::CONTEXT)
            )
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
        WikiBrowserComponent $wikiBrowserComponent, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->wikiBrowserComponent = $wikiBrowserComponent;

        return parent::render($parameterValues, $tableData, $tableName); // TODO: Change the autogenerated stub
    }

    /**
     * @throws \ReflectionException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $publication): string
    {
        $wikiPage = $this->get_publication_from_complex_content_object_item($publication);
        $complex_id = $publication->get_id();

        if ($publication->getAdditionalProperty('is_homepage') == 1)
        {
            $homepage = ' (' . $this->getTranslator()->trans('Homepage', [], Manager::CONTEXT) . ')';
        }

        if (isset($wikiPage))
        {
            if ($property = $column->get_name())
            {
                switch ($property)
                {
                    case ContentObject::PROPERTY_TITLE :
                        return '<a href="' . $this->wikiBrowserComponent->get_url(
                                [
                                    DisplayManager::PARAM_ACTION => Manager::ACTION_VIEW_WIKI_PAGE,
                                    DisplayManager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_id
                                ]
                            ) . '">' . htmlspecialchars($wikiPage->get_title()) . '</a>' . $homepage;
                    case self::PROPERTY_VERSIONS :
                        return (string) $wikiPage->get_version_count();
                    case ContentObject::PROPERTY_DESCRIPTION :
                        $description = str_ireplace(
                            ']]', '', str_ireplace('[[', '', str_ireplace('=', '', $wikiPage->get_description()))
                        );

                        return $this->getStringUtilities()->truncate($description, 50);
                    case ContentObject::PROPERTY_MODIFICATION_DATE :
                        return $this->getDatetimeUtilities()->formatLocaleDate(
                            null, $wikiPage->get_modification_date()
                        );
                }
            }
        }

        return parent::renderCell($column, $resultPosition, $publication);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $publication): string
    {
        $translator = $this->getTranslator();
        $complex_id = $publication->get_id();
        $wikiPage = $this->get_publication_from_complex_content_object_item($publication);

        $isOwner = $wikiPage->get_owner_id() == $this->getUser()->getId();

        $toolbar = new Toolbar();

        if ($this->wikiBrowserComponent->get_parent()->is_allowed_to_delete_child())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->wikiBrowserComponent->get_url(
                        [
                            DisplayManager::PARAM_ACTION => DisplayManager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            DisplayManager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_id
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        if ($this->wikiBrowserComponent->get_parent()->is_allowed_to_edit_content_object() || $isOwner)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $this->wikiBrowserComponent->get_url(
                        [
                            DisplayManager::PARAM_ACTION => DisplayManager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                            DisplayManager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_id
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($this->wikiBrowserComponent->get_parent()->is_allowed_to_edit_content_object())
        {
            if (($publication->getAdditionalProperty('is_homepage') == 0))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('SetAsHomepage', [], Manager::CONTEXT), new FontAwesomeGlyph('home'),
                        $this->wikiBrowserComponent->get_url(
                            [
                                DisplayManager::PARAM_ACTION => Manager::ACTION_SET_AS_HOMEPAGE,
                                DisplayManager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_id
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('SetAsHomepage', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('home', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        return $toolbar->render();
    }
}
