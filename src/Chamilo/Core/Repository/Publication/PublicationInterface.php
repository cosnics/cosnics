<?php
namespace Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * An interface that indicates that a class implements usage of the repository and forces the implementing context to
 * provide a given number of methods to safeguard compatibility with the repository
 * 
 * @author Hans De Bisschop
 */
interface PublicationInterface
{
    const ATTRIBUTES_TYPE_USER = 1;
    const ATTRIBUTES_TYPE_OBJECT = 2;

    /**
     * Determines whether the given content object can be edited in the implementing context
     * 
     * @param int $object_id
     * @return boolean
     */
    public static function is_content_object_editable($object_id);

    /**
     * Determines whether the given content object has been published in the implementing context
     * 
     * @param int $object_id
     * @return boolean
     */
    public static function content_object_is_published($object_id);

    /**
     * Determines whether any of the given content objects have been published in the implementing context
     * 
     * @param multitype:int $object_ids
     * @return boolean
     */
    public static function any_content_object_is_published($object_ids);

    /**
     * Returns attributes for content object publications in the implementing context
     * 
     * @param int $type
     * @param int $object_id
     * @param \libraries\storage\Condition $condition
     * @return multitype:mixed
     */
    public static function get_content_object_publication_attributes($object_id, $type = self::ATTRIBUTES_TYPE_OBJECT, $condition = null, $count = null,
        $offset = null, $order_properties = null);

    /**
     * Determines where in the implementing context the given content object is published
     * 
     * @param int $publication_id
     * @return \core\repository\ContentObjectPublicationAttributes
     */
    public static function get_content_object_publication_attribute($publication_id);

    /**
     * Counts the number of content object publications in the implementing context
     * 
     * @param int $attributes_type
     * @param int $identifier
     * @param \libraries\storage\Condition $condition
     * @return int
     */
    public static function count_publication_attributes($attributes_type = self::ATTRIBUTES_TYPE_OBJECT, $identifier, $condition = null);

    /**
     * Deletes all publications of a given content object in the implementing context
     * 
     * @param int $object_id
     * @return boolean
     */
    public static function delete_content_object_publications($object_id);

    /**
     *
     * @param int $publication_id
     * @return boolean
     */
    public static function delete_content_object_publication($publication_id);

    /**
     *
     * @param \core\repository\ContentObject $content_object
     * @return multitype:mixed
     */
    public static function get_content_object_publication_locations($content_object, $user = null);

    /**
     *
     * @param \core\repository\ContentObject $content_object
     * @param string[] $location
     * @param string[] $attributes
     */
    public static function publish_content_object(ContentObject $content_object, LocationSupport $location, 
        $options = array());

    /**
     *
     * @param \libraries\format\FormValidator $form
     */
    public static function add_publication_attributes_elements($form);

    /**
     *
     * @param \core\repository\ContentObjectPublicationAttributes $publication_attributes
     */
    public static function update_content_object_publication_id($publication_attributes);
}