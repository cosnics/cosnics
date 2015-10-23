<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Component\CourseSections;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseSections\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableFormActionsSupport;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: course_sections_browser_table.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.course_sections.component.course_sections_browser
 */
/**
 * Table to display a set of courses.
 */
class CourseSectionsTable extends DataClassTable implements TableFormActionsSupport
{
    const TABLE_IDENTIFIER = Manager :: PARAM_COURSE_SECTION_ID;

    public function get_implemented_form_actions()
    {
        $actions = new TableFormActions(__NAMESPACE__);
        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(
                    array(Manager :: PARAM_ACTION => Manager :: ACTION_REMOVE_COURSE_SECTION)),
                Translation :: get('RemoveSelected')));
        return $actions;
    }
}
