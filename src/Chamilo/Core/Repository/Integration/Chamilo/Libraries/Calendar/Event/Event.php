<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Libraries\Calendar\Event;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 *
 * @package core\repository\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Event extends \Chamilo\Libraries\Calendar\Event\Event implements ContentObjectSupport
{

    /**
     *
     * @var ContentObject
     */
    private $content_object;

    public function get_content_object()
    {
        return $this->content_object;
    }

    public function set_content_object(ContentObject $content_object)
    {
        $this->content_object = $content_object;
    }
}
