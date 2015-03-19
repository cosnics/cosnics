<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Storage\DataClass\ContentObjectRelMetadataElement;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 * The manager of this package.
 *
 * @package repository\integration\core\metadata\linker\type
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'linker_type_action';
    const PARAM_CONTENT_OBJECT_REL_METADATA_ELEMENT_ID = 'content_object_rel_metadata_element_id';
    const ACTION_BROWSE = 'Browser';
    const ACTION_DELETE = 'Deleter';
    const ACTION_UPDATE = 'Updater';
    const ACTION_CREATE = 'Creator';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * Returns the selected ContentObjectRelMetadataElement from the request parameters
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return ContentObjectRelMetadataElement
     */
    public function get_content_object_rel_metadata_element_from_request()
    {
        return $this->get_content_object_rel_metadata_element_by_id(
            $this->get_content_object_rel_metadata_element_id_from_request());
    }

    /**
     * Returns the selected ContentObjectRelMetadataElement id from the request parameters
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     */
    public function get_content_object_rel_metadata_element_id_from_request()
    {
        $content_object_rel_metadata_element_id = Request :: get(self :: PARAM_CONTENT_OBJECT_REL_METADATA_ELEMENT_ID);

        if (! isset($content_object_rel_metadata_element_id))
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObjectRelMetadataElement'));
        }

        return $content_object_rel_metadata_element_id;
    }

    /**
     * Returns the selected ContentObjectRelMetadataElement from a given id
     *
     * @param int $content_object_rel_metadata_element_id
     *
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return ContentObjectRelMetadataElement
     */
    public function get_content_object_rel_metadata_element_by_id($content_object_rel_metadata_element_id)
    {
        $content_object_rel_metadata_element = DataManager :: retrieve_by_id(
            ContentObjectRelMetadataElement :: class_name(),
            $content_object_rel_metadata_element_id);

        if (! $content_object_rel_metadata_element)
        {
            throw new ObjectNotExistException(Translation :: get('ContentObjectRelMetadataElement'));
        }

        return $content_object_rel_metadata_element;
    }

    /**
     * Fills the given ContentObjectRelMetadataElement from the given array based values
     *
     * @param ContentObjectRelMetadataElement $content_object_rel_metadata_element
     * @param array $values
     */
    public function fill_content_object_rel_metadata_element_from_values_array(
        ContentObjectRelMetadataElement $content_object_rel_metadata_element, array $values)
    {
        if (! empty($values[ContentObjectRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE]))
        {
            $content_object_rel_metadata_element->set_content_object_type(
                $values[ContentObjectRelMetadataElement :: PROPERTY_CONTENT_OBJECT_TYPE]);
        }

        $content_object_rel_metadata_element->set_metadata_element_id(
            $values[ContentObjectRelMetadataElement :: PROPERTY_METADATA_ELEMENT_ID]);

        $content_object_rel_metadata_element->set_required(
            $values[ContentObjectRelMetadataElement :: PROPERTY_REQUIRED] ? $values[ContentObjectRelMetadataElement :: PROPERTY_REQUIRED] : 0);
    }
}
