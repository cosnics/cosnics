<?php

namespace Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication\Service\PublicationModifier;
use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Translation\Translation;

class Manager implements PublicationInterface
{

    /**
     *
     * @var ContentObjectPublicationService
     */
    protected static $contentObjectPublicationService;

    /**
     * Returns the content object publication service
     *
     * @return ContentObjectPublicationService
     */
    public static function getContentObjectPublicationService()
    {
        if (!isset(self::$contentObjectPublicationService))
        {
            self::$contentObjectPublicationService = new ContentObjectPublicationService(
                new ContentObjectPublicationRepository(new PublicationRepository())
            );
        }

        return self::$contentObjectPublicationService;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::canContentObjectBeEdited()
     */
    public static function canContentObjectBeEdited($object_id)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::isContentObjectPublished()
     */
    public static function isContentObjectPublished($object_id)
    {
        return self::getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectId($object_id) >
            0;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::areContentObjectsPublished()
     */
    public static function areContentObjectsPublished($object_ids)
    {
        return self::getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectIds(
                $object_ids
            ) > 0;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::getContentObjectPublicationsAttributes()
     */
    public static function getContentObjectPublicationsAttributes(
        $object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null, $offset = null,
        $order_properties = null
    )
    {
        $publicationAttributes = [];

        switch ($type)
        {
            case PublicationInterface::ATTRIBUTES_TYPE_OBJECT :
                $publications =
                    self::getContentObjectPublicationService()->getContentObjectPublicationsByContentObjectId(
                        $object_id
                    );
                break;
            case PublicationInterface::ATTRIBUTES_TYPE_USER :
                $publications =
                    self::getContentObjectPublicationService()->getContentObjectPublicationsByContentObjectOwnerId(
                        $object_id
                    );
                break;
            default :
                return [];
        }

        foreach ($publications as $publication)
        {
            $publicationAttributes[] = self::createPublicationAttributesFromPublication($publication);
        }

        return $publicationAttributes;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::countPublicationAttributes()
     */
    public static function countPublicationAttributes(
        $attributes_type = self::ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null
    )
    {
        switch ($attributes_type)
        {
            case PublicationInterface::ATTRIBUTES_TYPE_OBJECT :
                return self::getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectIds(
                    $identifier
                );
            case PublicationInterface::ATTRIBUTES_TYPE_USER :
                return self::getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectOwnerId(
                    $identifier
                );
            default :
                return 0;
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */
    public static function get_content_object_publication_attribute($publication_id)
    {
        $publication = self::getContentObjectPublicationService()->getContentObjectPublicationById($publication_id);

        return self::createPublicationAttributesFromPublication($publication);
    }

    /**
     * Creates a publication attributes object from a given record
     *
     * @param ContentObjectPublication $publication
     *
     * @return \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes
     */
    protected static function createPublicationAttributesFromPublication(ContentObjectPublication $publication)
    {
        $attributes = new Attributes();

        $attributes->setId($publication->getId());
        $attributes->set_publisher_id($publication->getContentObject()->get_owner_id());
        $attributes->set_date($publication->getContentObject()->get_creation_date());
        $attributes->set_application(\Chamilo\Core\Home\Manager::context());
        $attributes->set_location(Translation::get('TypeName', null, \Chamilo\Core\Home\Manager::context()));
        $attributes->set_url('index.php');

        $attributes->set_title($publication->getContentObject()->get_title());
        $attributes->set_content_object_id($publication->get_content_object_id());
        $attributes->setModifierServiceIdentifier(PublicationModifier::class);

        return $attributes;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::deleteContentObjectPublications()
     */
    public static function deleteContentObjectPublications($object_id)
    {
        self::getContentObjectPublicationService()->deleteContentObjectPublicationsByContentObjectId($object_id);

        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publication()
     */
    public static function delete_content_object_publication($publication_id)
    {
        self::getContentObjectPublicationService()->deleteContentObjectPublicationById($publication_id);

        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::update_content_object_publication_id()
     */
    public static function update_content_object_publication_id($publication_attributes)
    {
        $publication = self::getContentObjectPublicationService()->getContentObjectPublicationById(
            $publication_attributes->get_id()
        );

        if ($publication instanceof ContentObjectPublication)
        {
            $publication->set_content_object_id($publication_attributes->get_content_object_id());

            return $publication->update();
        }
        else
        {
            return false;
        }
    }

    /*
     * Publication from the repository is not possible due to several home blocks being different
     */
    public static function get_allowed_content_object_types()
    {
        return [];
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */
    public static function add_publication_attributes_elements($form)
    {
    }
}
