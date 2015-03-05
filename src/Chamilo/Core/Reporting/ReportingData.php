<?php
namespace Chamilo\Core\Reporting;

class ReportingData
{

    private $title;

    private $description;

    private $data = array();

    private $rows = array();

    private $categories = array();

    private $show_categories = true;

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

    public function add_row($value)
    {
        $this->rows[] = $value;
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
}
