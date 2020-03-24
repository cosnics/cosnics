<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package Chamilo\Core\Repository\ContentObject\Webpage\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class WebpageDifference extends ContentObjectDifference
{
    const PAGE_PROPERTIES = 'page_properties';

    /**
     * @return string[]
     */
    public function getAdditionalPropertyNames()
    {
        return array(self::PAGE_PROPERTIES);
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
            case self::PAGE_PROPERTIES:
                $content = $contentObject->get_filename() . ' (' .
                    number_format($contentObject->get_filesize() / 1024, 2, '.', '') . ' kb)';
                break;
            default:
                $content = parent::getVisualAdditionalPropertyValue($contentObject, $propertyName);
        }

        return explode(PHP_EOL, $content);
    }
}
