<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassDisplayOrderSupport;
use Chamilo\Libraries\Storage\DataManager\Repository\DisplayOrderRepository;
use Chamilo\Libraries\Storage\Exception\DisplayOrderException;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DisplayOrderHandler
{
    private DisplayOrderRepository $displayOrderRepository;

    private Translator $translator;

    public function __construct(DisplayOrderRepository $displayOrderRepository, Translator $translator)
    {
        $this->displayOrderRepository = $displayOrderRepository;
        $this->translator = $translator;
    }

    protected function addDisplayOrderToContext(DataClassDisplayOrderSupport $dataClass): bool
    {
        return $this->getDisplayOrderRepository()->addDisplayOrderToContext($dataClass);
    }

    protected function countOtherDisplayOrdersInContext(DataClassDisplayOrderSupport $dataClass): int
    {
        return $this->getDisplayOrderRepository()->countOtherDisplayOrdersInContext($dataClass);
    }

    protected function deleteDisplayOrderFromContext(DataClassDisplayOrderSupport $dataClass): bool
    {
        $displayOrderContextProperties = array_intersect_key(
            $dataClass->getDefaultProperties(), array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        return $this->getDisplayOrderRepository()->deleteDisplayOrderFromContext(
            $dataClass, $displayOrderContextProperties, $this->getDisplayOrderValue($dataClass)
        );
    }

    protected function deletePreviousDisplayOrderFromPreviousContext(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    ): bool
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function findNextDisplayOrderValue(DataClassDisplayOrderSupport $dataClass): int
    {
        return $this->getDisplayOrderRepository()->findNextDisplayOrderValue($dataClass);
    }

    /**
     * @return string[]
     */
    protected function findPreviousDisplayOrderPropertiesRecord(DataClassDisplayOrderSupport $dataClass): array
    {
        return $this->getDisplayOrderRepository()->findDisplayOrderPropertiesRecord($dataClass);
    }

    protected function getDisplayOrderContextAsString(DataClassDisplayOrderSupport $dataClass): string
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

    public function getDisplayOrderRepository(): DisplayOrderRepository
    {
        return $this->displayOrderRepository;
    }

    protected function getDisplayOrderValue(DataClassDisplayOrderSupport $dataClass): ?int
    {
        return $dataClass->getDefaultProperty($dataClass->getDisplayOrderPropertyName());
    }

    /**
     * @param string[] $displayOrderPropertiesRecord
     */
    protected function getDisplayOrderValueFromRecord(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    ): ?int
    {
        return (int) $displayOrderPropertiesRecord[$dataClass->getDisplayOrderPropertyName()];
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function handleAddedDataClassInContext(DataClassDisplayOrderSupport $dataClass): bool
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

    public function handleDisplayOrderAfterDelete(DataClassDisplayOrderSupport $dataClass): bool
    {
        return $this->deleteDisplayOrderFromContext($dataClass);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function handleDisplayOrderBeforeCreate(DataClassDisplayOrderSupport $dataClass): bool
    {
        $this->validateDisplayOrder($dataClass);

        return $this->handleAddedDataClassInContext($dataClass);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function handleDisplayOrderBeforeUpdate(DataClassDisplayOrderSupport $dataClass): bool
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

    protected function hasDisplayOrder(DataClassDisplayOrderSupport $dataClass): bool
    {
        return !is_null($this->getDisplayOrderValue($dataClass));
    }

    /**
     * @param string[] $displayOrderPropertiesRecord
     */
    protected function hasDisplayOrderChanged(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    ): bool
    {
        return $this->getDisplayOrderValue($dataClass) !=
            $this->getDisplayOrderValueFromRecord($dataClass, $displayOrderPropertiesRecord);
    }

    /**
     * @param string[] $displayOrderPropertiesRecord
     */
    protected function hasDisplayOrderContextChanged(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    ): bool
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
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function setDisplayOrderToNextValueInContext(DataClassDisplayOrderSupport $dataClass)
    {
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();
        $displayOrderValue = $this->findNextDisplayOrderValue($dataClass);

        $dataClass->setDefaultProperty($displayOrderPropertyName, $displayOrderValue);
    }

    /**
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
                ], StringUtilities::LIBRARIES
                )
            );
        }
    }
}
