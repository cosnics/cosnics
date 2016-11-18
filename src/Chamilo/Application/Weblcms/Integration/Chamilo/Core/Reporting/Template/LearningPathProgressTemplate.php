<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\LearningPath\LearningPathProgressUsersBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingTemplate;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.templates Reporting template with an overview of the progress of each
 *          learning path per user
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class LearningPathProgressTemplate extends ReportingTemplate
{

    public function __construct($parent)
    {
        parent::__construct($parent);
        
        $this->course_id = Request::get(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE);
        $this->set_parameter(\Chamilo\Application\Weblcms\Manager::PARAM_COURSE, $this->course_id);
        
        $custom_breadcrumbs = array();
        $custom_breadcrumbs[] = new Breadcrumb($this->get_url(), Translation::get('LearningPathProgress'));
        $this->set_custom_breadcrumb_trail($custom_breadcrumbs);
        
        // learning path titles for tooltips
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($this->course_id));
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
        $publications = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_content_object_publications(
            $condition, 
            $order_by);
        
        while ($publication = $publications->next_result())
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]);
            $this->th_titles[] = $content_object->get_title();
        }
        
        $this->add_reporting_block($this->get_learning_path_progress_users());
    }

    public function get_learning_path_progress_users()
    {
        return new LearningPathProgressUsersBlock($this, true);
    }
}
