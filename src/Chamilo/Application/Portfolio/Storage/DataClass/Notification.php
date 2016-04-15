<?php
namespace Chamilo\Application\Portfolio\Storage\DataClass;

/**
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Notification extends \Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Notification
{
    // Properties
    const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     * Get the default properties of all feedback
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_PUBLICATION_ID));
    }

    /**
     *
     * @return int
     */
    public function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    /**
     *
     * @param int $content_object_id
     */
    public function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }
}