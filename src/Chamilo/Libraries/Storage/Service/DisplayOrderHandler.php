<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException;
use Chamilo\Libraries\Storage\Architecture\Interface\DataClassDisplayOrderSupport;
use Chamilo\Libraries\Storage\Repository\DisplayOrderRepository;

/**
 * @package Chamilo\Libraries\Storage\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DisplayOrderHandler
{
    public function __construct(protected DisplayOrderRepository $displayOrderRepository)
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function addDisplayOrderToContext(DataClassDisplayOrderSupport $dataClass): void
    {
        $this->displayOrderRepository->addDisplayOrderToContext($dataClass);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function countOtherDisplayOrdersInContext(DataClassDisplayOrderSupport $dataClass): int
    {
        return $this->displayOrderRepository->countOtherDisplayOrdersInContext($dataClass);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function deleteDisplayOrderFromContext(DataClassDisplayOrderSupport $dataClass): void
    {
        $displayOrderContextProperties = array_intersect_key(
            $dataClass->getDefaultProperties(), array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        $this->displayOrderRepository->deleteDisplayOrderFromContext(
            $dataClass, $displayOrderContextProperties, $this->getDisplayOrderValue($dataClass)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function deletePreviousDisplayOrderFromPreviousContext(
        DataClassDisplayOrderSupport $dataClass, array $displayOrderPropertiesRecord
    ): void
    {
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();

        $displayOrderContextProperties = array_intersect_key(
            $displayOrderPropertiesRecord, array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        $this->displayOrderRepository->deleteDisplayOrderFromContext(
            $dataClass, $displayOrderContextProperties, $displayOrderPropertiesRecord[$displayOrderPropertyName]
        );
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function findNextDisplayOrderValue(DataClassDisplayOrderSupport $dataClass): int
    {
        return $this->displayOrderRepository->findNextDisplayOrderValue($dataClass);
    }

    /**
     * @return string[]
     *
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function findPreviousDisplayOrderPropertiesRecord(DataClassDisplayOrderSupport $dataClass): array
    {
        return $this->displayOrderRepository->findDisplayOrderPropertiesRecord($dataClass);
    }

    protected function getDisplayOrderContextAsString(DataClassDisplayOrderSupport $dataClass): string
    {
        $displayOrderContextProperties = array_intersect_key(
            $dataClass->getDefaultProperties(), array_flip($dataClass->getDisplayOrderContextPropertyNames())
        );

        $displayOrderContext = [];

        foreach ($displayOrderContextProperties as $displayOrderContextProperty => $displayOrderContextPropertyValue) {
            $displayOrderContext[] = $displayOrderContextProperty . ' = ' . $displayOrderContextPropertyValue;
        }

        return implode(', ', $displayOrderContext);
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

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function handleAddedDataClassInContext(DataClassDisplayOrderSupport $dataClass): void
    {
        if ($this->hasDisplayOrder($dataClass)) {
            $this->addDisplayOrderToContext($dataClass);
        }
        else {
            $this->setDisplayOrderToNextValueInContext($dataClass);
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function handleDisplayOrderAfterDelete(DataClassDisplayOrderSupport $dataClass): void
    {
        $this->deleteDisplayOrderFromContext($dataClass);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function handleDisplayOrderBeforeCreate(DataClassDisplayOrderSupport $dataClass): void
    {
        $this->validateDisplayOrder($dataClass);
        $this->handleAddedDataClassInContext($dataClass);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    public function handleDisplayOrderBeforeUpdate(DataClassDisplayOrderSupport $dataClass): bool
    {
        $displayOrderPropertiesRecord = $this->findPreviousDisplayOrderPropertiesRecord($dataClass);

        $hasDisplayOrderContextChanged =
            $this->hasDisplayOrderContextChanged($dataClass, $displayOrderPropertiesRecord);
        $hasDisplayOrderChanged = $this->hasDisplayOrderChanged($dataClass, $displayOrderPropertiesRecord);

        if ($hasDisplayOrderContextChanged || $hasDisplayOrderChanged) {
            $this->validateDisplayOrder($dataClass);
            $this->deletePreviousDisplayOrderFromPreviousContext($dataClass, $displayOrderPropertiesRecord);
            $this->handleAddedDataClassInContext($dataClass);
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
        foreach ($dataClass->getDisplayOrderContextPropertyNames() as $propertyName) {
            if ($dataClass->getDefaultProperty($propertyName) != $displayOrderPropertiesRecord[$propertyName]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageNoResultException
     */
    protected function setDisplayOrderToNextValueInContext(DataClassDisplayOrderSupport $dataClass): static
    {
        $displayOrderPropertyName = $dataClass->getDisplayOrderPropertyName();
        $displayOrderValue = $this->findNextDisplayOrderValue($dataClass);

        $dataClass->setDefaultProperty($displayOrderPropertyName, $displayOrderValue);

        return $this;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\DisplayOrderException
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    protected function validateDisplayOrder(DataClassDisplayOrderSupport $dataClass): static
    {
        $displayOrder = $this->getDisplayOrderValue($dataClass);
        $numberOfOtherDisplayOrdersInContext = $this->countOtherDisplayOrdersInContext($dataClass);

        $hasDisplayOrder = $this->hasDisplayOrder($dataClass);
        $displayOrderTooLow = $displayOrder < 1;
        $displayOrderTooHigh = $displayOrder > ($numberOfOtherDisplayOrdersInContext + 1);

        if ($hasDisplayOrder && ($displayOrderTooLow || $displayOrderTooHigh)) {
            throw new DisplayOrderException(
                get_class($dataClass), $dataClass->getId(), $this->getDisplayOrderContextAsString($dataClass),
                $displayOrder, $numberOfOtherDisplayOrdersInContext
            );
        }

        return $this;
    }
}
