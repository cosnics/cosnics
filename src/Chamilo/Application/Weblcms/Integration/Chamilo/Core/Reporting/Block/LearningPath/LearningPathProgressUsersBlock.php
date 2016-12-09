<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\LearningPathAttempt;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overview of progress of each learning path
 *          per user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class LearningPathProgressUsersBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $course_id = $this->get_course_id();
        $users = CourseDataManager::retrieve_all_course_users($course_id)->as_array();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($course_id));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_TOOL), 
            new StaticConditionVariable(
                ClassnameUtilities::getInstance()->getClassNameFromNamespace(LearningPath::class_name(), true)));
        $condition = new AndCondition($conditions);
        
        $order_by = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_MODIFIED_DATE));
        
        $publication_resultset = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
            $condition, 
            $order_by);
        
        $publications = array();
        $headings = array();
        $headings[] = Translation::get('Name');
        while ($publication = $publication_resultset->next_result())
        {
            $publications[] = $publication;
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
            
            if ($publication_resultset->size() > 5)
            {
                $headings[] = substr($content_object->get_title(), 0, 14);
            }
            else
            {
                $headings[] = $content_object->get_title();
            }
        }
        
        $reporting_data->set_rows($headings);
        
        foreach ($users as $key => $user)
        {
            $reporting_data->add_category($key);
            $reporting_data->add_data_category_row(
                $key, 
                Translation::get('Name'), 
                \Chamilo\Core\User\Storage\DataClass\User::fullname(
                    $user[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_FIRSTNAME], 
                    $user[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_LASTNAME]));
            
            foreach ($publications as $publication)
            {
                $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ContentObject::class_name(), 
                    $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
                
                if ($publication_resultset->size() > 5)
                {
                    $title = substr($content_object->get_title(), 0, 14);
                }
                else
                {
                    $title = $content_object->get_title();
                }
                
                $conditions = array();
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        LearningPathAttempt::class_name(), 
                        LearningPathAttempt::PROPERTY_LEARNING_PATH_ID), 
                    new StaticConditionVariable($publication[ContentObjectPublication::PROPERTY_ID]));
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        LearningPathAttempt::class_name(), 
                        LearningPathAttempt::PROPERTY_USER_ID), 
                    new StaticConditionVariable($user[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_ID]));
                $condition = new AndCondition($conditions);
                
                $attempt = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve(
                    LearningPathAttempt::class_name(), 
                    new DataClassRetrieveParameters($condition));
                
                if (! $attempt instanceof LearningPathAttempt)
                {
                    if (\Chamilo\Application\Weblcms\Storage\DataManager::is_publication_target_user(
                        $user[\Chamilo\Core\User\Storage\DataClass\User::PROPERTY_ID], 
                        $publication[ContentObjectPublication::PROPERTY_ID], 
                        $course_id))
                    {
                        $reporting_data->add_data_category_row($key, $title, null);
                        continue;
                    }
                    
                    $reporting_data->add_data_category_row($key, $title, 'X');
                    continue;
                }
                
                $progress = $attempt->get_progress();
                
                switch ($progress)
                {
                    case 0 :
                        $progress = '<span style="color:red">' . $progress . '%</span>';
                        break;
                    case 100 :
                        $progress = '<span style="color:green">' . $progress . '%</span>';
                        break;
                    default :
                        $progress = '<span style="color:orange">' . $progress . '%</span>';
                        break;
                }
                
                $reporting_data->add_data_category_row($key, $title, $progress);
            }
        }
        $reporting_data->hide_categories();
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html::VIEW_TABLE);
    }
}
