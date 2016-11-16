<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\ReportingData;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

/**
 *
 * @package core\reporting\viewer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PropertiesTable extends Html
{
    const VIEW = 'properties_table';

    public function get_content()
    {
        $block = $this->get_block();
        $reporting_data = $block->get_data();
        
        $table = new \Chamilo\Libraries\Format\Table\PropertiesTable($this->convert_reporting_data($reporting_data));
        return $table->toHtml();
    }

    public function convert_reporting_data(ReportingData $data)
    {
        $properties = array();
        
        foreach ($data->get_rows() as $row_id => $row)
        {
            $properties[$row] = array();
            
            foreach ($data->get_categories() as $category_id => $category_name)
            {
                $properties[$row][] = $data->get_data_category_row($category_id, $row_id);
            }
        }
        
        return $properties;
    }
}
