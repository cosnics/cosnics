<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\GradeBook\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Publication extends DataClass
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PUBLICATION_ID
            )
        );
    }

    /**
     * @return int
     */
    public function getPublicationId(): int
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * @param int $publicationId
     */
    public function setPublicationId(int $publicationId)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publicationId);
    }

    /**
     * Old method for the course copier
     *
     * @param int $publicationId
     */
    public function set_publication_id(int $publicationId)
    {
        $this->setPublicationId($publicationId);
    }

    public static function get_table_name()
    {
        return 'weblcms_gradebook_publication';
    }
}

