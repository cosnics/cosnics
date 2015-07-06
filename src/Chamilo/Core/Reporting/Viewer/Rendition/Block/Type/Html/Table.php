<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Table extends Html
{
    const VIEW = 'table';

    public function get_content()
    {
        $block = $this->get_block();
        
        $reporting_data = $block->get_data();
        
        $table = new SortableTableFromArray(
            $this->convert_reporting_data($reporting_data), 
            $this->column, 
            20, 
            ClassnameUtilities :: getInstance()->getClassnameFromObject($block, true), 
            $this->direction);
        
        $parameters = $this->get_context()->get_context()->get_parameters();
        $parameters[Manager :: PARAM_BLOCK_ID] = $this->get_block()->get_id();
        $parameters[Manager :: PARAM_VIEWS] = $this->get_view();
        
        $table->set_additional_parameters($parameters);
        $j = 0;
        
        if ($reporting_data->is_categories_visible())
        {
            $table->set_header(0, '', false, $this->th_attributes, $this->td_attributes);
            $j ++;
        }
        
        foreach ($reporting_data->get_rows() as $row)
        {
            $table->set_header($j, $row, true, $this->th_attributes, $this->td_attributes);
            $j ++;
        }
        return $table->toHTML();
    }

    public function convert_reporting_data($data)
    {
        $table_data = array();
        foreach ($data->get_categories() as $category_id => $category_name)
        {
            $category_array = array();
            if ($data->is_categories_visible())
            {
                $category_array[] = $category_name;
            }
            foreach ($data->get_rows() as $row_id => $row_name)
            {
                $category_array[] = $data->get_data_category_row($category_id, $row_id);
            }
            $table_data[] = $category_array;
        }
        return $table_data;
    }
}
