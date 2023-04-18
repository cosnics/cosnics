<?php
namespace Chamilo\Application\Weblcms\Table\Publication\Table;

use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Rights\Entities\CourseGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CoursePlatformGroupEntity;
use Chamilo\Application\Weblcms\Rights\Entities\CourseUserEntity;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager as CourseGroupDataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * Cell renderer for the object publication table
 * 
 * @package application.weblcms
 * @author Original Author Unknown
 * @author Sven Vanpoucke - Hogeschool Gent - Refactoring to record table
 */
class ObjectPublicationTableCellRenderer extends RecordTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders a cell for a given object
     * 
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed $publication
     *
     * @return String
     */
    public function render_cell($column, $publication)
    {
        $content_object = $this->get_component()->get_content_object_from_publication($publication);
        
        switch ($column->get_name())
        {
            case ObjectPublicationTableColumnModel::COLUMN_STATUS :
                $title = Translation::get('TypeName', array(), $content_object->context());
                
                $icon_suffix = '';
                if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
                {
                    $icon_suffix = 'Na';
                    $title .= ' ' . Translation::get('NotAvailable') . ')';
                }
                else
                {
                    $last_visit_date = $this->get_component()->get_tool_browser()->get_last_visit_date();
                    if ($publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE] >= $last_visit_date)
                    {
                        $icon_suffix = 'New';
                        $title .= ' (' . Translation::get('New') . ')';
                    }
                }
                return '<img title="' . htmlentities($title) . '" src="' .
                     $content_object->get_icon_path(Theme::ICON_MINI . $icon_suffix) . '" />';
                
                break;
            case ContentObject::PROPERTY_TITLE :
                if ($content_object instanceof ComplexContentObjectSupport)
                {
                    $details_url = $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID], 
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT));
                    
                    return '<a href="' . $details_url . '">' . parent::render_cell($column, $publication) . '</a>';
                }
                
                $details_url = $this->get_component()->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID], 
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW));
                
                return '<a href="' . $details_url . '">' . parent::render_cell($column, $publication) . '</a>';
                
                break;
            case ContentObjectPublication::PROPERTY_PUBLICATION_DATE :
                $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
                $data = DatetimeUtilities::format_locale_date(
                    $date_format, 
                    $publication[ContentObjectPublication::PROPERTY_PUBLICATION_DATE]);
                break;
            case ContentObjectPublication::PROPERTY_MODIFIED_DATE :
                $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
                $data = DatetimeUtilities::format_locale_date(
                    $date_format, 
                    $publication[ContentObjectPublication::CONTENT_OBJECT_MODIFICATION_DATE_ALIAS]);
                break;
            case ContentObjectPublication::PROPERTY_PUBLISHER_ID :
                $user = $this->retrieve_user($publication[ContentObjectPublication::PROPERTY_PUBLISHER_ID]);
                if (! $user)
                {
                    $data = '<i>' . Translation::get('UserUnknown') . '</i>';
                }
                else
                {
                    $data = $user->get_fullname();
                }
                break;
            case 'published_for' :
                if ($publication[ContentObjectPublication::PROPERTY_EMAIL_SENT])
                {
                    $email_icon = ' - <img src="' . Theme::getInstance()->getCommonImagePath('Action/Email') . '" alt=""
                        style="vertical-align: middle;" title="' .
                         Translation::get('SentByEmail') . '"/>';
                }
                $data = '<div style="float: left;">' . $this->render_publication_targets($publication) . '</div>' .
                     $email_icon;
                break;
            case ContentObject::PROPERTY_DESCRIPTION :
                $data = $publication[ContentObject::PROPERTY_DESCRIPTION];
                $data = StringUtilities::getInstance()->truncate($data, 100);
        }
        
        if ($data)
        {
            if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
            {
                return '<span style="color: gray">' . $data . '</span>';
            }
            else
            {
                return $data;
            }
        }
        
        return parent::render_cell($column, $publication);
    }

    /**
     * Returns the actions toolbar quick-win table ordering: passes the table direction to the get_publication_actions
     * method (default ascending).
     * 
     * @param mixed $publication
     *
     * @return string
     */
    public function get_actions($publication)
    {
        $table = $this->get_table();
        $column_model = $table->get_column_model();
        return $this->get_component()->get_publication_actions_dropdown(
            $publication, 
            $column_model->is_display_order_column(), 
            $column_model->get_default_order_direction() == SORT_ASC,
            $this->get_table()->getTableFilterParameters()
        );
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders the publication targets
     * 
     * @param mixed$publication
     * @return string
     */
    public function render_publication_targets($publication)
    {
        try
        {
            $target_entities = WeblcmsRights::getInstance()->get_target_entities(
                WeblcmsRights::VIEW_RIGHT, 
                Manager::context(), 
                $publication[ContentObjectPublication::PROPERTY_ID], 
                WeblcmsRights::TYPE_PUBLICATION, 
                $this->get_component()->get_tool_browser()->get_course_id(), 
                WeblcmsRights::TREE_TYPE_COURSE);
        }
        catch (Exception $exception)
        {
            error_log($exception->getMessage());
            $target_entities = array();
        }
        
        $rdm = \Chamilo\Core\Rights\Storage\DataManager::getInstance();
        
        $target_list = array();
        
        if (is_array($target_entities[0]) && array_key_exists(0, $target_entities[0]))
        {
            $target_list[] = Translation::get('Everybody', null, Utilities::COMMON_LIBRARIES);
        }
        else
        {
            $target_list[] = '<select>';
            
            foreach ($target_entities as $entity_type => $entity_ids)
            {
                switch ($entity_type)
                {
                    case CoursePlatformGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $group_id)
                        {
                            $group = \Chamilo\Core\Group\Storage\DataManager::retrieve_by_id(
                                Group::class_name(), 
                                $group_id);
                            if ($group)
                            {
                                $target_list[] = '<option>' . $group->get_name() . '</option>';
                            }
                        }
                        break;
                    case CourseUserEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $user_id)
                        {
                            $user = $this->retrieve_user($user_id);
                            if ($user)
                            {
                                $target_list[] = '<option>' . $user->get_fullname() . '</option>';
                            }
                        }
                        break;
                    case CourseGroupEntity::ENTITY_TYPE :
                        foreach ($entity_ids as $course_group_id)
                        {
                            $course_group = CourseGroupDataManager::retrieve_by_id(
                                CourseGroup::class_name(), 
                                $course_group_id);
                            
                            if ($course_group)
                            {
                                $target_list[] = '<option>' . $course_group->get_name() . '</option>';
                            }
                        }
                        
                        break;
                    
                    case 0 :
                        $target_list[] = '<option>Everyone</option>';
                        break;
                }
            }
            $target_list[] = '</select>';
        }
        
        return implode(PHP_EOL, $target_list);
    }

    /**
     * Retrieves a user by id
     * 
     * @param int $user_id
     *
     * @return \core\user\User
     */
    public function retrieve_user($user_id)
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
            $user_id);
    }
}
