<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Table;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
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
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ComplexTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_WEIGHT = 'weight';

    public const TABLE_IDENTIFIER = Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     * @var ?\Chamilo\Libraries\Architecture\Application\Application
     */
    protected ?Application $application = null;

    protected function check_move_allowed($cloi)
    {
        $moveup_allowed = true;
        $movedown_allowed = true;

        $count = DataManager::count_complex_content_object_items(
            ComplexContentObjectItem::class, new DataClassCountParameters($this->application->getComplexCondition())
        );
        if ($count == 1)
        {
            $moveup_allowed = false;
            $movedown_allowed = false;
        }
        else
        {
            if ($cloi->get_display_order() == 1)
            {
                $moveup_allowed = false;
            }
            else
            {
                if ($cloi->get_display_order() == $count)
                {
                    $movedown_allowed = false;
                }
            }
        }

        return ['moveup' => $moveup_allowed, 'movedown' => $movedown_allowed];
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([
                    Manager::PARAM_ACTION => Manager::ACTION_COPY_COMPLEX_CONTENT_OBJECT_ITEM
                ]), $translator->trans('CopySelected', [], StringUtilities::LIBRARIES)
            ), true
        );

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([
                    Manager::PARAM_ACTION => Manager::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM
                ]), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            ), true
        );

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([
                    Manager::PARAM_ACTION => Manager::ACTION_CHANGE_PARENT
                ]), $translator->trans('MoveSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        return $actions;
    }

    protected function getTitleLink($complexContentObjectItem)
    {
        return $this->application->get_complex_content_object_item_edit_url($complexContentObjectItem->getId());
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $typeGlyph = new FontAwesomeGlyph('folder', [], $translator->trans('Type', [], Manager::CONTEXT));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE, $typeGlyph->render()));

        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE, null, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION, null, false)
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_WEIGHT, $translator->trans('Weight', [], Manager::CONTEXT))
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
     * @param \Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem $complexContentObjectItem
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $complexContentObjectItem
    ): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $content_object = $complexContentObjectItem->get_ref_object();

        switch ($column->get_name())
        {
            case self::PROPERTY_TYPE :
                return $content_object->get_icon_image(IdentGlyph::SIZE_MINI);

            case ContentObject::PROPERTY_TITLE :
                $title = htmlspecialchars($content_object->get_title());
                $title_short = $title;
                $title_short = StringUtilities::getInstance()->truncate($title_short, 53, false);

                if ($content_object instanceof ComplexContentObjectSupport)
                {
                    $title_short = '<a href="' . $urlGenerator->fromRequest(
                            [
                                Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complexContentObjectItem->get_id()
                            ]
                        ) . '">' . $title_short . '</a>';
                }
                else
                {
                    $title_short =
                        '<a href="' . $this->getTitleLink($complexContentObjectItem) . '">' . $title_short . '</a>';
                }

                return $title_short;
            case ContentObject::PROPERTY_DESCRIPTION :
                $description = $content_object->get_description();

                return StringUtilities::getInstance()->truncate($description, 75);
            case self::PROPERTY_WEIGHT :
                return $complexContentObjectItem->get_weight();
        }

        return parent::renderCell($column, $resultPosition, $complexContentObjectItem);
    }

    /**
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $complexContentObjectItem): string
    {
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->application->get_complex_content_object_item_edit_url($complexContentObjectItem->get_id()),
                ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('CopyEdit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('copy'),
                $this->application->get_complex_content_object_item_copy_url($complexContentObjectItem->get_id()),
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $this->application->get_complex_content_object_item_delete_url($complexContentObjectItem->get_id()),
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('ChangeParent', [], StringUtilities::LIBRARIES),
                new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas'),
                $this->application->get_complex_content_object_parent_changer_url($complexContentObjectItem->get_id()),
                ToolbarItem::DISPLAY_ICON
            )
        );

        $allowed = $this->check_move_allowed($complexContentObjectItem);

        if ($allowed['moveup'])
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUp', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-up'),
                    $this->application->get_complex_content_object_item_move_url(
                        $complexContentObjectItem->get_id(), \Chamilo\Core\Repository\Manager::PARAM_DIRECTION_UP
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUpNotAvailable', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($allowed['movedown'])
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDown', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-down'),
                    $this->application->get_complex_content_object_item_move_url(
                        $complexContentObjectItem->get_id(), \Chamilo\Core\Repository\Manager::PARAM_DIRECTION_DOWN
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDownNotAvailable', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
