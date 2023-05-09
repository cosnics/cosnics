<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Table;

use Chamilo\Application\Weblcms\Storage\DataClass\CourseSection;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Application\Weblcms\Tool\Manager as ToolManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseSectionsTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_COURSE_SECTION_ID;

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([ToolManager::PARAM_ACTION => Manager::ACTION_REMOVE_COURSE_SECTION]),
                $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $this->addColumn(new DataClassPropertyTableColumn(CourseSection::class, CourseSection::PROPERTY_NAME));
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseSection $courseSection
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $courseSection): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $filter = [
            CourseSection::TYPE_DISABLED,
            CourseSection::TYPE_TOOL,
            CourseSection::TYPE_LINK,
            CourseSection::TYPE_ADMIN
        ];

        if (!in_array($courseSection->getType(), $filter))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_UPDATE_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $courseSection->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_REMOVE_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $courseSection->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON, true
                )
            );

            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('SelectTools', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas'),
                    $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_SELECT_TOOLS_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $courseSection->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('EditNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('pencil-alt', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('SelectToolsNA'),
                    new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal', 'text-muted'], null, 'fas'), null,
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $order = $courseSection->get_display_order();

        if ($order == 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUpNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUp', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-up'),
                    $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_MOVE_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $courseSection->getId(),
                            Manager::PARAM_DIRECTION => - 1
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($resultPosition->isLast())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDownNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-down', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDown', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-down'),
                    $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_MOVE_COURSE_SECTION,
                            Manager::PARAM_COURSE_SECTION_ID => $courseSection->getId(),
                            Manager::PARAM_DIRECTION => 1
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($courseSection->getType() != CourseSection::TYPE_ADMIN)
        {
            if ($courseSection->is_visible())
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('ChangeVisible', [], Manager::CONTEXT), new FontAwesomeGlyph('eye'),
                        $urlGenerator->fromRequest(
                            [
                                ToolManager::PARAM_ACTION => Manager::ACTION_CHANGE_COURSE_SECTION_VISIBILITY,
                                Manager::PARAM_COURSE_SECTION_ID => $courseSection->getId()
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('ChangeVisible', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('eye', ['text-muted']), $urlGenerator->fromRequest(
                        [
                            ToolManager::PARAM_ACTION => Manager::ACTION_CHANGE_COURSE_SECTION_VISIBILITY,
                            Manager::PARAM_COURSE_SECTION_ID => $courseSection->getId()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        return $toolbar->render();
    }
}
