<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Csv;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Csv;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Csv
{

    public function render()
    {
        $data = $this->get_block()->retrieve_data();
        
        $csv_data = array();
        
        foreach ($data->get_categories() as $category_id => $category_name)
        {
            $category_array = array();
            
            if ($data->is_categories_visible())
            {
                $category_array[Translation::get('Category', null, Utilities::COMMON_LIBRARIES)] = $category_name;
            }
            
            foreach ($data->get_rows() as $row_id => $row_name)
            {
                $category_array[$row_name] = strip_tags($data->get_data_category_row($category_id, $row_id));
            }
            
            $csv_data[] = $category_array;
        }
        
        return $csv_data;
    }
}
