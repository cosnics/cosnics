<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\Type\Html;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
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
        
        $parameters = $this->get_context()->get_context()->get_parameters();
        $parameters[Manager::PARAM_BLOCK_ID] = $this->get_block()->get_id();
        $parameters[Manager::PARAM_VIEWS] = $this->get_view();
        
        $headers = [];
        
        if ($reporting_data->is_categories_visible())
        {
            $headers[] = new StaticTableColumn(); // $this->th_attributes, $this->td_attributes);
        }
        
        foreach ($reporting_data->get_rows() as $row)
        {
            $headers[] = new SortableStaticTableColumn($row, $row); // , true, $this->th_attributes,
                                                                    // $this->td_attributes);
        }
        
        $table = new SortableTableFromArray(
            $this->convert_reporting_data($reporting_data), 
            $headers, 
            $parameters, 
            $this->column, 
            20, 
            $this->direction, 
            ClassnameUtilities::getInstance()->getClassnameFromObject($block, true));
        
        return $table->toHtml();
    }

    public function convert_reporting_data($data)
    {
        $table_data = [];
        foreach ($data->get_categories() as $category_id => $category_name)
        {
            $category_array = [];
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
