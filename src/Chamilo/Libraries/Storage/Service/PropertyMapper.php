<?php
namespace Chamilo\Libraries\Storage\Service;

use Doctrine\Common\Collections\ArrayCollection;
use stdClass;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PropertyMapper
{
    /**
     * @param string[] $propertyNames
     *
     * @return bool|string
     */
    public function determineClassKeyValue(stdClass $class, array $propertyNames)
    {
        $keyValue = $class;
        $numberOfProperties = count($propertyNames);

        foreach ($propertyNames as $count => $propertyName)
        {
            if ($keyValue instanceof stdClass)
            {
                if (isset($keyValue->$propertyName))
                {
                    if ($count < ($numberOfProperties - 1))
                    {
                        $keyValue = $keyValue->$propertyName;
                    }
                    else
                    {
                        return $keyValue->$propertyName;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        return false;
    }

    /**
     * @param \stdClass[] $dataClasses
     *
     * @return \stdClass[][]
     */
    public function groupClassByProperty(array $dataClasses, string $propertyName): array
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            if (isset($dataClass->$propertyName))
            {
                $propertyValue = $dataClass->$propertyName;

                if (isset($propertyValue) && $propertyValue !== '')
                {
                    if (!array_key_exists($dataClass->$propertyName, $mappedDataClasses))
                    {
                        $mappedDataClasses[$dataClass->$propertyName] = [];
                    }

                    $mappedDataClasses[$dataClass->$propertyName][] = $dataClass;
                }
            }
        }

        return $mappedDataClasses;
    }

    /**
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[]|\Doctrine\Common\Collections\ArrayCollection $dataClasses
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[][]
     */
    public function groupDataClassByProperty($dataClasses, string $propertyName): array
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            if (in_array($propertyName, $dataClass::getDefaultPropertyNames()))
            {
                $propertyValue = $dataClass->getDefaultProperty($propertyName);

                if (isset($propertyValue) && $propertyValue !== '')
                {
                    if (!array_key_exists($dataClass->getDefaultProperty($propertyName), $mappedDataClasses))
                    {
                        $mappedDataClasses[$dataClass->getDefaultProperty($propertyName)] = [];
                    }

                    $mappedDataClasses[$dataClass->getDefaultProperty($propertyName)][] = $dataClass;
                }
            }
        }

        return $mappedDataClasses;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Libraries\Storage\DataClass\DataClass> $dataClasses
     * @param string $methodName
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[][]
     */
    public function groupDataClassCollectionByMethod(ArrayCollection $dataClasses, string $methodName): array
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            $groupValue = $dataClass->$methodName();

            if ($groupValue)
            {
                $mappedDataClasses[$groupValue][] = $dataClass;
            }
        }

        return $mappedDataClasses;
    }

    /**
     * @param string[][]|ArrayCollection $records
     *
     * @return string[][]|ArrayCollection
     */
    public function groupRecordsByProperty(array|ArrayCollection $records, string $propertyName): array|ArrayCollection
    {
        $mappedRecords = [];

        foreach ($records as $record)
        {
            if (array_key_exists($propertyName, $record))
            {
                if ($record[$propertyName])
                {
                    if (!array_key_exists($record[$propertyName], $mappedRecords))
                    {
                        $mappedRecords[$record[$propertyName]] = [];
                    }

                    $mappedRecords[$record[$propertyName]][] = $record;
                }
            }
        }

        if ($records instanceof ArrayCollection)
        {
            $mappedRecords = new ArrayCollection($mappedRecords);
        }

        return $mappedRecords;
    }

    /**
     * @param \stdClass[] $classes
     * @param string[] $propertyNames
     *
     * @return \stdClass[]
     */
    public function mapClassByProperties(array $classes, array $propertyNames): array
    {
        $mappedClasses = [];

        foreach ($classes as $class)
        {
            $keyValue = $this->determineClassKeyValue($class, $propertyNames);

            if ($keyValue)
            {
                $mappedClasses[$keyValue] = $class;
            }
        }

        return $mappedClasses;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\Chamilo\Libraries\Storage\DataClass\DataClass[] $dataClasses
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]|ArrayCollection
     */
    public function mapDataClassByMethod(array|ArrayCollection $dataClasses, string $methodName): array|ArrayCollection
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            $mapValue = $dataClass->$methodName();

            if ($mapValue)
            {
                $mappedDataClasses[$mapValue] = $dataClass;
            }
        }

        if ($dataClasses instanceof ArrayCollection)
        {
            $mappedDataClasses = new ArrayCollection($mappedDataClasses);
        }

        return $mappedDataClasses;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\Chamilo\Libraries\Storage\DataClass\DataClass[] $dataClasses
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]|ArrayCollection
     */
    public function mapDataClassByProperty(array|ArrayCollection $dataClasses, string $propertyName
    ): array|ArrayCollection
    {
        $mappedDataClasses = [];

        foreach ($dataClasses as $dataClass)
        {
            $propertyValue = $dataClass->getDefaultProperty($propertyName);

            if (isset($propertyValue) && $propertyValue !== '')
            {
                $mappedDataClasses[$propertyValue] = $dataClass;
            }
        }

        if ($dataClasses instanceof ArrayCollection)
        {
            $mappedDataClasses = new ArrayCollection($mappedDataClasses);
        }

        return $mappedDataClasses;
    }

    /**
     * @param string[][]|ArrayCollection $records
     *
     * @return string[][]|ArrayCollection
     */
    public function mapRecordsByProperty(array|ArrayCollection $records, string $propertyName): array|ArrayCollection
    {
        $mappedRecords = [];

        foreach ($records as $record)
        {
            $propertyValue = $record[$propertyName];

            if (isset($propertyValue) && $propertyValue !== '')
            {
                $mappedRecords[$propertyValue] = $record;
            }
        }

        if ($records instanceof ArrayCollection)
        {
            $mappedRecords = new ArrayCollection($mappedRecords);
        }

        return $mappedRecords;
    }
}