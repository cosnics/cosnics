<?php
namespace Chamilo\Application\Weblcms\Course\Table;

use Chamilo\Application\Weblcms\Course\Manager;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\CourseType\Storage\DataClass\CourseType;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Application\Weblcms\Course\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CourseTableRenderer extends RecordListTableRenderer implements TableRowActionsSupport, TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_COURSE_ID;

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromParameters(
                    [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DELETE]
                ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_VISUAL_CODE));
        $this->addColumn(new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_TITLE));

        $this->addColumn(
            new DataClassPropertyTableColumn(Course::class, Course::PROPERTY_TITULAR_ID, null, false)
        );

        $this->addColumn(
            new DataClassPropertyTableColumn(
                CourseType::class, CourseType::PROPERTY_TITLE, $translator->trans('CourseType', [], Manager::CONTEXT)
            )
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $course): string
    {
        $translator = $this->getTranslator();

        if ($column instanceof DataClassPropertyTableColumn)
        {
            switch ($column->get_class_name())
            {
                case Course::class :
                {
                    switch ($column->get_name())
                    {
                        case Course::PROPERTY_TITLE :
                            return parent::renderCell($column, $resultPosition, $course);
                        case Course::PROPERTY_TITULAR_ID :
                            return DataManager::get_fullname_from_user(
                                $course[Course::PROPERTY_TITULAR_ID],
                                $translator->trans('TitularUnknown', [], Manager::CONTEXT)
                            );
                    }
                    break;
                }
                case CourseType::class :
                {
                    if ($column->get_name() == CourseType::PROPERTY_TITLE)
                    {
                        $course_type_title = $course[Course::PROPERTY_COURSE_TYPE_TITLE];

                        return !$course_type_title ? $translator->trans('NoCourseType', [], Manager::CONTEXT) :
                            $course_type_title;
                    }
                }
            }
        }

        return parent::renderCell($column, $resultPosition, $course);
    }

    public function renderTableRowActions(TableResultPosition $resultPosition, $course): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $viewUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => \Chamilo\Application\Weblcms\Manager::CONTEXT,
            Application::PARAM_ACTION => \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE,
            \Chamilo\Application\Weblcms\Manager::PARAM_COURSE => $course[DataClass::PROPERTY_ID]
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('ViewCourseHome', [], Manager::CONTEXT), new FontAwesomeGlyph('home'), $viewUrl,
                ToolbarItem::DISPLAY_ICON
            )
        );

        $updateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_UPDATE,
            Manager::PARAM_COURSE_ID => $course[DataClass::PROPERTY_ID]
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $updateUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $deleteUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Application::PARAM_ACTION => Manager::ACTION_DELETE,
            Manager::PARAM_COURSE_ID => $course[DataClass::PROPERTY_ID]
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
