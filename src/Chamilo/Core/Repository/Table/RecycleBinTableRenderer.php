<?php
namespace Chamilo\Core\Repository\Table;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Service\ContentObjectUrlGenerator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RecycleBinTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_ORIGINAL_LOCATION = 'original_location';

    public const TABLE_IDENTIFIER = Manager::PARAM_CONTENT_OBJECT_ID;

    protected ContentObjectUrlGenerator $contentObjectUrlGenerator;

    /**
     * @var string[]
     */
    protected array $parent_title_cache = [];

    protected StringUtilities $stringUtilities;

    public function __construct(
        ContentObjectUrlGenerator $contentObjectUrlGenerator, StringUtilities $stringUtilities, Translator $translator,
        UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->stringUtilities = $stringUtilities;
        $this->contentObjectUrlGenerator = $contentObjectUrlGenerator;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getContentObjectUrlGenerator(): ContentObjectUrlGenerator
    {
        return $this->contentObjectUrlGenerator;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTableActions(): TableActions
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $restoreUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_RESTORE_CONTENT_OBJECTS
            ]
        );

        $actions->addAction(
            new TableAction(
                $restoreUrl, $translator->trans('RestoreSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        $deleteUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Application::PARAM_ACTION => Manager::ACTION_DELETE_CONTENT_OBJECTS,
                Manager::PARAM_DELETE_PERMANENTLY => 1
            ]
        );

        $actions->addAction(
            new TableAction(
                $deleteUrl, $translator->trans('DeleteSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns(): void
    {
        $translator = $this->getTranslator();
        $glyph = new FontAwesomeGlyph('folder', [], $translator->trans('Type', [], Manager::CONTEXT));

        $this->addColumn(
            new StaticTableColumn(ContentObject::PROPERTY_TYPE, $glyph->render())
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_TITLE
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                ContentObject::class, ContentObject::PROPERTY_DESCRIPTION
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_ORIGINAL_LOCATION, $translator->trans('OriginalLocation', [], Manager::CONTEXT)
            )
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $contentObject): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();
        $stringUtilities = $this->getStringUtilities();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TYPE :
                return $contentObject->get_icon_image(IdentGlyph::SIZE_MINI);
            case ContentObject::PROPERTY_TITLE :
                $title = parent::renderCell($column, $resultPosition, $contentObject);
                $title_short = $stringUtilities->truncate($title, 50);

                $viewUrl = $this->getContentObjectUrlGenerator()->getViewUrl($contentObject);

                return '<a href="' . htmlentities($viewUrl) . '" title="' . htmlentities($title) . '">' . $title_short .
                    '</a>';
            case ContentObject::PROPERTY_DESCRIPTION :
                return $stringUtilities->truncate(
                    html_entity_decode($contentObject->get_description()), 50
                );
            case self::PROPERTY_ORIGINAL_LOCATION :
                $pid = $contentObject->get_parent_id();

                if (!isset($this->parent_title_cache[$pid]))
                {
                    $category = DataManager::retrieve_categories(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                RepositoryCategory::class, DataClass::PROPERTY_ID
                            ), new StaticConditionVariable($pid)
                        )
                    )->current();

                    $this->parent_title_cache[$pid] = '<a href="' . htmlentities(
                            $urlGenerator->fromParameters(
                                [
                                    Manager::PARAM_CATEGORY_ID => $pid,
                                    Application::PARAM_ACTION => Manager::ACTION_BROWSE_CONTENT_OBJECTS
                                ]
                            )
                        ) . '" title="' . htmlentities($translator->trans('BrowseThisCategory', [], Manager::CONTEXT)) .
                        '">' . ($category ? $category->get_name() : $translator->trans(
                            'Root', [], StringUtilities::LIBRARIES
                        )) . '</a>';
                }

                return $this->parent_title_cache[$pid];
        }

        return parent::renderCell($column, $resultPosition, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $contentObject): string
    {
        $translator = $this->getTranslator();
        $contentObjectUrlGenerator = $this->getContentObjectUrlGenerator();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Restore', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('undo'),
                $contentObjectUrlGenerator->getRestoreUrl($contentObject), ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $contentObjectUrlGenerator->getDeleteUrl($contentObject), ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
