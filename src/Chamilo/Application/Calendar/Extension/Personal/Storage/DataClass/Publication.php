<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass;

/**
 *
 * @package Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Dieter De Neef
 */
class Publication extends \Chamilo\Core\Repository\Publication\Storage\DataClass\Publication
{

    // Properties
    const PROPERTY_PUBLISHED = 'published';

    const PROPERTY_PUBLISHER = 'publisher_id';

    /**
     * Get the default properties of all Publications.
     *
     * @return string[] The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(array(self::PROPERTY_PUBLISHER, self::PROPERTY_PUBLISHED));
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function get_publication_object()
    {
        return parent::getContentObject();
    }

    /**
     * @return integer
     */
    public function get_published()
    {
        return $this->get_default_property(self::PROPERTY_PUBLISHED);
    }

    /**
     * @return integer
     */
    public function get_publisher()
    {
        return $this->get_default_property(self::PROPERTY_PUBLISHER);
    }

    /**
     * @param integer $published
     */
    public function set_published($published)
    {
        $this->set_default_property(self::PROPERTY_PUBLISHED, $published);
    }

    /**
     * @param integer $publisher
     */
    public function set_publisher($publisher)
    {
        $this->set_default_property(self::PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'calendar_personal_publication';
    }
}
