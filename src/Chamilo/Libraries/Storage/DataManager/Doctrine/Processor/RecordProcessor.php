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
     * Processes a given record by transforming to the correct type
     *
     * @param mixed[] $record
     * @return mixed[]
     */
    public function processRecord($record, $encoding = 'UTF-8', $typeMap = array())
    {
        foreach ($record as $key => &$field)
        {
            $field = $this->processField($field, $this->determineFieldTypeFromMap($key, $typeMap), $encoding);
        }

        return $record;
    }

    /**
     *
     * @param string $key
     * @param string[] $typeMap
     * @return NULL|string
     */
    protected function determineFieldTypeFromMap($key, $typeMap = array())
    {
        return isset($typeMap[$key]) ? $typeMap[$key] : null;
    }

    /**
     *
     * @param mixed $field
     * @return mixed
     */
    protected function processField($field, $fieldType = null, $encoding = 'UTF-8')
    {
        $field = $this->processResource($field);
        $field = $this->processEncoding($field, $encoding);

        if ($fieldType)
        {
            $field = settype($field, $fieldType);
        }

        return $field;
    }

    /**
     *
     * @param mixed $field
     * @return string
     */
    protected function processResource($field)
    {
        if (is_resource($field))
        {
            $data = '';

            while (! feof($field))
            {
                $data .= fread($field, 1024);
            }

            $field = $data;
        }

        return $field;
    }

    /**
     *
     * @param mixed $field
     * @return string
     */
    protected function processEncoding($field, $encoding = 'UTF-8')
    {
        if ($encoding !== 'UTF-8' && is_string($field) && ! is_numeric($field) && ! mb_check_encoding($field, 'UTF-8'))
        {
            $field = trim(mb_convert_encoding($field, 'UTF-8', $encoding));
        }

        return $field;
    }
}