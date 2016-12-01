<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 *
 * @package Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event extends \Chamilo\Libraries\Calendar\Event\Event implements ContentObjectSupport
{

    /**
     *
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     *
     * @see \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\ContentObjectSupport::getContentObject()
     */
    public function getContentObject()
    {
        return $this->contentObject;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event\ContentObjectSupport::setContentObject()
     */
    public function setContentObject(ContentObject $contentObject)
    {
        $this->contentObject = $contentObject;
    }
}
