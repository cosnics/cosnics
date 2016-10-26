<?php
namespace Chamilo\Libraries\Storage\Iterator;

/**
 *
 * @package Chamilo\Libraries\Storage\Iterator
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataClassIterator extends \ArrayIterator
{
    const POSITION_FIRST = 1;
    const POSITION_LAST = 2;
    const POSITION_SINGLE = 3;
    const POSITION_MIDDLE = 4;
    const POSITION_INVALID = 5;

    /**
     *
     * @return integer
     */
    public function getCurrentEntryPositionType()
    {
        if ($this->count() == 0)
        {
            return self::POSITION_EMPTY;
        }
        elseif ($this->count() == 1)
        {
            return self::POSITION_SINGLE;
        }
        elseif ($this->count() > 1 && $this->key() == ($this->count() - 1))
        {
            return self::POSITION_LAST;
        }
        elseif ($this->count() > 1 && $this->key() == 0)
        {
            return self::POSITION_FIRST;
        }
        elseif ($this->key() == 0 || $this->key() > ($this->count() - 1))
        {
            return self::POSITION_INVALID;
        }
        else
        {
            return self::POSITION_MIDDLE;
        }
    }

    /**
     *
     * @param integer $position
     * @return boolean
     */
    public function isCurrentEntryOnPosition($position)
    {
        return ($this->getCurrentEntryPositionType() === $position || $this->hasOnlyOneEntry());
    }

    /**
     *
     * @return boolean
     */
    public function isCurrentEntryFirst()
    {
        return $this->isCurrentEntryOnPosition(self::POSITION_FIRST);
    }

    /**
     *
     * @return boolean
     */
    public function isCurrentEntryLast()
    {
        return $this->isCurrentEntryOnPosition(self::POSITION_LAST);
    }

    /**
     *
     * @return boolean
     */
    public function isCurrentEntryInTheMiddle()
    {
        return $this->isCurrentEntryOnPosition(self::POSITION_MIDDLE);
    }

    /**
     *
     * @return boolean
     */
    public function hasOnlyOneEntry()
    {
        return $this->count() == 1;
    }
}
