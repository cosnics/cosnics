<?php

namespace Chamilo\Libraries\Test\Stub;

use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;
use InvalidArgumentException;

/**
 * @package Chamilo\Libraries\Test\Stub
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
Class DisplayOrderDataClassStub implements DataClassDisplayOrderSupport
{
    const PROPERTY_ID = 'id';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_PARENT_ID = 'parent_id';
    const NO_UID = - 1;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $sort;

    /**
     * @var int
     */
    protected $parentId;

    public function getDisplayOrderPropertyName(): string
    {
        return self::PROPERTY_SORT;
    }

    /**
     * @return string[]
     */
    public function getDisplayOrderContextPropertyNames(): array
    {
        return [self::PROPERTY_PARENT_ID];
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function getDefaultProperty($propertyName)
    {
        switch ($propertyName)
        {
            case self::PROPERTY_SORT:
                return $this->sort;
            case self::PROPERTY_PARENT_ID:
                return $this->parentId;
        }

        throw new InvalidArgumentException(
            sprintf('The given property with name %s could not be found', $propertyName)
        );
    }

    /**
     * @param string $propertyName
     * @param string $propertyvalue
     */
    public function setDefaultProperty($propertyName, $propertyvalue)
    {
        switch ($propertyName)
        {
            case self::PROPERTY_SORT:
                $this->sort = $propertyvalue;

                return;
            case self::PROPERTY_PARENT_ID:
                $this->parentId = $propertyvalue;

                return;
        }

        throw new InvalidArgumentException(
            sprintf('The given property with name %s could not be found', $propertyName)
        );
    }

    /**
     * @return integer
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort)
    {
        $this->sort = $sort;
    }

    /**
     * @param int $parentId
     */
    public function setParentId(int $parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return string[]
     */
    public function getDefaultProperties(): array
    {
        return array(
            self::PROPERTY_ID => $this->getId(), self::PROPERTY_SORT => $this->getSort(),
            self::PROPERTY_PARENT_ID => $this->getParentId()
        );
    }

    /**
     * @return boolean
     */
    public function isIdentified(): bool
    {
        $id = $this->getId();

        return isset($id) && strlen($id) > 0 && $id != self::NO_UID;
    }
}