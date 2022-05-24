<?php
namespace Chamilo\Libraries\Storage\Iterator;

use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package Chamilo\Libraries\Storage\Iterator
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-extends ArrayCollection<TKey,T>
 */
class DataClassIterator extends ArrayCollection
{
    const POSITION_FIRST = 1;
    const POSITION_INVALID = 5;
    const POSITION_LAST = 2;
    const POSITION_MIDDLE = 4;
    const POSITION_SINGLE = 3;

    /**
     *
     * @var string
     */
    private string $dataClassName;

    /**
     * DataClassIterator constructor.
     *
     * @param string $dataClassName
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[] $dataClasses
     */
    public function __construct(string $dataClassName, array $dataClasses = [])
    {
        parent::__construct($dataClasses);
        $this->dataClassName = $dataClassName;
    }

    /**
     * @return array
     * @psalm-return array<TKey,T>
     *
     * @deprecated Backwards compatibility with ArrayIterator, use ArrayCollection::toArray() now
     */
    public function getArrayCopy(): array
    {
        return $this->toArray();
    }

    public function getCacheClassName(): string
    {
        $className = $this->getDataClassName();

        $isCompositeDataClass = is_subclass_of($className, CompositeDataClass::class);
        $isExtensionClass = get_parent_class($className) !== CompositeDataClass::class;

        if ($isCompositeDataClass && $isExtensionClass)
        {
            return $className::parentClassName();
        }
        else
        {
            return $className;
        }
    }

    public function getCurrentEntryPositionType(): int
    {
        if ($this->count() == 1)
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

    protected function getDataClassName(): string
    {
        return $this->dataClassName;
    }

    protected function setDataClassName(string $dataClassName): DataClassIterator
    {
        $this->dataClassName = $dataClassName;

        return $this;
    }

    public function hasOnlyOneEntry(): bool
    {
        return $this->count() == 1;
    }

    public function isCurrentEntryFirst(): bool
    {
        return $this->isCurrentEntryOnPosition(self::POSITION_FIRST);
    }

    public function isCurrentEntryInTheMiddle(): bool
    {
        return $this->isCurrentEntryOnPosition(self::POSITION_MIDDLE);
    }

    public function isCurrentEntryLast(): bool
    {
        return $this->isCurrentEntryOnPosition(self::POSITION_LAST);
    }

    public function isCurrentEntryOnPosition(int $position): bool
    {
        return ($this->getCurrentEntryPositionType() === $position || $this->hasOnlyOneEntry());
    }

    /**
     * @return mixed
     * @psalm-return T|false
     *
     * @deprecated Backwards compatibility with ArrayIterator, use ArrayCollection::first() now
     */
    public function rewind()
    {
        return $this->first();
    }
}
