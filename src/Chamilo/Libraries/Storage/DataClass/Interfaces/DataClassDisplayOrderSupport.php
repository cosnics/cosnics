<?php
namespace Chamilo\Libraries\Storage\DataClass\Interfaces;

/**
 * Interface DisplayOrderDataClassSupport
 * @package Chamilo\Libraries\Storage\DataClass\Interfaces
 */
interface DataClassDisplayOrderSupport
{

    /**
     * @return string[]
     */
    public function getDefaultProperties();

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function getDefaultProperty($propertyName);

    /**
     * @return string[]
     */
    public function getDisplayOrderContextPropertyNames();

    /**
     * @return string
     */
    public function getDisplayOrderPropertyName();

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return boolean
     */
    public function isIdentified();

    /**
     * @param string $propertyName
     * @param string $propertyvalue
     */
    public function setDefaultProperty($propertyName, $propertyvalue);
}