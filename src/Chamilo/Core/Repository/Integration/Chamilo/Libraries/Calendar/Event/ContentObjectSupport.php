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
interface ContentObjectSupport
{

    /**
     *
     * @return ContentObject
     */
    public function get_content_object();

    /**
     *
     * @param ContentObject $content_object
     */
    public function set_content_object(ContentObject $content_object);
}