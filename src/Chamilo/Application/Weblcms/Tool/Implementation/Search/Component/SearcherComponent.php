<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Search\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Search\Form\SearchForm;
use Chamilo\Application\Weblcms\Tool\Implementation\Search\Manager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\String\Text;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Search\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SearcherComponent extends Manager
{
    /**
     * Number of results per page
     */
    public const RESULTS_PER_PAGE = 10;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Search\Form\SearchForm
     */
    private $searchForm;

    public function run()
    {
        $html = [];

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

            $course_group_ids = [];

            foreach ($course_groups as $course_group)
            {
                $course_group_ids[] = $course_group->get_id();
            }

            $publications = DataManager::retrieves(
                ContentObjectPublication::class,
                new DataClassRetrievesParameters($this->get_retrieve_publications_condition())
            );

            $tools = [];

            foreach ($publications as $publication)
            {
                if ($this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication) &&
                    (!$publication->is_hidden() || $this->is_allowed(WeblcmsRights::EDIT_RIGHT)))
                {
                    $tools[$publication->get_tool()][] = $publication;
                }
            }

            $results = 0;

            $tabs = new TabsCollection();

            foreach ($tools as $tool => $publications)
            {
                $resultsHtml = [];

                if (strpos($tool, 'feedback') !== false)
                {
                    continue;
                }

                $objects = [];

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
                        'TypeName', null, \Chamilo\Application\Weblcms\Tool\Manager::get_tool_type_namespace($tool)
                    );

                    $results += $count;

                    foreach ($objects as $pub)
                    {
                        $object = $pub->get_content_object();

                        if ($object->getType() != Introduction::class)
                        {
                            $url = $this->get_url(
                                [
                                    \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => $pub->get_tool(),
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $pub->get_id(),
                                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_VIEW
                                ]
                            );
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

                    $tabs->add(new ContentTab($tool, $tabLabel, implode(PHP_EOL, $resultsHtml)));
                }
            }

            $variable = ($results > 1 ? 'ResultsFoundFor' : 'ResultFoundFor');

            $html[] = '<div class="alert alert-info">';
            $html[] = Translation::get($variable, ['COUNT' => $results, 'QUERY' => $query]);
            $html[] = '</div>';

            if ($results > 0)
            {
                $html[] = $this->getTabsRenderer()->render('search', $tabs);
            }
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    // Inherited

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Search\Form\SearchForm
     */
    public function getSearchForm()
    {
        if (!isset($this->searchForm))
        {
            $this->searchForm = new SearchForm(
                $this->get_url(
                    [\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_SEARCH]
                )
            );
        }

        return $this->searchForm;
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    public function get_condition()
    {
        $query = $this->get_query();
        if (!$query)
        {
            $query = $this->getRequest()->request->get('query');
        }

        if (isset($query) && $query != '')
        {
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE), $query
            );
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION), $query
            );

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
     * @return AndCondition
     */
    protected function get_retrieve_publications_condition()
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($this->get_course_id())
        );
        $conditions[] = new NotCondition(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
                ), new StaticConditionVariable('home')
            )
        );

        if (!$this->get_course()->is_course_admin($this->get_user()))
        {
            $from_date_variables = new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_FROM_DATE
            );

            $to_date_variable = new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TO_DATE
            );

            $time_conditions = [];

            $time_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(
                    ContentObjectPublication::class, ContentObjectPublication::PROPERTY_HIDDEN
                ), new StaticConditionVariable(0)
            );

            $forever_conditions = [];

            $forever_conditions[] = new EqualityCondition($from_date_variables, new StaticConditionVariable(0));

            $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));

            $forever_condition = new AndCondition($forever_conditions);

            $between_conditions = [];

            $between_conditions[] = new ComparisonCondition(
                $from_date_variables, ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable(time())
            );

            $between_conditions[] = new ComparisonCondition(
                $to_date_variable, ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(time())
            );

            $between_condition = new AndCondition($between_conditions);

            $time_conditions[] = new OrCondition([$forever_condition, $between_condition]);

            $conditions[] = new AndCondition($time_conditions);
        }

        $condition = new AndCondition($conditions);

        return $condition;
    }
}
