<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\ContentObject\LearningPath\Integration\Chamilo\Core\Repository\Publication\Service\PublicationService;
use Chamilo\Core\Repository\Publication\PublicationInterface;
use Chamilo\Core\Repository\Publication\Storage\DataClass\Attributes;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Exception;

class Manager implements PublicationInterface
{

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::canContentObjectBeEdited()
     */
    public static function add_publication_attributes_elements($form)
    {
    }

    public static function areContentObjectsPublished($object_ids)
    {
        return self::getPublicationService()->areContentObjectsPublished($object_ids);
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::areContentObjectsPublished()
     */

    public static function canContentObjectBeEdited($object_id)
    {
        return true;
    }

    public static function countPublicationAttributes($attributes_type = null, $identifier = null, $condition = null)
    {
        if ($attributes_type == self::ATTRIBUTES_TYPE_OBJECT)
        {
            return self::getPublicationService()->countContentObjectPublicationAttributesForContentObject(
                $identifier
            );
        }
        else
        {
            return self::getPublicationService()->countContentObjectPublicationAttributesForUser(
                $identifier
            );
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::get_content_object_publication_attribute()
     */

    public static function deleteContentObjectPublications($object_id)
    {
        try
        {
            self::getPublicationService()->deleteContentObjectPublicationsByObjectId($object_id);

            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::countPublicationAttributes()
     */

    public static function delete_content_object_publication($publication_id)
    {
        try
        {
            self::getPublicationService()->deleteContentObjectPublicationsByTreeNodeDataId(
                $publication_id
            );

            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::deleteContentObjectPublications()
     */

    public static function getContentObjectPublicationsAttributes(
        $object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, ?Condition $condition = null, ?int $count = null,
        ?int $offset = null, ?OrderBy $order_properties = null
    )
    {
        if ($type == self::ATTRIBUTES_TYPE_OBJECT)
        {
            return self::getPublicationService()->getContentObjectPublicationAttributesForContentObject(
                $object_id
            );
        }
        else
        {
            return self::getPublicationService()->getContentObjectPublicationAttributesForUser(
                $object_id
            );
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::delete_content_object_publication()
     */

    /**
     * @return PublicationService | object
     */
    protected static function getPublicationService()
    {
        $dependencyInjectionContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $dependencyInjectionContainer->get(PublicationService::class);
    }

    public static function get_content_object_publication_attribute($publication_id)
    {
        return self::getPublicationService()->getContentObjectPublicationAttributesForTreeNodeData(
            $publication_id
        );
    }

    /*
     * (non-PHPdoc) @see \core\repository\publication\PublicationInterface::add_publication_attributes_elements()
     */

    public static function isContentObjectPublished($object_id)
    {
        return self::getPublicationService()->areContentObjectsPublished([$object_id]);
    }

    /**
     * @param Attributes $publication_attributes
     *
     * @return bool
     */
    public static function update_content_object_publication_id($publication_attributes)
    {
        try
        {
            self::getPublicationService()->updateContentObjectIdInTreeNodeData(
                $publication_attributes->getId(), $publication_attributes->get_content_object_id()
            );

            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }
}
