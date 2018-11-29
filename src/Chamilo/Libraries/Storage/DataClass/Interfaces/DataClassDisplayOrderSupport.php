<?php
namespace Chamilo\Libraries\Storage\DataClass\Interfaces;

/**
 * Interface DisplayOrderDataClassSupport
 * @package Chamilo\Libraries\Storage\DataClass\Interfaces
 */
interface DataClassDisplayOrderSupport
{

    /**
     * @return string
     */
    public function getDisplayOrderPropertyName();

    /**
     * @return string[]
     */
    public function getDisplayOrderContextPropertyNames();

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function getDefaultProperty($propertyName);

    /**
     * @param string $propertyName
     * @param string $propertyvalue
     */
    public function setDefaultProperty($propertyName, $propertyvalue);

    /**
     * @return string[]
     */
    public function getDefaultProperties();

    /**
     * @return integer
     */
    public function getId();

    /**
     * @return boolean
     */
    public function isIdentified();
}