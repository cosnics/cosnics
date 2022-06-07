<?php
namespace Chamilo\Application\Weblcms\Table\AdminRequest;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package application.lib.weblcms.weblcms_manager.component.admin_request_browser
 */

/**
 * Cell rendere for the learning object browser table
 */
class AdminRequestTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    // Inherited
    public function get_actions($request)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        return $toolbar->as_html();
    }

    public function render_cell($column, $request)
    {

        // Add special features here
        switch ($column->get_name())
        {

            case CommonRequest::PROPERTY_MOTIVATION :
                $motivation = strip_tags(parent::render_cell($column, $request));
                if (strlen($motivation) > 175)
                {
                    $motivation = mb_substr($motivation, 0, 200) . '&hellip;';
                }

                return $motivation;
            case AdminRequestTableColumnModel::USER_NAME :
                return \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                    User::class, $request->get_user_id()
                )->get_fullname();

            case AdminRequestTableColumnModel::COURSE_NAME :
                return DataManager::retrieve_by_id(Course::class, $request->get_course_id())->get_title();
            case CommonRequest::PROPERTY_SUBJECT :
                return $request->get_subject();

            case CommonRequest::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::getInstance()->formatLocaleDate(null, $request->get_creation_date());

            case CommonRequest::PROPERTY_DECISION_DATE :
                if ($request->get_decision_date() != null)
                {
                    return DatetimeUtilities::getInstance()->formatLocaleDate(null, $request->get_decision_date());
                }
                else
                {
                    return $request->get_decision_date();
                }
        }

        return parent::render_cell($column, $request);
    }
}
