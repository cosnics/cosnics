<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package Chamilo\Core\Repository\ContentObject\File\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FileDifference extends ContentObjectDifference
{

    const FILE_PROPERTIES = 'file_properties';

    /**
     * @return string[]
     */
    public function getAdditionalPropertyNames()
    {
        return array(self::FILE_PROPERTIES);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $propertyName
     *
     * @return string[]
     */
    public function getVisualAdditionalPropertyValue(ContentObject $contentObject, string $propertyName)
    {
        switch ($propertyName)
        {
            case self::FILE_PROPERTIES:
                $content = $contentObject->get_filename() . ' (' .
                    number_format($contentObject->get_filesize() / 1024, 2, '.', '') . ' kb)';
                break;
            default:
                $content = parent::getVisualAdditionalPropertyValue($contentObject, $propertyName);
        }

        return explode(PHP_EOL, $content);
    }
}
