<?php
namespace Chamilo\Application\Weblcms\CourseType\Table;

use Chamilo\Application\Weblcms\CourseType\Manager;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Application\Weblcms\CourseType\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableParameterValues;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Application\Weblcms\CourseType\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseTypeTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const DEFAULT_ORDER_COLUMN_INDEX = 3;

    public const TABLE_IDENTIFIER = Manager::PARAM_COURSE_TYPE_ID;

    /**
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    protected Application $application;

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([Manager::PARAM_ACTION => Manager::ACTION_DELETE]),
                $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([Manager::PARAM_ACTION => Manager::ACTION_ACTIVATE]),
                $translator->trans('ActivateSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromRequest([Manager::PARAM_ACTION => Manager::ACTION_DEACTIVATE]),
                $translator->trans('DeactivateSelected', [], StringUtilities::LIBRARIES), false
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseType::class, CourseType::PROPERTY_TITLE, null, false
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseType::class, CourseType::PROPERTY_DESCRIPTION, null, false
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseType::class, CourseType::PROPERTY_ACTIVE, null, false
            )
        );
        $this->addColumn(
            $this->getDataClassPropertyTableColumnFactory()->getColumn(
                CourseType::class, CourseType::PROPERTY_DISPLAY_ORDER, null, false
            )
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @deprecated Temporary solution to allow rendering of DI-based tables in a non-DI context
     */
    public function legacyRender(
        Application $application, TableParameterValues $parameterValues, ArrayCollection $tableData,
        ?string $tableName = null
    ): string
    {
        $this->application = $application;

        return parent::render($parameterValues, $tableData, $tableName);
    }

    /**
     * @param \Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType $courseType
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $courseType): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case CourseType::PROPERTY_TITLE :
                $name = parent::renderCell($column, $resultPosition, $courseType);
                $name_short = $name;

                if (strlen($name_short) > 53)
                {
                    $name_short = mb_substr($name_short, 0, 50) . '&hellip;';
                }

                return '<a href="' . $this->application->get_view_course_type_url($courseType->getId()) . '" title="' .
                    htmlentities($name) . '">' . $name_short . '</a>';

            case CourseType::PROPERTY_DESCRIPTION :
                $description = strip_tags(parent::renderCell($column, $resultPosition, $courseType));

                if (strlen($description) > 175)
                {
                    $description = mb_substr($description, 0, 170) . '&hellip;';
                }

                return $description;

            case CourseType::PROPERTY_ACTIVE :

                if ($courseType->is_active())
                {
                    $glyph = new FontAwesomeGlyph(
                        'circle', ['text-success'], $translator->trans('ConfirmTrue', [], StringUtilities::LIBRARIES),
                        'fas'
                    );
                }
                else
                {
                    $glyph = new FontAwesomeGlyph(
                        'circle', ['text-danger'], $translator->trans('ConfirmFalse', [], StringUtilities::LIBRARIES),
                        'fas'
                    );
                }

                return $glyph->render();
        }

        return parent::renderCell($column, $resultPosition, $courseType);
    }

    /**
     * @param \Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType $courseType
     *
     * @throws \ReflectionException
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $courseType): string
    {
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        if ($courseType->is_active())
        {
            $activation_translation = $translator->trans('Deactivate', [], StringUtilities::LIBRARIES);
            $glyph = new FontAwesomeGlyph('eye');
        }
        else
        {
            $activation_translation = $translator->trans('Activate', [], StringUtilities::LIBRARIES);
            $glyph = new FontAwesomeGlyph('eye', ['text-muted']);
        }

        $toolbar->add_item(
            new ToolbarItem(
                $activation_translation, $glyph,
                $this->application->get_change_course_type_activation_url($courseType->getId()),
                ToolbarItem::DISPLAY_ICON
            )
        );

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $this->application->get_update_course_type_url($courseType->getId()), ToolbarItem::DISPLAY_ICON
            )
        );

        if (!DataManager::has_course_type_courses($courseType->getId()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->application->get_delete_course_type_url($courseType->getId()), ToolbarItem::DISPLAY_ICON,
                    true
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('DeleteNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($resultPosition->getPosition() > 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUp', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-up'),
                    $this->application->get_move_course_type_url(
                        $courseType->getId(), Manager::MOVE_DIRECTION_UP
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUpNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (!$resultPosition->isLast())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDown', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-down'),
                    $this->application->get_move_course_type_url(
                        $courseType->getId(), Manager::MOVE_DIRECTION_DOWN
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDownNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-down', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }
}
