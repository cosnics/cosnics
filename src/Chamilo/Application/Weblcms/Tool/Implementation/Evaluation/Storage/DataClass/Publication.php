<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Evaluation\Storage\DataClass
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Publication extends DataClass
{
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_OPEN_FOR_STUDENTS = 'open_for_students';
    const PROPERTY_SELF_EVALUATION_ALLOWED = 'self_evaluation_allowed';

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
                self::PROPERTY_OPEN_FOR_STUDENTS,
                self::PROPERTY_SELF_EVALUATION_ALLOWED
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

    /**
     *
     * @return int
     */
    public function getEntityType(): int
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param int $entityType
     */
    public function setEntityType(int $entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @return bool
     */
    public function getOpenForStudents(): bool
    {
        return $this->get_default_property(self::PROPERTY_OPEN_FOR_STUDENTS);
    }

    /**
     * @param bool $openForStudents
     */
    public function setOpenForStudents(bool $openForStudents)
    {
        $this->set_default_property(self::PROPERTY_OPEN_FOR_STUDENTS, $openForStudents);
    }

    /**
     * @return bool
     */
    public function getSelfEvaluationAllowed(): bool
    {
        return $this->get_default_property(self::PROPERTY_SELF_EVALUATION_ALLOWED);
    }

    /**
     * @param bool $selfEvaluationAllowed
     */
    public function setSelfEvaluationAllowed(bool $selfEvaluationAllowed)
    {
        $this->set_default_property(self::PROPERTY_SELF_EVALUATION_ALLOWED, $selfEvaluationAllowed);
    }

    public static function get_table_name()
    {
        return 'weblcms_evaluation_publication';
    }
}

