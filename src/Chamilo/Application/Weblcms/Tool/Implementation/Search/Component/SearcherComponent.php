<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Search\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Search\Form\SearchForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Search\Manager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\String\Text;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Search\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SearcherComponent extends Manager
{
    /**
     * Number of results per page
     */
    const RESULTS_PER_PAGE = 10;

    /**
     *
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Search\Form\SearchForm
     */
    private $searchForm;

    /**
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Search\Form\SearchForm
     */
    public function getSearchForm()
    {
        if (! isset($this->searchForm))
        {
            $this->searchForm = new SearchForm(
                $this->get_url(
                    array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SEARCH)));
        }
        
        return $this->searchForm;
    }
    
    // Inherited
    public function run()
    {
        $html = array();
        
        $html[] = $this->render_header();
        
        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-body">';
        $html[] = $this->getSearchForm()->render();
        $html[] = '</div>';
        $html[] = '</div>';
        
        // If form validates, show results
        $query = $this->get_query();
        if ($query)
        {
            $course_groups = $this->get_course_groups();
            
            $course_group_ids = array();
            
            foreach ($course_groups as $course_group)
            {
                $course_group_ids[] = $course_group->get_id();
            }
            
            $publications = \Chamilo\Application\Weblcms\Storage\DataManager::retrieves(
                ContentObjectPublication::class_name(), 
                new DataClassRetrievesParameters($this->get_retrieve_publications_condition()));
            
            $tools = array();
            
            while ($publication = $publications->next_result())
            {
                if ($this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication) &&
                     (! $publication->is_hidden() || $this->is_allowed(WeblcmsRights::EDIT_RIGHT)))
                {
                    $tools[$publication->get_tool()][] = $publication;
                }
            }
            
            $results = 0;
            
            $tabs = new DynamicTabsRenderer('search');
            
            foreach ($tools as $tool => $publications)
            {
                $resultsHtml = array();
                
                if (strpos($tool, 'feedback') !== false)
                {
                    continue;
                }
                
                $objects = array();
                
                foreach ($publications as $publication)
                {
                    $lo = $publication->get_content_object();
                    $lo_title = $lo->get_title();
                    $lo_description = strip_tags($lo->get_description());
                    
                    if (stripos($lo_title, $query) !== false || stripos($lo_description, $query) !== false)
                    {
                        $objects[] = $publication;
                    }
                }
                
                $count = count($objects);
                
                if ($count > 0)
                {
                    
                    $toolName = Translation::get(
                        'TypeName', 
                        null, 
                        \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($tool));
                    
                    $results += $count;
                    
                    foreach ($objects as $pub)
                    {
                        $object = $pub->get_content_object();
                        
                        if ($object->get_type() != Introduction::class_name())
                        {
                            $url = $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => $pub->get_tool(), 
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $pub->get_id(), 
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW));
                        }
                        else
                        {
                            $url = '#';
                        }
                        
                        $resultsHtml[] = '<div class="panel panel-default">';
                        
                        $resultsHtml[] = '<div class="panel-heading">';
                        $resultsHtml[] = '<h4 class="panel-title">';
                        $resultsHtml[] = '<a href="' . $url . '">' . $object->get_icon_image() . '' .
                             Text::highlight($object->get_title(), $query) . '</a>';
                        $resultsHtml[] = '</h4>';
                        $resultsHtml[] = '</div>';
                        
                        $resultsHtml[] = '<div class="panel-body">';
                        $resultsHtml[] = Text::highlight(strip_tags($object->get_description()), $query);
                        $resultsHtml[] = '</div>';
                        
                        $resultsHtml[] = '</div>';
                    }
                    
                    $tabLabel = $toolName . ' <span class="badge">' . $count . '</span>';
                    
                    $tabs->add_tab(new DynamicContentTab($tool, $tabLabel, null, implode(PHP_EOL, $resultsHtml)));
                }
            }
            
            $variable = ($results > 1 ? 'ResultsFoundFor' : 'ResultFoundFor');
            
            $html[] = '<div class="alert alert-info">';
            $html[] = Translation::get($variable, array('COUNT' => $results, 'QUERY' => $query));
            $html[] = '</div>';
            
            if ($results > 0)
            {
                $html[] = $tabs->render();
            }
        }
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_condition()
    {
        $query = $this->get_query();
        if (! $query)
        {
            $query = Request::post('query');
        }
        
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TITLE), 
                '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_DESCRIPTION), 
                '*' . $query . '*');
            return new OrCondition($conditions);
        }
        
        return null;
    }

    public function get_query()
    {
        $query = trim($this->getSearchForm()->getQuery());
        if ($query == '')
        {
            return null;
        }
        
        return $query;
    }

    /**
     *
     * @return AndCondition
     */
    protected function get_retrieve_publications_condition()
    {
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_COURSE_ID), 
            new StaticConditionVariable($this->get_course_id()));
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class_name(), 
                    ContentObjectPublication::PROPERTY_TOOL), 
                new StaticConditionVariable('home')));
        
        if (! $this->get_course()->is_course_admin($this->get_user()))
        {
            $from_date_variables = new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_FROM_DATE);
            
            $to_date_variable = new PropertyConditionVariable(
                ContentObjectPublication::class_name(), 
                ContentObjectPublication::PROPERTY_TO_DATE);
            
            $time_conditions = array();
            
            $time_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class_name(), 
                    ContentObjectPublication::PROPERTY_HIDDEN), 
                new StaticConditionVariable(0));
            
            $forever_conditions = array();
            
            $forever_conditions[] = new EqualityCondition($from_date_variables, new StaticConditionVariable(0));
            
            //$forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));
            
            $forever_condition = new AndCondition($forever_conditions);
            
            $between_conditions = array();
            
            $between_conditions[] = new InequalityCondition(
                $from_date_variables, 
                InequalityCondition::LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable(time()));
            
            $between_conditions[] = new InequalityCondition(
                $to_date_variable, 
                InequalityCondition::GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable(time()));
            
            $between_condition = new AndCondition($between_conditions);
            
            $time_conditions[] = new OrCondition(array($forever_condition, $between_condition));
            
            $conditions[] = new AndCondition($time_conditions);
        }
        
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
