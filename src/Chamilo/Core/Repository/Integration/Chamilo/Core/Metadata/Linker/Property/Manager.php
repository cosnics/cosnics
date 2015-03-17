<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Storage\DataClass\ContentObjectPropertyRelMetadataElement;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Property\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * The manager of this package.
 *
 * @package repository\integration\core\metadata\linker\property
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'linker_property_action';
    const PARAM_CONTENT_OBJECT_PROPERTY_REL_METADATA_ELEMENT_ID = 'content_object_property_rel_metadata_id';
    const ACTION_BROWSE = 'browser';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';
    const ACTION_CREATE = 'creator';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * Returns the selected ContentObjectPropertyRelMetadataElement from the request parameters
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return ContentObjectPropertyRelMetadataElement
     */
    public function get_content_object_property_rel_metadata_element_from_request()
    {
        return $this->get_content_object_property_rel_metadata_element_by_id(
            $this->get_content_object_property_rel_metadata_element_id_from_request());
    }

    /**
     * Returns the selected ContentObjectPropertyRelMetadataElement id from the request parameters
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     */
    public function get_content_object_property_rel_metadata_element_id_from_request()
    {
        $content_object_property_rel_metadata_element_id = Request :: get(
            self :: PARAM_CONTENT_OBJECT_PROPERTY_REL_METADATA_ELEMENT_ID);

        if (! isset($content_object_property_rel_metadata_element_id))
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObjectPropertyRelMetadataElement'));
        }

        return $content_object_property_rel_metadata_element_id;
    }

    /**
     * Returns the selected ContentObjectPropertyRelMetadataElement from a given id
     *
     * @param int $content_object_property_rel_metadata_element_id
     *
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return ContentObjectPropertyRelMetadataElement
     */
    public function get_content_object_property_rel_metadata_element_by_id(
        $content_object_property_rel_metadata_element_id)
    {
        $content_object_property_rel_metadata_element = DataManager :: retrieve_by_id(
            ContentObjectPropertyRelMetadataElement :: class_name(),
            $content_object_property_rel_metadata_element_id);

        if (! $content_object_property_rel_metadata_element)
        {
            throw new ObjectNotExistException(Translation :: get('ContentObjectPropertyRelMetadataElement'));
        }

        return $content_object_property_rel_metadata_element;
    }

    /**
     * Fills the given ContentObjectPropertyRelMetadataElement from the given array based values
     *
     * @param ContentObjectPropertyRelMetadataElement $content_object_property_rel_metadata_element
     * @param array $values
     */
    public function fill_content_object_property_rel_metadata_element_from_values_array(
        ContentObjectPropertyRelMetadataElement $content_object_property_rel_metadata_element, array $values)
    {
        $content_object_property_rel_metadata_element->set_content_object_type(
            $values[ContentObjectPropertyRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE]);

        $content_object_property_rel_metadata_element->set_property_name(
            $values[ContentObjectPropertyRelMetadataElement :: PROPERTY_PROPERTY_NAME]);

        $content_object_property_rel_metadata_element->set_metadata_element_id(
            $values[ContentObjectPropertyRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID]);
    }
}
