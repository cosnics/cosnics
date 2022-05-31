<?php
namespace Chamilo\Libraries\Storage\DataManager\AdoDb\Service;

/**
 *
 * @package Chamilo\Libraries\Storage\DataManager\AdoDb\Processor
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RecordProcessor
{

    /**
     *
     * @param string $key
     * @param string[] $typeMap
     *
     * @return ?string
     */
    protected function determineFieldTypeFromMap(string $key, array $typeMap = []): ?string
    {
        return $typeMap[$key] ?? null;
    }

    /**
     * @param mixed $field
     * @param ?string $fieldType
     *
     * @return mixed
     */
    protected function processField($field, ?string $fieldType = null)
    {
        $field = $this->processResource($field);

        if ($fieldType)
        {
            settype($field, $fieldType);
        }

        return $field;
    }

    /**
     * @param string[] $record
     * @param string[] $typeMap
     *
     * @return string[]
     */
    public function processRecord(array $record, array $typeMap = []): array
    {
        foreach ($record as $key => &$field)
        {
            $field = $this->processField($field, $this->determineFieldTypeFromMap($key, $typeMap));
        }

        return $record;
    }

    /**
     * @param mixed $field
     */
    protected function processResource($field): string
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