<?php
namespace Chamilo\Core\Reporting;

use Chamilo\Core\Reporting\ReportingDataStyle;

/**
 * $Id: ReportingData.php
 * For details on (PDF) formatting @see ReportingDataStyle
 */
class ReportingData
{

    private $title;

    private $description;

    private $data = array();

    private $rows = array();

    private $categories = array();

    private $show_categories = true;

    private $show_row_heading = true;

    /**
     *
     * @var array mapping row ID's onto ReportingDataStyle objects.
     */
    private $row_style = array();

    /**
     *
     * @var null or ReportingDataStyle objects.
     */
    private $category_style;

    /**
     *
     * @return the $title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     *
     * @param $title the $title to set
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     *
     * @return the $description
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     *
     * @param $description the $description to set
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    public function get_data()
    {
        return $this->data;
    }

    public function show_categories()
    {
        $this->show_categories = true;
    }

    public function hide_categories()
    {
        $this->show_categories = false;
    }

    public function is_categories_visible()
    {
        return $this->show_categories;
    }

    public function show_row_heading()
    {
        $this->show_row_heading = true;
    }

    public function hide_row_heading()
    {
        $this->show_row_heading = false;
    }

    public function is_row_heading_visible()
    {
        return $this->show_row_heading;
    }

    public function get_data_category_row($category_id, $row_id)
    {
        if (isset($this->data[$category_id]) && isset($this->data[$category_id][$row_id]))
        {
            return $this->data[$category_id][$row_id];
        }
        else
        {
            return null;
        }
    }

    public function get_data_category($category_id)
    {
        if (isset($this->data[$category_id]))
        {
            return $this->data[$category_id];
        }
        else
        {
            return null;
        }
    }

    public function get_data_row($row_id)
    {
        if ($this->get_row($row_id))
        {
            $data = array();
            foreach ($this->get_categories() as $category_id => $category_name)
            {
                $data[] = $this->get_data_category_row($category_id, $row_id);
            }
            return $data;
        }
        else
        {
            return null;
        }
    }

    public function get_rows()
    {
        return $this->rows;
    }

    public function set_rows($rows)
    {
        $this->rows = $rows;
    }

    public function get_categories()
    {
        return $this->categories;
    }

    public function set_categories($categories)
    {
        $this->categories = $categories;
    }

    public function get_category($id)
    {
        return $this->categories[$id];
    }

    public function set_category($id, $value)
    {
        $this->categories[id] = $value;
    }

    public function get_row($id)
    {
        return $this->row[$id];
    }

    public function add_category($value)
    {
        $this->categories[] = $value;
    }

    /**
     *
     * @param $value string Id of added row.
     * @param $style RepostingDataStyle Null or style of given row.
     */
    public function add_row($value, $style = null)
    {
        $this->rows[] = $value;
        
        if ($style)
        {
            $this->set_row_style($value, $style);
        }
    }

    public function add_data_category($category, $value)
    {
        $category_id = array_search($category, $this->categories);
        $this->data[$category_id] = $value;
    }

    public function add_data_row($row, $value)
    {
        $row_id = array_search($row, $this->rows);
        foreach ($this->categories as $category_id => $category)
        {
            if (! is_array($this->data[$category_id]))
            {
                $this->data[$category_id] = array();
            }
            $this->data[$category_id][$row_id] = $value[$category_id];
        }
    }

    public function add_data_category_row($category, $row, $value)
    {
        $category_id = array_search($category, $this->categories);
        $row_id = array_search($row, $this->rows);
        if (! is_array($this->data[$category_id]))
        {
            $this->data[$category_id] = array();
        }
        $this->data[$category_id][$row_id] = $value;
    }

    public function is_empty()
    {
        $data = $this->get_data();
        return empty($data);
    }

    /**
     * Set style of given row.
     * 
     * @param $style string Row ID,
     * @param $style RepostingDataStyle Null or style of given row.
     *        @note $style is cloned before storing it, so that any change to $style will not influence the stored
     *            values.
     */
    public function set_row_style($row, $style)
    {
        $this->row_style[$row] = clone $style;
    }

    /**
     *
     * @param $row string Row ID.
     * @return RepostingDataStyle.
     */
    public function get_row_style($row)
    {
        return $this->row_style[$row];
    }

    /**
     * Sets style of category.
     * 
     * @param $style RepostingDataStyle Style of category.
     */
    public function set_category_style($style)
    {
        $this->category_style = clone $style;
    }

    /**
     *
     * @return RepostingDataStyle.
     */
    public function get_category_style()
    {
        return $this->category_style;
    }

    /**
     * Creates ReportingDataStyle objects for each row and the category.
     * Relative width of style objects: maximum length of (stripped) content of each each row is collected. Row Width
     * are proportional to
     * the content length.
     */
    public function setStyleAutomatically()
    {
        // Do nothing if any of styles is not null.
        if ($this->hasStyle())
        {
            return;
        }
        
        // Collect maximum data width per row.
        $max_row_lengths = $this->getMaxRowDataLengths();
        
        // Relative width = Normalized max row lenght.
        $sum_of_max_lengths = array_sum($max_row_lengths);
        $relative_widths = array_map(function ($item) use ($sum_of_max_lengths)
        {
            return $item / $sum_of_max_lengths;
        }, $max_row_lengths);
        // Avoid zero widths by clipping relative widths to a minimum.
        $min_relative_width = floatval(
            \Chamilo\Configuration\Configuration::get('Chamilo\Core\Reporting', 'min_relative_width'));
        $relative_widths = array_map(
            function ($item) use ($min_relative_width)
            {
                return max($item, $min_relative_width);
            }, 
            $relative_widths);
        // Normalize again after clipping.
        $sum_of_relative_widths = array_sum($relative_widths);
        $relative_widths = array_map(
            function ($item) use ($sum_of_relative_widths)
            {
                return $item / $sum_of_relative_widths;
            }, 
            $relative_widths);
        
        // Set relative width per row.
        foreach ($relative_widths as $row_name => $relative_width)
        {
            $style = new ReportingDataStyle();
            $style->setRelativeWidth($relative_width);
            
            if ($row_name == "#CATEGORY#")
            {
                $this->set_category_style($style);
            }
            else
            {
                $this->set_row_style($row_name, $style);
            }
        }
    }

    /**
     *
     * @return True of either category or any of the row styles is not null..
     */
    public function hasStyle()
    {
        // Do nothing if any of styles is not null.
        if (! is_null($this->get_category_style()))
        {
            return true;
        }
        
        foreach ($this->get_rows() as $row_index => $row_name)
        {
            if (! is_null($this->get_row_style($row_name)))
            {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Returns an array of maximum data lengths per row.
     * For each row, loops over all categories and finds the maximum data length.
     * Data length: string length after stripping all tags.
     * 
     * @return array: keys: row names values: maximum data lengths. If category is visible the key "#CATEGORY#" contains
     *         the maximum
     *         category length.
     */
    private function getMaxRowDataLengths()
    {
        // Collect maximum data width per row.
        $max_row_lengths = [];
        
        if ($this->is_categories_visible())
        {
            $max_length = 0;
            
            foreach ($this->get_categories() as $category_name)
            {
                $max_length = max($max_length, strlen($category_name));
            }
            
            $max_row_lengths["#CATEGORY#"] = $max_length;
        }
        
        foreach ($this->get_rows() as $row_index => $row_name)
        {
            $max_length = 0;
            
            if ($this->is_row_heading_visible())
            {
                $max_length = max($max_length, strlen($row_name));
            }
            
            foreach ($this->get_categories() as $category_index => $category_name)
            {
                $max_length = max($max_length, strlen(strip_tags($this->data[$category_index][$row_index])));
            }
            
            $max_row_lengths[$row_name] = $max_length;
        }
        
        return $max_row_lengths;
    }
}
?>