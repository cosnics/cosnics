<?php
namespace Chamilo\Libraries\Storage\DataClass;

/**
 * @package Chamilo\Libraries\Storage\DataClass
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PropertyMapper
{
    /**
     *
     * @param string[][] $records
     * @param string $propertyName
     *
     * @return string[][]
     */
    public function mapRecordsByProperty($records, $propertyName)
    {
        $mappedRecords = array();

        foreach ($records as $record)
        {
            $propertyValue = $record[$propertyName];

            if (isset($propertyValue))
            {
                $mappedRecords[$propertyValue] = $record;
            }
        }

        return $mappedRecords;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[] $dataClasses
     * @param string $propertyName
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function mapDataClassByProperty($dataClasses, $propertyName)
    {
        $mappedDataClasses = array();

        foreach ($dataClasses as $dataClass)
        {
            $propertyValue = $dataClass->get_default_property($propertyName);

            if (isset($propertyValue))
            {
                $mappedDataClasses[$propertyValue] = $dataClass;
            }
        }

        return $mappedDataClasses;
    }

    /**
     *
     * @param string[][] $records
     * @param string $propertyName
     *
     * @return string[]
     */
    public function groupRecordsByProperty($records, $propertyName)
    {
        $mappedRecords = array();

        foreach ($records as $record)
        {
            if (array_key_exists($propertyName, $record))
            {
                if (!array_key_exists($record[$propertyName], $mappedRecords))
                {
                    $mappedRecords[$record[$propertyName]] = array();
                }

                $mappedRecords[$record[$propertyName]][] = $record;
            }
        }

        return $mappedRecords;
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass[] $dataClasses
     * @param string $propertyName
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass[]
     */
    public function groupDataClassByProperty($dataClasses, $propertyName)
    {
        $mappedDataClasses = array();

        foreach ($dataClasses as $dataClass)
        {
            if (in_array($propertyName, $dataClass->get_default_property_names()))
            {
                if (!array_key_exists($dataClass->get_default_property($propertyName), $mappedDataClasses))
                {
                    $mappedDataClasses[$dataClass->get_default_property($propertyName)] = array();
                }

                $mappedDataClasses[$dataClass->get_default_property($propertyName)][] = $dataClass;
            }
        }

        return $mappedDataClasses;
    }
}