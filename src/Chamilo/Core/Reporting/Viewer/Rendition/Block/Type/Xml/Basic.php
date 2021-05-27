<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Xml;

use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Xml;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Xml
{

    public function render()
    {
        $data = $this->get_block()->retrieve_data();
        
        $xml_data = [];
        $placeholders = array(' ', '#');
        $replace_by = array('_', 'no');
        
        foreach ($data->get_categories() as $category_id => $category_name)
        {
            $category_array = [];
            
            if ($data->is_categories_visible())
            {
                $category_array[str_replace(
                    ' ', 
                    '_', 
                    strtolower(Translation::get('Category', null, Utilities::COMMON_LIBRARIES)))] = $category_name;
            }
            
            foreach ($data->get_rows() as $row_id => $row_name)
            {
                $category_array[str_replace($placeholders, $replace_by, strtolower($row_name))] = strip_tags(
                    $data->get_data_category_row($category_id, $row_id));
            }
            
            $xml_data[] = $category_array;
        }
        return $xml_data;
    }
}
