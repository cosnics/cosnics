<?php
namespace Chamilo\Libraries\Platform\Security;

/**
 * Validates the given serialized data to make sure that only the allowed classes are embedded in the data and the
 * data is safe to unserialize
 *
 * @package Chamilo\Libraries\Platform\Security
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class SerializedDataValidator
{
    /**
     * @param string $serializedData
     * @param array $allowedClasses
     */
    public static function validateSerializedData(string $serializedData, array $allowedClasses = [])
    {
        $matches = [];
        preg_match_all('/O:\d*:"([^"]*)"/', $serializedData, $matches);

        foreach($matches[1] as $objectClassName)
        {
            if(!in_array($objectClassName, $allowedClasses))
            {
                throw new \RuntimeException('[SECURITY] Could not validate the serialized data');
            }
        }
    }
}
