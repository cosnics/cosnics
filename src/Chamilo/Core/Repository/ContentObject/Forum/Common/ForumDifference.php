<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;

/**
 * @package Chamilo\Core\Repository\ContentObject\Forum\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ForumDifference extends ContentObjectDifference
{
    /**
     * @return string[]
     */
    public function getAdditionalPropertyNames()
    {
        return array(Forum::PROPERTY_LOCKED);
    }
}
