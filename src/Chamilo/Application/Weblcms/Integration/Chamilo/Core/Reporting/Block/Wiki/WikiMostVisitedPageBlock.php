<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\User\Integration\Chamilo\Core\Tracking\Storage\DataClass\Visit;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Repository\DataManager;
use Chamilo\Libraries\Storage\StorageParameters;
use Chamilo\Libraries\Translation\Translation;

class WikiMostVisitedPageBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows([Translation::get('MostVisitedPage'), Translation::get('NumberOfVisits')]);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $this->getPublicationId()
        );
        $wiki = $publication->get_content_object();

        $complex_content_object_items =
            \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                ComplexContentObjectItem::class, new StorageParameters(
                    condition: new EqualityCondition(
                        new PropertyConditionVariable(
                            ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                        ), new StaticConditionVariable($wiki->get_id())
                    )
                )
            );

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
                $conditions = [];

                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(
                        Visit::class, Visit::PROPERTY_LOCATION
                    ), 'publication=' . $this->getPublicationId()
                );

                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(
                        Visit::class, Visit::PROPERTY_LOCATION
                    ), 'display_action=view_item'
                );

                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(
                        Visit::class, Visit::PROPERTY_LOCATION
                    ), 'application=weblcms'
                );

                $conditions[] = new ContainsCondition(
                    new PropertyConditionVariable(
                        Visit::class, Visit::PROPERTY_LOCATION
                    ), 'selected_cloi=' . $complex_content_object_item->get_id()
                );

                $condition = new AndCondition($conditions);

                $items = DataManager::retrieves(Visit::class, new StorageParameters(condition: $condition));

                if (count($items) >= $most_visits)
                {
                    $most_visits = count($items);
                    $most_visited_page = $complex_content_object_item;
                }
            }
        }

        $url = 'index.php?go=' . \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE . '&course=' .
            $this->getCourseId() . '&tool=' . $this->get_tool() . '&application=weblcms&' .
            Manager::PARAM_PUBLICATION_ID . '=' . $this->getPublicationId() . '&tool_action=' . Manager::ACTION_VIEW .
            '&display_action=view_item&selected_cloi=' . $most_visited_page->get_id();

        $reporting_data->add_category(0);
        $reporting_data->add_data_category_row(
            0, Translation::get('MostVisitedPage'),
            '<a href="' . $url . '">' . $most_visited_page->get_ref_object()->get_title() . '</a>'
        );

        $reporting_data->add_data_category_row(0, Translation::get('NumberOfVisits'), $most_visits);
        $reporting_data->hide_categories();

        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function get_views()
    {
        return [Html::VIEW_TABLE];
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }
}
