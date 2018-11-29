<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;
use Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository;

/**
 * @package Chamilo\Libraries\Storage\Service
 *
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     */
    protected function addDisplayOrderToContext(DataClassDisplayOrderSupport $dataClass)
    {
        return $this->getDisplayOrderRepository()->addDisplayOrderToContext($dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     */
    protected function deleteDisplayOrderFromContext(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrderContextProperties = array_intersect_key(
            $dataClass->getDefaultProperties(), array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderRepository()->deleteDisplayOrderFromContext(
            $dataClass, $displayOrderContextProperties, $this->getDisplayOrderValue($dataClass)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param string[] $displayOrderPropertiesRecord
     *
     * @return boolean
     * @throws \Exception
     */
    protected function deletePreviousDisplayOrderFromContext(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    )
    {
        return $this->deletePreviousDisplayOrderFromContextForProperties(
            $dataClass, $displayOrderPropertiesRecord, $dataClass->getDefaultProperties()
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param string[] $displayOrderPropertiesRecord
     * @param string[] $properties
     *
     * @return boolean
     * @throws \Exception
     */
    protected function deletePreviousDisplayOrderFromContextForProperties(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord, array $properties
    )
    {
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();

        $displayOrderContextProperties = array_intersect_key(
            $properties, array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderRepository()->deleteDisplayOrderFromContext(
            $dataClass, $displayOrderContextProperties, $displayOrderPropertiesRecord[$displayOrderPropertyName]
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param string[] $displayOrderPropertiesRecord
     *
     * @return boolean
     * @throws \Exception
     */
    protected function deletePreviousDisplayOrderFromPreviousContext(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    )
    {
        return $this->deletePreviousDisplayOrderFromContextForProperties(
            $dataClass, $displayOrderPropertiesRecord, $displayOrderPropertiesRecord
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return integer
     */
    protected function findNextDisplayOrderValue(DataClassDisplayOrderSupport $dataClass)
    {
        return $this->getDisplayOrderRepository()->findNextDisplayOrderValue($dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return string[]
     * @throws \Exception
     */
    protected function findPreviousDisplayOrderPropertiesRecord(DataClassDisplayOrderSupport $dataClass)
    {
        return $this->getDisplayOrderRepository()->findDisplayOrderPropertiesRecord($dataClass);
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return integer
     */
    protected function getDisplayOrderValue(DataClassDisplayOrderSupport $dataClass)
    {
        return $dataClass->getDefaultProperty($dataClass->getDisplayOrderPropertyName());
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param array $displayOrderPropertiesRecord
     *
     * @return integer
     */
    protected function getDisplayOrderValueFromRecord(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    )
    {
        return $displayOrderPropertiesRecord[$dataClass->getDisplayOrderPropertyName()];
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     */
    protected function handleAddedDataClassInContext(DataClassDisplayOrderSupport $dataClass)
    {
        if ($this->hasDisplayOrder($dataClass))
        {
            if (!$this->addDisplayOrderToContext($dataClass))
            {
                return false;
            }
        }
        else
        {
            $this->setDisplayOrderToNextValueInContext($dataClass);
        }

        return true;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function handleDisplayOrderAfterDelete(DataClassDisplayOrderSupport $dataClass)
    {
        return $this->deleteDisplayOrderFromContext($dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     */
    protected function hasDisplayOrder(DataClassDisplayOrderSupport $dataClass)
    {
        return !is_null($this->getDisplayOrderValue($dataClass));
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param string[] $displayOrderPropertiesRecord
     *
     * @return boolean
     * @throws \Exception
     */
    protected function hasDisplayOrderChanged(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    )
    {
        return $this->getDisplayOrderValue($dataClass) !=
            $this->getDisplayOrderValueFromRecord($dataClass, $displayOrderPropertiesRecord);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     * @param string[] $displayOrderPropertiesRecord
     *
     * @return boolean
     */
    protected function hasDisplayOrderContextChanged(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    )
    {
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     */
    public function handleDisplayOrderBeforeCreate(DataClassDisplayOrderSupport $dataClass)
    {
        return $this->handleAddedDataClassInContext($dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function handleDisplayOrderBeforeUpdate(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrderPropertiesRecord = $this->findPreviousDisplayOrderPropertiesRecord($dataClass);

        $hasDisplayOrderContextChanged =
            $this->hasDisplayOrderContextChanged($dataClass, $displayOrderPropertiesRecord);
        $hasDisplayOrderChanged = $this->hasDisplayOrderChanged($dataClass, $displayOrderPropertiesRecord);

        if ($hasDisplayOrderContextChanged)
        {
            if (!$this->deletePreviousDisplayOrderFromPreviousContext(
                $dataClass, $displayOrderPropertiesRecord
            ))
            {
                return false;
            }

            if (!$this->handleAddedDataClassInContext($dataClass))
            {
                return false;
            }
        }
        else
        {
            if ($hasDisplayOrderChanged)
            {
                if (!$this->deletePreviousDisplayOrderFromContext(
                    $dataClass, $displayOrderPropertiesRecord
                ))
                {
                    return false;
                }

                if (!$this->addDisplayOrderToContext($dataClass))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     */
    protected function setDisplayOrderToNextValueInContext(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();
        $displayOrderValue = $this->findNextDisplayOrderValue($dataClass);

        $dataClass->setDefaultProperty($displayOrderPropertyName, $displayOrderValue);
    }
}