<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Home\Repository\ContentObjectPublicationRepository;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\Publication\LocationSupport;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Storage\Repository\PublicationRepository;
use Chamilo\Libraries\Platform\Translation;

class Manager implements PublicationInterface
{
    /**
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
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::is_content_object_editable()
     */
    public static function is_content_object_editable($object_id)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::content_object_is_published()
     */
    public static function content_object_is_published($object_id)
    {
        return self::getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectId(
            $object_id
        ) > 0;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::any_content_object_is_published()
     */
    public static function any_content_object_is_published($object_ids)
    {
        return self::getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectIds(
            $object_ids
        ) > 0;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attributes()
     */
    public static function get_content_object_publication_attributes(
        $object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null,
        $offset = null, $order_properties = null
    )
    {
        $publicationAttributes = array();

        switch ($type)
        {
            case PublicationInterface :: ATTRIBUTES_TYPE_OBJECT :
                $publications =
                    self::getContentObjectPublicationService()->getContentObjectPublicationsByContentObjectId(
                        $object_id
                    );
                break;
            case PublicationInterface :: ATTRIBUTES_TYPE_USER :
                $publications =
                    self::getContentObjectPublicationService()->getContentObjectPublicationsByContentObjectOwnerId(
                        $object_id
                    );
                break;
            default :
                return array();
        }

        foreach($publications as $publication)
        {
            $publicationAttributes[] = self::createPublicationAttributesFromPublication($publication);
        }

        return $publicationAttributes;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::count_publication_attributes()
     */
    public static function count_publication_attributes(
        $attributes_type = self::ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null
    )
    {
        switch ($attributes_type)
        {
            case PublicationInterface :: ATTRIBUTES_TYPE_OBJECT :
                return self::getContentObjectPublicationService()->countContentObjectPublicationsByContentObjectIds(
                    $identifier
                );
            case PublicationInterface :: ATTRIBUTES_TYPE_USER :
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
        $attributes = new \Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes();

        $attributes->setId($publication->getId());
        $attributes->set_publisher_id($publication->getContentObject()->get_owner_id());
        $attributes->set_date($publication->getContentObject()->get_creation_date());
        $attributes->set_application(\Chamilo\Core\Home\Manager::context());
        $attributes->set_location(Translation:: get('TypeName', null, \Chamilo\Core\Home\Manager:: context()));
        $attributes->set_url('index.php');

        $attributes->set_title($publication->getContentObject()->get_title());
        $attributes->set_content_object_id($publication->get_content_object_id());

        return $attributes;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publications()
     */
    public static function delete_content_object_publications($object_id)
    {
        self::getContentObjectPublicationService()->deleteContentObjectPublicationsByContentObjectId(
            $object_id
        );

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
    public static function get_content_object_publication_locations($content_object, $user = null)
    {
        return null;
    }

    /*
     * Publication from the repository is not possible due to several home blocks being different
     */
    public static function get_allowed_content_object_types()
    {
        return array();
    }

    /*
     * Publication from the repository is not possible due to several home blocks being different
     */
    public static function publish_content_object(
        \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object, LocationSupport $location,
        $options = array()
    )
    {
        return false;
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */
    public static function add_publication_attributes_elements($form)
    {
    }
}
