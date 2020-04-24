<?php
namespace Chamilo\Libraries\Storage\DataManager\Doctrine\Processor;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\Doctrine\Processor
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class RecordProcessor
{

    /**
     *
     * @param string $key
     * @param string[] $typeMap
     *
     * @return NULL|string
     */
    protected function determineFieldTypeFromMap($key, $typeMap = array())
    {
        return isset($typeMap[$key]) ? $typeMap[$key] : null;
    }

    /**
     *
     * @param mixed $field
     * @param string $fieldType
     *
     * @return mixed
     */
    protected function processField($field, $fieldType = null)
    {
        $field = $this->processResource($field);

        if ($fieldType)
        {
            $field = settype($field, $fieldType);
        }

        return $field;
    }

    /**
     * Processes a given record by transforming to the correct type
     *
     * @param string[] $record
     * @param string[] $typeMap
     *
     * @return string[]
     */
    public function processRecord($record, $typeMap = array())
    {
        foreach ($record as $key => &$field)
        {
            $field = $this->processField($field, $this->determineFieldTypeFromMap($key, $typeMap));
        }

        return $record;
    }

    /**
     *
     * @param mixed $field
     *
     * @return string
     */
    protected function processResource($field)
    {
        if (is_resource($field))
        {
            $data = '';

            while (!feof($field))
            {
                $data .= fread($field, 1024);
            }

            $field = $data;
        }

        return $field;
    }
}