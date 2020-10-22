<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Publication extends \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass\Publication
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_CHECK_FOR_PLAGIARISM = 'check_for_plagiarism';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PUBLICATION_ID,
                self::PROPERTY_ENTITY_TYPE,
                self::PROPERTY_CHECK_FOR_PLAGIARISM
            )
        );
    }

    /**
     * @return int
     */
    public function getPublicationId()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     * @param int $publicationId
     */
    public function setPublicationId($publicationId)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publicationId);
    }

    /**
     * Old method for the course copier
     *
     * @param int $publicationId
     */
    public function set_publication_id($publicationId)
    {
        $this->setPublicationId($publicationId);
    }

    /**
     *
     * @return int
     */
    public function getEntityType()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param int $entityType
     */
    public function setEntityType($entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @return bool
     */
    public function getCheckForPlagiarism()
    {
        return $this->get_default_property(self::PROPERTY_CHECK_FOR_PLAGIARISM);
    }

    /**
     * @param bool $checkForPlagiarism
     */
    public function setCheckForPlagiarism(bool $checkForPlagiarism)
    {
        $this->set_default_property(self::PROPERTY_CHECK_FOR_PLAGIARISM, $checkForPlagiarism);
    }

    public static function get_table_name()
    {
        return 'weblcms_assignment_publication';
    }
}

