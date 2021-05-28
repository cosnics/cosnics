<?php
namespace Chamilo\Libraries\Format\Table;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author digitaal-leren@hogent.be
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableSort
{
    const SORT_DATE = 3;
    const SORT_IMAGE = 4;

    /**
     *
     * @var string[][]
     */
    private $data;

    /**
     *
     * @var integer
     */
    private $column;

    /**
     *
     * @var integer
     */
    private $direction;

    /**
     *
     * @param string[][] $data
     * @param integer $column
     * @param integer $direction
     */
    public function __construct($data, $column = 0, $direction = SORT_ASC)
    {
        $this->data = $data;
        $this->column = $column;
        $this->direction = $direction;
    }

    /**
     *
     * @return integer
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     *
     * @param integer $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    /**
     *
     * @return string[][]
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *
     * @param string[][] $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     *
     * @return integer
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     *
     * @param integer $direction
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
    }

    /**
     * Checks whether a column of a 2D-array contains only dates (GNU date syntax)
     *
     * @param string[][] $data The data-array
     * @param integer $column The index of the column to check
     *
     * @return boolean
     */
    public function isDateColumn(&$data, $column)
    {
        $isDate = true;

        foreach ($data as $index => $row)
        {
            if (strlen(strip_tags($row[$column])) != 0)
            {
                $check_date = strtotime(strip_tags($row[$column]));
                // strtotime Returns a timestamp on success, FALSE otherwise.
                // Previous to PHP 5.1.0, this function would return -1 on failure.
                $isDate &= ($check_date != - 1 && $check_date != false);
            }
            else
            {
                $isDate &= false;
            }

            if (!$isDate)
            {
                break;
            }
        }

        return $isDate;
    }

    /**
     * Checks whether a column of a 2D-array contains only images (<img src=" path/file.ext" alt=".."/>)
     *
     * @param string[][] $data The data-array
     * @param integer $column The index of the column to check
     *
     * @return boolean
     */
    public function isImageColumn(&$data, $column)
    {
        $isImage = true;

        foreach ($data as $index => $row)
        {
            $isImage &= strlen(trim(strip_tags($row[$column], '<img>'))) > 0; // at least one img-tag
            $isImage &= strlen(trim(strip_tags($row[$column]))) == 0; // and no text outside attribute-values
            if (!$isImage)
            {
                break;
            }
        }

        return $isImage;
    }

    /**
     * Checks whether a column of a 2D-array contains only numeric values
     *
     * @param string[][] $data The data-array
     * @param integer $column The index of the column to check
     *
     * @return boolean
     */
    public function isNumericColumn(&$data, $column)
    {
        $isNumeric = true;

        foreach ($data as $index => $row)
        {
            $isNumeric &= is_numeric(strip_tags($row[$column]));

            if (!$isNumeric)
            {
                break;
            }
        }

        return $isNumeric;
    }

    /**
     * Sorts a 2-dimensional table.
     */
    public function sort()
    {
        $data = $this->getData();

        if (!is_array($data) || empty($data))
        {
            return [];
        }

        if ($this->getColumn() != strval(intval($this->getColumn())))
        {
            // Probably an attack
            return $data;
        }

        if (!in_array($this->getDirection(), array(SORT_ASC, SORT_DESC)))
        {
            // Probably an attack
            return $data;
        }

        if ($this->isImageColumn($data, $this->getColumn()))
        {
            $type = self::SORT_IMAGE;
        }
        elseif ($this->isDateColumn($data, $this->getColumn()))
        {
            $type = self::SORT_DATE;
        }
        elseif ($this->isNumericColumn($data, $this->getColumn()))
        {
            $type = SORT_NUMERIC;
        }
        else
        {
            $type = SORT_STRING;
        }

        $compare_operator = $this->getDirection() == SORT_ASC ? '>' : '<=';

        switch ($type)
        {
            case SORT_NUMERIC :
                $compare_function =
                    'return strip_tags($a[' . $this->getColumn() . ']) ' . $compare_operator . ' strip_tags($b[' .
                    $this->getColumn() . ']);';
                break;
            case self::SORT_IMAGE :
                $compare_function =
                    'return strnatcmp(strip_tags($a[' . $this->getColumn() . '], "<img>"), strip_tags($b[' .
                    $this->getColumn() . '], "<img>")) ' . $compare_operator . ' 0;';
                break;
            case self::SORT_DATE :
                $compare_function =
                    'return strtotime(strip_tags($a[' . $this->getColumn() . '])) ' . $compare_operator .
                    ' strtotime(strip_tags($b[' . $this->getColumn() . ']));';
                break;
            case SORT_STRING :
            default :
                $compare_function =
                    'return strnatcmp(strip_tags($a[' . $this->getColumn() . ']), strip_tags($b[' . $this->getColumn() .
                    '])) ' . $compare_operator . ' 0;';
                break;
        }

        // Sort the content
        usort($data, create_function('$a, $b', $compare_function));

        return $data;
    }
}
