<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport;
use Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository;

/**
 * @package Chamilo\Libraries\Storage\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DisplayOrderHandler
{
    /**
     * @var \Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository
     */
    private $displayOrderRepository;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository $displayOrderRepository
     */
    public function __construct(DisplayOrderRepository $displayOrderRepository)
    {
        $this->displayOrderRepository = $displayOrderRepository;
    }

    /**
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository
     */
    public function getDisplayOrderRepository(): DisplayOrderRepository
    {
        return $this->displayOrderRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository $displayOrderRepository
     */
    public function setDisplayOrderRepository(DisplayOrderRepository $displayOrderRepository): void
    {
        $this->displayOrderRepository = $displayOrderRepository;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return integer
     */
    public function findNextDisplayOrderValue(DisplayOrderSupport $dataClass)
    {
        return $this->getDisplayOrderRepository()->findNextDisplayOrderValue($dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     */
    public function prepareCreate(DisplayOrderSupport $dataClass)
    {
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();
        $displayOrderValue = $this->findNextDisplayOrderValue($dataClass);

        $dataClass->setDefaultProperty($displayOrderPropertyName, $displayOrderValue);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return boolean
     */
    public function handleDelete(DisplayOrderSupport $dataClass)
    {
        return $this->getDisplayOrderRepository()->updateDisplayOrders($dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return string[]
     * @throws \Exception
     */
    public function findDisplayOrderPropertiesRecord(DisplayOrderSupport $dataClass)
    {
        return $this->getDisplayOrderRepository()->findDisplayOrderPropertiesRecord($dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function hasDisplayOrderContextChanged(DisplayOrderSupport $dataClass)
    {
        $displayOrderPropertiesRecord = $this->findDisplayOrderPropertiesRecord($dataClass);

        foreach ($dataClass->getDisplayOrderContextPropertyNames() as $propertyName)
        {
            if ($dataClass->getDefaultProperty($propertyName) != $displayOrderPropertiesRecord[$propertyName])
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function hasDisplayOrderChanged(DisplayOrderSupport $dataClass)
    {
        $displayOrderPropertiesRecord = $this->findDisplayOrderPropertiesRecord($dataClass);
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();

        return $dataClass->getDefaultProperty($displayOrderPropertyName) !=
            $displayOrderPropertiesRecord[$displayOrderPropertyName];
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DisplayOrderSupport $dataClass
     *
     * @throws \Exception
     */
    public function prepareUpdate(DisplayOrderSupport $dataClass)
    {
        $hasDisplayOrderContextChanged = $this->hasDisplayOrderContextChanged($dataClass);
        $hasDisplayOrderChanged = $this->hasDisplayOrderChanged($dataClass);

        var_dump($hasDisplayOrderContextChanged, $hasDisplayOrderChanged);
    }
}