<?php
namespace Chamilo\Application\Survey\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Application\Survey\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublicationEntityRelation extends DataClass
{
    
    // Properties
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_ENTITY_TYPE = 'entity_type';
    const PROPERTY_ENTITY_ID = 'entity_id';
    const PROPERTY_RIGHTS = 'rights';

    /**
     *
     * @var \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    private $publication;

    /**
     *
     * @return string[]
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PUBLICATION_ID, 
                self::PROPERTY_ENTITY_TYPE, 
                self::PROPERTY_ENTITY_ID, 
                self::PROPERTY_RIGHTS));
    }

    /**
     *
     * @return integer
     */
    public function getPublicationId()
    {
        return $this->get_default_property(self::PROPERTY_PUBLICATION_ID);
    }

    /**
     *
     * @return \Chamilo\Application\Survey\Storage\DataClass\Publication
     */
    public function getPublication()
    {
        if (! isset($this->publication))
        {
            $this->publication = DataManager::retrieve_by_id(Publication::class_name(), $this->get_publication());
        }
        
        return $this->publication;
    }

    /**
     *
     * @param integer $publicationId
     */
    public function setPublicationId($publicationId)
    {
        $this->set_default_property(self::PROPERTY_PUBLICATION_ID, $publicationId);
    }

    /**
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_TYPE);
    }

    /**
     *
     * @param string $entityType
     */
    public function setEntityType($entityType)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_TYPE, $entityType);
    }

    /**
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->get_default_property(self::PROPERTY_ENTITY_ID);
    }

    /**
     *
     * @param integer $entityId
     */
    public function setEntityId($entityId)
    {
        $this->set_default_property(self::PROPERTY_ENTITY_ID, $entityId);
    }

    /**
     *
     * @return integer
     */
    public function getRights()
    {
        return $this->get_default_property(self::PROPERTY_RIGHTS);
    }

    /**
     *
     * @param integer $rights
     */
    public function setRights($rights)
    {
        $this->set_default_property(self::PROPERTY_RIGHTS, $rights);
    }
}