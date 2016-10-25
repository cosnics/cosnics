<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class WikiMostVisitedPageBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('MostVisitedPage'), Translation :: get('NumberOfVisits')));

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_by_id(
            ContentObjectPublication :: class_name(),
            $this->get_publication_id());
        $wiki = $publication->get_content_object();

        $complex_content_object_items = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            new DataClassRetrievesParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem :: class_name(),
                        ComplexContentObjectItem :: PROPERTY_PARENT),
                    new StaticConditionVariable($wiki->get_id()))))->as_array();

        if (empty($complex_content_object_items))
        {
            return $reporting_data;
        }
        else
        {
            $most_visits = 0;
            $most_visited_page = null;

            foreach ($complex_content_object_items as $complex_content_object_item)
            {
                $conditions = array();

                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: class_name(),
                        \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: PROPERTY_LOCATION),
                    '*publication=' . $this->get_publication_id() . '*');

                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: class_name(),
                        \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: PROPERTY_LOCATION),
                    '*display_action=view_item*');

                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: class_name(),
                        \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: PROPERTY_LOCATION),
                    '*application=weblcms*');

                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: class_name(),
                        \Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit :: PROPERTY_LOCATION),
                    '*selected_cloi=' . $complex_content_object_item->get_id() . '*');

                $condition = new AndCondition($conditions);

                $items = DataManager :: retrieves(Visit :: class_name(), new DataClassRetrievesParameters($condition));

                if (count($items) >= $most_visits)
                {
                    $most_visits = count($items);
                    $most_visited_page = $complex_content_object_item;
                }
            }
        }

        $url = 'index.php?go=' . \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE . '&course=' .
             $this->get_course_id() . '&tool=' . $this->get_tool() . '&application=weblcms&' .
             \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID . '=' . $this->get_publication_id() .
             '&tool_action=' . \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW .
             '&display_action=view_item&selected_cloi=' . $most_visited_page->get_id();

        $reporting_data->add_category(0);
        $reporting_data->add_data_category_row(
            0,
            Translation :: get('MostVisitedPage'),
            '<a href="' . $url . '">' . $most_visited_page->get_ref_object()->get_title() . '</a>');

        $reporting_data->add_data_category_row(0, Translation :: get('NumberOfVisits'), $most_visits);
        $reporting_data->hide_categories();

        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_views()
    {
        return array(\Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html :: VIEW_TABLE);
    }
}
