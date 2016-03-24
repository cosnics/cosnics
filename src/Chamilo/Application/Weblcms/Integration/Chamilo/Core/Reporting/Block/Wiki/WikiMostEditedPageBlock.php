<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class WikiMostEditedPageBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('MostEditedPage'), Translation :: get('NumberOfEdits')));

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
            $most_edits = 0;
            $most_edited_page = null;

            foreach ($complex_content_object_items as $complex_content_object_item)
            {
                $page_edits = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                    ContentObject :: class_name(),
                    $complex_content_object_item->get_ref())->get_version_count();

                if ($page_edits >= $most_edits)
                {
                    $most_edits = $page_edits;
                    $most_edited_page = $complex_content_object_item;
                }
            }

            $url = 'index.php?go=' . \Chamilo\Application\Weblcms\Manager :: ACTION_VIEW_COURSE . '&course=' .
                 $this->get_course_id() . '&tool=' . $this->get_tool() . '&application=weblcms&' .
                 \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID . '=' . $this->get_publication_id() .
                 '&tool_action=' . \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_VIEW .
                 '&display_action=view_item&selected_cloi=' . $most_edited_page->get_id();

            $reporting_data->add_category(0);
            $reporting_data->add_data_category_row(
                0,
                Translation :: get('MostEditedPage'),
                '<a href="' . $url . '">' . $most_edited_page->get_ref_object()->get_title() . '</a>');
            $reporting_data->add_data_category_row(0, Translation :: get('NumberOfEdits'), $most_edits);
            $reporting_data->hide_categories();
        }

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
