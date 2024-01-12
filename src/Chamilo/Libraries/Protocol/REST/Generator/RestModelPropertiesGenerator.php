<?php

namespace Chamilo\Libraries\Protocol\REST\Generator;

/**
 * Class RestModelPropertiesGenerator
 *
 * @package Chamilo\Libraries\Protocol\REST\Model
 */
class RestModelPropertiesGenerator
{
    /**
     * @param array $result
     *
     * @return RestModelProperty[]
     */
    public function generatePropertiesFromArray(array $result)
    {
        $properties = [];

        foreach($result as $key => $value)
        {
            $name = $this->normalizePropertyName($key);

            if(is_array($value))
            {
                $keys = array_keys($value);
                $keysContainString = false;
                foreach($keys as $key)
                {
                    if(is_string($key))
                    {
                        $keysContainString = true;
                        break;
                    }
                }
            }
            $isArray = is_array($value) && !$keysContainString;
            if ($isArray) $value = $value[0];

            $type = $this->determineType($value);

            if ($type == "object")
            {
                $value = $this->generatePropertiesFromArray($value);
            }

            $properties[] = new RestModelProperty($name, $type, $isArray, is_null($value), $value);
        }
        return $properties;
    }

    protected function normalizePropertyName(string $propertyName): string
    {
        $normalizedPropertyName = '';

        $parts = explode('_', $propertyName);

        foreach($parts as $part)
        {
            $normalizedPropertyName .= ucfirst($part);
        }

        return lcfirst($normalizedPropertyName);
    }

    protected function determineType($value)
    {
        $type = "string";

        if(is_int($value))
        {
            $type = "int";
        }
        elseif(is_bool($value))
        {
            $type = 'bool';
        }
        elseif(is_array($value))
        {
            $type = 'object';
        }

        return $type;
    }


}
