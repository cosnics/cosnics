<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Storage\DataClass
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
class Publication extends DataClass
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_ENTITY_TYPE = 'entity_type';
    public const PROPERTY_PUBLICATION_ID = 'publication_id';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            [
                self::PROPERTY_PUBLICATION_ID,
                self::PROPERTY_ENTITY_TYPE
            ]
        );
    }

    /**
     * @return int
     */
    public function getEntityType()
    {
        return $this->getDefaultProperty(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     * @return int
     */
    public function getPublicationId()
    {
        return $this->getDefaultProperty(self::PROPERTY_PUBLICATION_ID);
    }

    public static function getStorageUnitName(): string
    {
        return 'weblcms_assignment_publication';
    }

    /**
     * @param int $entityType
     */
    public function setEntityType($entityType)
    {
        $this->setDefaultProperty(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     * @param int $publicationId
     */
    public function setPublicationId($publicationId)
    {
        $this->setDefaultProperty(self::PROPERTY_PUBLICATION_ID, $publicationId);
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
}

