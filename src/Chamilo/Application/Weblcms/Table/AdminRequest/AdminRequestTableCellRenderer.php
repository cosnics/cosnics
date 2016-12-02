<?php
namespace Chamilo\Application\Weblcms\Table\AdminRequest;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CommonRequest;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * $Id: admin_request_browser_table_cell_renderer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * 
 * @package application.lib.weblcms.weblcms_manager.component.admin_request_browser
 */
/**
 * Cell rendere for the learning object browser table
 */
class AdminRequestTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     * Constructor
     * 
     * @param $browser WeblcmsBrowserComponent
     */
    /*
     * function __construct($browser) { parent :: __construct($browser); }
     */
    
    // Inherited
    public function render_cell($column, $request)
    {
        // if ($column === AdminRequestTableColumnModel :: get_modification_column())
        // {
        // return $this->get_modification_links($request);
        // }
        
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
                    \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                    $request->get_user_id())->get_fullname();
            
            case AdminRequestTableColumnModel::COURSE_NAME :
                return DataManager::retrieve_by_id(Course::class_name(), $request->get_course_id())->get_title();
            case CommonRequest::PROPERTY_SUBJECT :
                return $request->get_subject();
            
            case CommonRequest::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::format_locale_date(null, $request->get_creation_date());
            
            case CommonRequest::PROPERTY_DECISION_DATE :
                if ($request->get_decision_date() != null)
                {
                    return DatetimeUtilities::format_locale_date(null, $request->get_decision_date());
                }
                else
                {
                    return $request->get_decision_date();
                }
        }
        return parent::render_cell($column, $request);
    }

    public function get_actions($request)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        
        // $check_item = $request->get_decision();
        //
        // if ($check_item != CommonRequest :: ALLOWED_DECISION)
        // {
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('Accept', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/Confirm'),
        // $this->get_component()->get_course_request_allowing_url(
        // $request,
        // $this->get_component()->get_request_type(),
        // $this->get_component()->get_request_view()),
        // ToolbarItem :: DISPLAY_ICON));
        // }
        // if ($check_item == CommonRequest :: NO_DECISION)
        // {
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('Reject', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/Refuse'),
        // $this->get_component()->get_course_request_refuse_url(
        // $request,
        // $this->get_component()->get_request_type(),
        // $this->get_component()->get_request_view()),
        // ToolbarItem :: DISPLAY_ICON));
        // }
        //
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/Ddelete'),
        // $this->get_component()->get_course_request_deleting_url(
        // $request,
        // $this->get_component()->get_request_type(),
        // $this->get_component()->get_request_view()),
        // ToolbarItem :: DISPLAY_ICON,
        // true));
        //
        // $toolbar->add_item(
        // new ToolbarItem(
        // Translation :: get('View', null, Utilities :: COMMON_LIBRARIES),
        // Theme :: getInstance()->getCommonImagePath('Action/View'),
        // $this->get_component()->get_course_request_viewing_url(
        // $request,
        // $this->get_component()->get_request_type(),
        // $this->get_component()->get_request_view()),
        // ToolbarItem :: DISPLAY_ICON));
        
        return $toolbar->as_html();
    }
}
