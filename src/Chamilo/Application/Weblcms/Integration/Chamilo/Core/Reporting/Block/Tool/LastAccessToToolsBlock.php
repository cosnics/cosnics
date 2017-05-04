<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Tool;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssessmentScoresTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssignmentScoresTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\LearningPathProgressTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\ToolPublicationsDetailTemplate;
use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataManager as WeblcmsTrackingDataManager;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package application.weblcms.php.reporting.blocks Reporting block with an overiew of the tools and their access
 *          details
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class LastAccessToToolsBlock extends ToolAccessBlock
{

    public function count_data()
    {
        $reporting_data = parent::count_data();
        
        $reporting_data->add_row(Translation::get('Actions'));
        
        $course_id = $this->get_course_id();
        $user_id = $this->get_user_id();
        
        $tool_names = $reporting_data->get_categories();
        foreach ($tool_names as $tool_name)
        {
            $publications = $this->count_tool_publications($tool_name);
            if ($publications > 0)
            {
                $filter = array(\Chamilo\Core\Reporting\Viewer\Manager::PARAM_BLOCK_ID);
                
                $params = $this->get_parent()->get_parameters();
                $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = ToolPublicationsDetailTemplate::class_name();
                $params[\Chamilo\Application\Weblcms\Manager::PARAM_USERS] = $user_id;
                $params[\Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::PARAM_REPORTING_TOOL] = $tool_name;
                $url = $this->get_parent()->get_url($params, $filter);
                
                $toolbar = new Toolbar();
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('ViewPublications'), 
                        Theme::getInstance()->getCommonImagePath('Action/Reporting'), 
                        $url, 
                        ToolbarItem::DISPLAY_ICON));

                switch ($tool_name)
                {
                    case ClassnameUtilities::getInstance()->getClassNameFromNamespace(Assignment::class_name()) :
                        $params = $this->get_parent()->get_parameters();
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = AssignmentScoresTemplate::class_name();
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
                        
                        $url_detail = $this->get_parent()->get_url($params, $filter);
                        
                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation::get('ViewScores'), 
                                Theme::getInstance()->getCommonImagePath('Action/ViewResults'), 
                                $url_detail, 
                                ToolbarItem::DISPLAY_ICON));
                        
                        break;
                    case ClassnameUtilities::getInstance()->getClassNameFromNamespace(Assessment::class_name()) :
                        $params = $this->get_parent()->get_parameters();
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = AssessmentScoresTemplate::class_name();
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
                        
                        $url_detail = $this->get_parent()->get_url($params, $filter);
                        
                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation::get('ViewScores'), 
                                Theme::getInstance()->getCommonImagePath('Action/ViewResults'), 
                                $url_detail, 
                                ToolbarItem::DISPLAY_ICON));
                        
                        break;
                    case ClassnameUtilities::getInstance()->getClassNameFromNamespace(LearningPath::class_name()) :
                        $params = $this->get_parent()->get_parameters();
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID] = LearningPathProgressTemplate::class_name();
                        $params[\Chamilo\Application\Weblcms\Manager::PARAM_COURSE] = $course_id;
                        
                        $url_detail = $this->get_parent()->get_url($params, $filter);
                        
                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation::get('ViewProgressUsers'), 
                                Theme::getInstance()->getCommonImagePath('Action/ViewResults'), 
                                $url_detail, 
                                ToolbarItem::DISPLAY_ICON));
                        break;
                }
                
                $reporting_data->add_data_category_row($tool_name, Translation::get('Actions'), $toolbar->as_html());
            }
        }
        
        return $reporting_data;
    }

    /**
     * Returns the summary data for this course
     * 
     * @return RecordResultSet
     */
    public function retrieve_course_summary_data()
    {
        return WeblcmsTrackingDataManager::retrieve_tools_access_summary_data($this->get_course_id());
    }

    /**
     * Returns the condition for the tools publication count
     * 
     * @param string $tool_name
     *
     * @return AndCondition
     */
    public function get_tool_publications_condition($tool_name)
    {
        $conditions = array();
        
        $conditions[] = parent::get_tool_publications_condition($tool_name);
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($this->get_course_id()));
        
        return new AndCondition($conditions);
    }
}
