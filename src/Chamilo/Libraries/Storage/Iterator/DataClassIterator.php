<?php
namespace Chamilo\Libraries\Storage\Iterator;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;

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
     * @var string
     */
    private $dataClassName;

    public function __construct($dataClassName, $dataClasses)
    {
        parent::__construct($dataClasses);
        $this->dataClassName = $dataClassName;
    }

    /**
     *
     * @return string
     */
    protected function getDataClassName()
    {
        return $this->dataClassName;
    }

    /**
     *
     * @param string $dataClassName
     */
    protected function setDataClassName($dataClassName)
    {
        $this->dataClassName = $dataClassName;
    }

    /**
     *
     * @return string
     */
    public function getCacheClassName()
    {
        $compositeDataClassName = CompositeDataClass::class_name();
        $className = $this->getDataClassName();

        $isCompositeDataClass = is_subclass_of($className, $compositeDataClassName);
        $isExtensionClass = get_parent_class($className) !== $compositeDataClassName;

        if ($isCompositeDataClass && $isExtensionClass)
        {
            return $className::parent_class_name();
        }
        else
        {
            return $className;
        }
    }

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
