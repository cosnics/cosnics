<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Integration\Chamilo\Core\Reporting\Block;

use Chamilo\Core\Reporting\ReportingBlock;
use Chamilo\Core\Reporting\ReportingData;

class PageBlock extends ReportingBlock
{

    public function count_data()
    {
        $pages = $this->get_parent()->get_pages();
        
        $reporting_data = new ReportingData();
        
        // creating actual reporing data
        foreach ($pages as $page)
        {
            $reporting_data->add_category($this->get_parent()->get_page_template_url($page));
        }
        
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
