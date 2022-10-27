<?php
namespace Chamilo\Libraries\Format\Table;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  digitaal-leren@hogent.be
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TableSort
{
    public const SORT_DATE = 3;
    public const SORT_IMAGE = 4;

    private int $column;

    /**
     * @var string[][]
     */
    private array $data;

    private int $direction;

    /**
     * @param string[][] $data
     */
    public function __construct(array $data, int $column = 0, int $direction = SORT_ASC)
    {
        $this->data = $data;
        $this->column = $column;
        $this->direction = $direction;
    }

    public function getColumn(): int
    {
        return $this->column;
    }

    /**
     * @return string[][]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param string[][] $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getDirection(): int
    {
        return $this->direction;
    }

    public function setDirection(int $direction)
    {
        $this->direction = $direction;
    }

    /**
     * Checks whether a column of a 2D-array contains only dates (GNU date syntax)
     *
     * @param string[][] $data The data-array
     * @param int $column      The index of the column to check
     *
     * @return bool
     */
    public function isDateColumn(array $data, int $column): bool
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
     * @param int $column      The index of the column to check
     *
     * @return bool
     */
    public function isImageColumn(array $data, int $column): bool
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
     * @param int $column      The index of the column to check
     *
     * @return bool
     */
    public function isNumericColumn(array $data, int $column): bool
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
     * Sorts a 2-dimensional table.
     */
    public function sort(): array
    {
        $data = $this->getData();

        if (empty($data))
        {
            return [];
        }

        if (!in_array($this->getDirection(), [SORT_ASC, SORT_DESC]))
        {
            // Probably an attack
            return $data;
        }

        $column = $this->getColumn();
        $direction = $this->getDirection();

        if ($this->isImageColumn($data, $column))
        {
            $compareFunction = function ($a, $b) use ($column, $direction) {
                $compareResult = strnatcmp(
                    strip_tags($a[$column], '<img>'), strip_tags($b[$column], '<img>')
                );

                if ($direction == SORT_ASC)
                {
                    return $compareResult > 0;
                }
                else
                {
                    return $compareResult <= 0;
                }
            };
        }
        elseif ($this->isDateColumn($data, $column))
        {
            $compareFunction = function ($a, $b) use ($column, $direction) {
                $aTime = strtotime(strip_tags($a[$column]));
                $bTime = strtotime(strip_tags($b[$column]));

                if ($direction == SORT_ASC)
                {
                    return $aTime > $bTime;
                }
                else
                {
                    return $aTime <= $bTime;
                }
            };
        }
        elseif ($this->isNumericColumn($data, $column))
        {
            $compareFunction = function ($a, $b) use ($column, $direction) {
                $aTime = strip_tags($a[$column]);
                $bTime = strip_tags($b[$column]);

                if ($direction == SORT_ASC)
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
            $compareFunction = function ($a, $b) use ($column, $direction) {
                $compareResult = strnatcmp(
                    strip_tags($a[$column]), strip_tags($b[$column])
                );

                if ($direction == SORT_ASC)
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
