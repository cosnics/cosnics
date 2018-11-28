<?php
namespace Chamilo\Libraries\Storage\DataClass\Interfaces;

/**
 * Interface DisplayOrderSupport
 * @package Chamilo\Libraries\Storage\DataClass\Interfaces
 */
interface DisplayOrderSupport
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
     * @return integer
     */
    public function getId();
}