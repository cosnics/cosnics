<?php
namespace Chamilo\Libraries\Format\Table;

/**
 *
 * @package common.html.table
 */
// $Id: table_sort.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
define('SORT_DATE', 3);
define('SORT_IMAGE', 4);
class TableSort
{

    /**
     * Sorts a 2-dimensional table.
     * 
     * @param array $data The data to be sorted.
     * @param int $column The column on which the data should be sorted (default = 0)
     * @param string $direction The direction to sort (SORT_ASC (default) or SORT_DESC)
     * @return array The sorted dataset
     * @author digitaal-leren@hogent.be
     */
    public function sort_table($data, $column = 0, $direction = SORT_ASC)
    {
        if (! is_array($data) || empty($data))
        {
            return array();
        }
        if ($column != strval(intval($column)))
        {
            // Probably an attack
            return $data;
        }
        if (! in_array($direction, array(SORT_ASC, SORT_DESC)))
        {
            // Probably an attack
            return $data;
        }
        
        if (TableSort::is_image_column($data, $column))
        {
            $type = SORT_IMAGE;
        }
        elseif (TableSort::is_date_column($data, $column))
        {
            $type = SORT_DATE;
        }
        elseif (TableSort::is_numeric_column($data, $column))
        {
            $type = SORT_NUMERIC;
        }
        else
        {
            $type = SORT_STRING;
        }
        
        $compare_operator = $direction == SORT_ASC ? '>' : '<=';
        
        switch ($type)
        {
            case SORT_NUMERIC :
                $compare_function = 'return strip_tags($a[' . $column . ']) ' . $compare_operator . ' strip_tags($b[' .
                     $column . ']);';
                break;
            case SORT_IMAGE :
                $compare_function = 'return strnatcmp(strip_tags($a[' . $column . '], "<img>"), strip_tags($b[' . $column .
                     '], "<img>")) ' . $compare_operator . ' 0;';
                break;
            case SORT_DATE :
                $compare_function = 'return strtotime(strip_tags($a[' . $column . '])) ' . $compare_operator .
                     ' strtotime(strip_tags($b[' . $column . ']));';
            case SORT_STRING :
            default :
                $compare_function = 'return strnatcmp(strip_tags($a[' . $column . ']), strip_tags($b[' . $column . '])) ' .
                     $compare_operator . ' 0;';
                break;
        }
        
        // Sort the content
        usort($data, create_function('$a, $b', $compare_function));
        return $data;
    }

    /**
     * Checks whether a column of a 2D-array contains only numeric values
     * 
     * @param array $data The data-array
     * @param int $column The index of the column to check
     * @return bool true if column contains only dates, false otherwise
     * @todo Take locale into account (eg decimal point or comma ?)
     * @author digitaal-leren@hogent.be
     */
    public function is_numeric_column(& $data, $column)
    {
        $is_numeric = true;
        foreach ($data as $index => & $row)
        {
            $is_numeric &= is_numeric(strip_tags($row[$column]));
            if (! $is_numeric)
            {
                break;
            }
        }
        return $is_numeric;
    }

    /**
     * Checks whether a column of a 2D-array contains only dates (GNU date syntax)
     * 
     * @param array $data The data-array
     * @param int $column The index of the column to check
     * @return bool true if column contains only dates, false otherwise
     * @author digitaal-leren@hogent.be
     */
    public function is_date_column(& $data, $column)
    {
        $is_date = true;
        foreach ($data as $index => & $row)
        {
            if (strlen(strip_tags($row[$column])) != 0)
            {
                $check_date = strtotime(strip_tags($row[$column]));
                // strtotime Returns a timestamp on success, FALSE otherwise.
                // Previous to PHP 5.1.0, this function would return -1 on failure.
                $is_date &= ($check_date != - 1 && $check_date != false);
            }
            else
            {
                $is_date &= false;
            }
            if (! $is_date)
            {
                break;
            }
        }
        return $is_date;
    }

    /**
     * Checks whether a column of a 2D-array contains only images (<img src=" path/file.ext" alt=".."/>)
     * 
     * @param array $data The data-array
     * @param int $column The index of the column to check
     * @return bool true if column contains only images, false otherwise
     * @author digitaal-leren@hogent.be
     */
    public function is_image_column(& $data, $column)
    {
        $is_image = true;
        foreach ($data as $index => & $row)
        {
            $is_image &= strlen(trim(strip_tags($row[$column], '<img>'))) > 0; // at least one img-tag
            $is_image &= strlen(trim(strip_tags($row[$column]))) == 0; // and no text outside attribute-values
            if (! $is_image)
            {
                break;
            }
        }
        return $is_image;
    }
}
