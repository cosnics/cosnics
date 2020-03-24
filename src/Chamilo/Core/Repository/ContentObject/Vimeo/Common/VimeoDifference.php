<?php
namespace Chamilo\Core\Repository\ContentObject\Vimeo\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * @package Chamilo\Core\Repository\ContentObject\Vimeo\Common
 *
 * @author Shoira Mukhsinova
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VimeoDifference extends ContentObjectDifference
{
    const VIDEO_URL = 'video_url';

    /**
     * @return string[]
     */
    public function getAdditionalPropertyNames()
    {
        return array(self::VIDEO_URL);
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
            case self::VIDEO_URL:
                $content = $contentObject->get_video_url();
                break;
            default:
                $content = parent::getVisualAdditionalPropertyValue($contentObject, $propertyName);
        }

        return explode(PHP_EOL, $content);
    }
}
