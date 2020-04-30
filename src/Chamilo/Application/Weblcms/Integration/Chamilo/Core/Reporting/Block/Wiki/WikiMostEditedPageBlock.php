<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\Wiki;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Block\ToolBlock;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Manager;
use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class WikiMostEditedPageBlock extends ToolBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation::get('MostEditedPage'), Translation::get('NumberOfEdits')));
        
        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class,
            $this->getPublicationId());
        
        $wiki = $publication->get_content_object();
        $complex_content_object_items = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class,
            new DataClassRetrievesParameters(
                new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class,
                        ComplexContentObjectItem::PROPERTY_PARENT), 
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
                $page_edits = DataManager::retrieve_by_id(
                    ContentObject::class,
                    $complex_content_object_item->get_ref())->get_version_count();
                
                if ($page_edits >= $most_edits)
                {
                    $most_edits = $page_edits;
                    $most_edited_page = $complex_content_object_item;
                }
            }
            
            $url = 'index.php?go=' . \Chamilo\Application\Weblcms\Manager::ACTION_VIEW_COURSE . '&course=' .
                 $this->getCourseId() . '&tool=' . $this->get_tool() . '&application=weblcms&' .
                 Manager::PARAM_PUBLICATION_ID . '=' . $this->getPublicationId() .
                 '&tool_action=' . Manager::ACTION_VIEW .
                 '&display_action=view_item&selected_cloi=' . $most_edited_page->get_id();
            
            $reporting_data->add_category(0);
            $reporting_data->add_data_category_row(
                0, 
                Translation::get('MostEditedPage'), 
                '<a href="' . $url . '">' . $most_edited_page->get_ref_object()->get_title() . '</a>');
            $reporting_data->add_data_category_row(0, Translation::get('NumberOfEdits'), $most_edits);
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
        return array(Html::VIEW_TABLE);
    }
}
