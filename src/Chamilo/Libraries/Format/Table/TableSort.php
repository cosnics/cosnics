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
     * @var integer[]
     */
    private array $columns;

    /**
     *
     * @var string[][]
     */
    private array $data;

    /**
     *
     * @var integer[]
     */
    private array $directions;

    /**
     *
     * @param string[][] $data
     * @param array $columns
     * @param array $directions
     */
    public function __construct(array $data, array $columns = [0], array $directions = [SORT_ASC])
    {
        $this->data = $data;
        $this->columns = $columns;
        $this->directions = $directions;
    }

    /**
     *
     * @return integer[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     *
     * @return string[][]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     *
     * @param string[][] $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     *
     * @return integer[]
     */
    public function getDirections(): array
    {
        return $this->directions;
    }

    /**
     * Checks whether a column of a 2D-array contains only dates (GNU date syntax)
     *
     * @param string[][] $data The data-array
     * @param integer $column The index of the column to check
     *
     * @return boolean
     */
    public function isDateColumn(array &$data, int $column): bool
    {
        $isDate = true;

        foreach ($data as $row)
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
    public function isImageColumn(array &$data, int $column): bool
    {
        $isImage = true;

        foreach ($data as $row)
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
    public function isNumericColumn(array &$data, int $column): bool
    {
        $isNumeric = true;

        foreach ($data as $row)
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
     *
     * @param integer[] $columns
     */
    public function setColumn(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     *
     * @param integer[] $directions
     */
    public function setDirection(array $directions)
    {
        $this->directions = $directions;
    }

    /**
     * Sorts a 2-dimensional table.
     */
    public function sort(): array
    {
        $data = $this->getData();

        if (!is_array($data) || empty($data))
        {
            return [];
        }

        foreach ($this->getColumns() as $column)
        {
            if ($column != strval(intval($column)))
            {
                // Probably an attack
                return $data;
            }
        }

        foreach ($this->getDirections() as $direction)
        {
            if (!in_array($direction, array(SORT_ASC, SORT_DESC)))
            {
                // Probably an attack
                return $data;
            }
        }

        $firstColumn = $this->getColumns()[0];
        $firstDirection = $this->getDirections()[0];

        $compare_operator = $this->getDirections()[0] == SORT_ASC ? '>' : '<=';

        if ($this->isImageColumn($data, $firstColumn))
        {
            $compareFunction = function ($a, $b) use ($firstColumn, $firstDirection) {
                $compareResult = strnatcmp(
                    strip_tags($a[$firstColumn], '<img>'), strip_tags($b[$firstColumn], '<img>')
                );

                if ($firstDirection == SORT_ASC)
                {
                    return $compareResult > 0;
                }
                else
                {
                    return $compareResult <= 0;
                }
            };
        }
        elseif ($this->isDateColumn($data, $firstColumn))
        {
            $compareFunction = function ($a, $b) use ($firstColumn, $firstDirection) {
                $aTime = strtotime(strip_tags($a[$firstColumn]));
                $bTime = strtotime(strip_tags($b[$firstColumn]));

                if ($firstDirection == SORT_ASC)
                {
                    return $aTime > $bTime;
                }
                else
                {
                    return $aTime <= $bTime;
                }
            };
        }
        elseif ($this->isNumericColumn($data, $firstColumn))
        {
            $compareFunction = function ($a, $b) use ($firstColumn, $firstDirection) {
                $aTime = strip_tags($a[$firstColumn]);
                $bTime = strip_tags($b[$firstColumn]);

                if ($firstDirection == SORT_ASC)
                {
                    return $aTime > $bTime;
                }
                else
                {
                    return $aTime <= $bTime;
                }
            };
        }
        else
        {
            $compareFunction = function ($a, $b) use ($firstColumn, $firstDirection) {
                $compareResult = strnatcmp(
                    strip_tags($a[$firstColumn]), strip_tags($b[$firstColumn])
                );

                if ($firstDirection == SORT_ASC)
                {
                    return $compareResult > 0;
                }
                else
                {
                    return $compareResult <= 0;
                }
            };
        }

        // Sort the content
        usort($data, $compareFunction);

        return $data;
    }
}
