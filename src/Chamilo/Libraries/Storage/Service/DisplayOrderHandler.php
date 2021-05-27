<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;
use Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository;
use Chamilo\Libraries\Storage\Exception\DisplayOrderException;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

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
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository $displayOrderRepository
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(DisplayOrderRepository $displayOrderRepository, Translator $translator)
    {
        $this->displayOrderRepository = $displayOrderRepository;
        $this->translator = $translator;
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
     * @return int
     */
    protected function countOtherDisplayOrdersInContext(DataClassDisplayOrderSupport $dataClass)
    {
        return $this->getDisplayOrderRepository()->countOtherDisplayOrdersInContext($dataClass);
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
    protected function deletePreviousDisplayOrderFromPreviousContext(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    )
    {
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();

        $displayOrderContextProperties = array_intersect_key(
            $displayOrderPropertiesRecord, array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderRepository()->deleteDisplayOrderFromContext(
            $dataClass, $displayOrderContextProperties, $displayOrderPropertiesRecord[$displayOrderPropertyName]
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
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return string
     */
    protected function getDisplayOrderContextAsString(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrderContextProperties = array_intersect_key(
            $dataClass->getDefaultProperties(), array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        $displayOrderContext = [];

        foreach ($displayOrderContextProperties as $displayOrderContextProperty => $displayOrderContextPropertyValue)
        {
            $displayOrderContext[] = $displayOrderContextProperty . ' = ' . $displayOrderContextPropertyValue;
        }

        return implode(', ', $displayOrderContext);
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
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
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
     * @return bool
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    public function handleDisplayOrderBeforeCreate(DataClassDisplayOrderSupport $dataClass)
    {
        $this->validateDisplayOrder($dataClass);

        return $this->handleAddedDataClassInContext($dataClass);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @return boolean
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Exception
     */
    public function handleDisplayOrderBeforeUpdate(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrderPropertiesRecord = $this->findPreviousDisplayOrderPropertiesRecord($dataClass);

        $hasDisplayOrderContextChanged =
            $this->hasDisplayOrderContextChanged($dataClass, $displayOrderPropertiesRecord);
        $hasDisplayOrderChanged = $this->hasDisplayOrderChanged($dataClass, $displayOrderPropertiesRecord);

        if ($hasDisplayOrderContextChanged || $hasDisplayOrderChanged)
        {
            $this->validateDisplayOrder($dataClass);

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

        return true;
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
     */
    protected function setDisplayOrderToNextValueInContext(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();
        $displayOrderValue = $this->findNextDisplayOrderValue($dataClass);

        $dataClass->setDefaultProperty($displayOrderPropertyName, $displayOrderValue);
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport $dataClass
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     */
    protected function validateDisplayOrder(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrder = $this->getDisplayOrderValue($dataClass);
        $numberOfOtherDisplayOrdersInContext = $this->countOtherDisplayOrdersInContext($dataClass);

        $hasDisplayOrder = $this->hasDisplayOrder($dataClass);
        $displayOrderTooLow = $displayOrder < 1;
        $displayOrderTooHigh = $displayOrder > ($numberOfOtherDisplayOrdersInContext + 1);

        if ($hasDisplayOrder && ($displayOrderTooLow || $displayOrderTooHigh))
        {
            throw new DisplayOrderException(
                $this->getTranslator()->trans(
                    'InvalidDisplayOrderExceptionMessage', [
                    '{TYPE}' => get_class($dataClass),
                    '{ID}' => $dataClass->getId(),
                    '{CONTEXT}' => $this->getDisplayOrderContextAsString($dataClass),
                    '{DISPLAY_ORDER}' => $displayOrder,
                    '{COUNT}' => $numberOfOtherDisplayOrdersInContext
                ], Utilities::COMMON_LIBRARIES
                )
            );
        }
    }
}
