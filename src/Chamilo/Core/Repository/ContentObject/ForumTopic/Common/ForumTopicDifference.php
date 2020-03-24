<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;

/**
 * @package Chamilo\Core\Repository\ContentObject\ForumTopic\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ForumTopicDifference extends ContentObjectDifference
{

    /**
     * @return string[]
     */
    public function getAdditionalPropertyNames()
    {
        return array(ForumTopic::PROPERTY_LOCKED);
    }
}
