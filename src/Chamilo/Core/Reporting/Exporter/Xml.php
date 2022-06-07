<?php
namespace Chamilo\Core\Reporting\Exporter;

use Chamilo\Libraries\File\Export\Export;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class Xml extends Csv
{

    public function export()
    {
        $export = Export::factory('xml', $this->convert_data());
        $export->set_filename($this->get_file_name());
        $export->send_to_browser();
    }

    public function save()
    {
        $file = $this->get_file_name();
        $export = Export::factory('xml', $this->convert_data());
        $export->set_filename($this->get_file_name());
        return $export->render_data();
    }

    public function convert_data()
    {
        $template = $this->get_template();
        $block = $template->get_current_block();
        $data = $block->retrieve_data();
        
        $csv_data = [];
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
                    strtolower(Translation::get('Category', null, StringUtilities::LIBRARIES)))] = $category_name;
            }
            foreach ($data->get_rows() as $row_id => $row_name)
            {
                $category_array[str_replace($placeholders, $replace_by, strtolower($row_name))] = strip_tags(
                    $data->get_data_category_row($category_id, $row_id));
            }
            $csv_data[] = $category_array;
        }
        return $csv_data;
    }
}
