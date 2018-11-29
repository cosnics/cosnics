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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return boolean
     */
    protected function addDisplayOrderToContext(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return $this->getDisplayOrderRepository()->addDisplayOrderToContext($displayOrderDataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param string[] $displayOrderPropertiesRecord
     *
     * @return boolean
     * @throws \Exception
     */
    protected function deletePreviousDisplayOrderFromContext(
        DataClassDisplayOrderSupport $displayOrderDataClass, array $displayOrderPropertiesRecord
    )
    {
        return $this->deletePreviousDisplayOrderFromContextForProperties(
            $displayOrderDataClass, $displayOrderPropertiesRecord, $displayOrderDataClass->getDefaultProperties()
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return boolean
     */
    protected function deleteDisplayOrderFromContext(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        $displayOrderContextProperties = array_intersect_key(
            $displayOrderDataClass->getDefaultProperties(),
            array_flip($displayOrderDataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderRepository()->deleteDisplayOrderFromContext(
            $displayOrderDataClass, $displayOrderContextProperties, $this->getDisplayOrderValue($displayOrderDataClass)
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param string[] $displayOrderPropertiesRecord
     * @param string[] $properties
     *
     * @return boolean
     * @throws \Exception
     */
    protected function deletePreviousDisplayOrderFromContextForProperties(
        DataClassDisplayOrderSupport $displayOrderDataClass, array $displayOrderPropertiesRecord, array $properties
    )
    {
        $displayOrderPropertyName = $displayOrderDataClass->getDisplayOrderPropertyName();

        $displayOrderContextProperties = array_intersect_key(
            $properties, array_flip($displayOrderDataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderRepository()->deleteDisplayOrderFromContext(
            $displayOrderDataClass, $displayOrderContextProperties,
            $displayOrderPropertiesRecord[$displayOrderPropertyName]
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param string[] $displayOrderPropertiesRecord
     *
     * @return boolean
     * @throws \Exception
     */
    protected function deletePreviousDisplayOrderFromPreviousContext(
        DataClassDisplayOrderSupport $displayOrderDataClass, array $displayOrderPropertiesRecord
    )
    {
        return $this->deletePreviousDisplayOrderFromContextForProperties(
            $displayOrderDataClass, $displayOrderPropertiesRecord, $displayOrderPropertiesRecord
        );
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return integer
     */
    protected function findNextDisplayOrderValue(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return $this->getDisplayOrderRepository()->findNextDisplayOrderValue($displayOrderDataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return string[]
     * @throws \Exception
     */
    protected function findPreviousDisplayOrderPropertiesRecord(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return $this->getDisplayOrderRepository()->findDisplayOrderPropertiesRecord($displayOrderDataClass);
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return integer
     */
    protected function getDisplayOrderValue(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return $displayOrderDataClass->getDefaultProperty($displayOrderDataClass->getDisplayOrderPropertyName());
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param array $displayOrderPropertiesRecord
     *
     * @return integer
     */
    protected function getDisplayOrderValueFromRecord(
        DataClassDisplayOrderSupport $displayOrderDataClass, array $displayOrderPropertiesRecord
    )
    {
        return $displayOrderPropertiesRecord[$displayOrderDataClass->getDisplayOrderPropertyName()];
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function handleDelete(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return $this->deleteDisplayOrderFromContext($displayOrderDataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return boolean
     */
    protected function hasDisplayOrder(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return !is_null($this->getDisplayOrderValue($displayOrderDataClass));
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param string[] $displayOrderPropertiesRecord
     *
     * @return boolean
     * @throws \Exception
     */
    protected function hasDisplayOrderChanged(
        DataClassDisplayOrderSupport $displayOrderDataClass, array $displayOrderPropertiesRecord
    )
    {
        return $this->getDisplayOrderValue($displayOrderDataClass) !=
            $this->getDisplayOrderValueFromRecord($displayOrderDataClass, $displayOrderPropertiesRecord);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     * @param string[] $displayOrderPropertiesRecord
     *
     * @return boolean
     */
    protected function hasDisplayOrderContextChanged(
        DataClassDisplayOrderSupport $displayOrderDataClass, array $displayOrderPropertiesRecord
    )
    {
        foreach ($displayOrderDataClass->getDisplayOrderContextPropertyNames() as $propertyName)
        {
            if ($displayOrderDataClass->getDefaultProperty($propertyName) !=
                $displayOrderPropertiesRecord[$propertyName])
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return boolean
     */
    public function prepareCreate(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        return $this->handleAddedDataClassInContext($displayOrderDataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return boolean
     */
    protected function handleAddedDataClassInContext(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        if ($this->hasDisplayOrder($displayOrderDataClass))
        {
            if (!$this->addDisplayOrderToContext($displayOrderDataClass))
            {
                return false;
            }
        }
        else
        {
            $this->setDisplayOrderToNextValueInContext($displayOrderDataClass);
        }

        return true;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     *
     * @return boolean
     * @throws \Exception
     */
    public function prepareUpdate(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        $displayOrderPropertiesRecord = $this->findPreviousDisplayOrderPropertiesRecord($displayOrderDataClass);

        $hasDisplayOrderContextChanged =
            $this->hasDisplayOrderContextChanged($displayOrderDataClass, $displayOrderPropertiesRecord);
        $hasDisplayOrderChanged = $this->hasDisplayOrderChanged($displayOrderDataClass, $displayOrderPropertiesRecord);

        if ($hasDisplayOrderContextChanged)
        {
            if (!$this->deletePreviousDisplayOrderFromPreviousContext(
                $displayOrderDataClass, $displayOrderPropertiesRecord
            ))
            {
                return false;
            }

            if (!$this->handleAddedDataClassInContext($displayOrderDataClass))
            {
                return false;
            }
        }
        else
        {
            if ($hasDisplayOrderChanged)
            {
                if (!$this->deletePreviousDisplayOrderFromContext(
                    $displayOrderDataClass, $displayOrderPropertiesRecord
                ))
                {
                    return false;
                }

                if (!$this->addDisplayOrderToContext($displayOrderDataClass))
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $displayOrderDataClass
     */
    protected function setDisplayOrderToNextValueInContext(DataClassDisplayOrderSupport $displayOrderDataClass)
    {
        $displayOrderPropertyName = $displayOrderDataClass->getDisplayOrderPropertyName();
        $displayOrderValue = $this->findNextDisplayOrderValue($displayOrderDataClass);

        $displayOrderDataClass->setDefaultProperty($displayOrderPropertyName, $displayOrderValue);
    }
}