<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage\DataClass\ContentObjectAlternative;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Alternative\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 * The manager of this package.
 *
 * @package repository\integration\core\metadata\linker\alternative
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'linker_alternative_action';
    const PARAM_CONTENT_OBJECT_ID = \Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID;
    const PARAM_CONTENT_OBJECT_ALTERNATIVE_ID = 'content_object_alternative_id';
    const ACTION_BROWSE = 'browser';
    const ACTION_DELETE = 'deleter';
    const ACTION_UPDATE = 'updater';
    const ACTION_CREATE = 'creator';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * Returns the selected ContentObjectAlternative from the request parameters
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return ContentObjectAlternative
     */
    public function get_content_object_alternative_from_request()
    {
        return $this->get_content_object_alternative_by_id($this->get_content_object_alternative_id_from_request());
    }

    /**
     * Returns the selected ContentObjectAlternative id from the request parameters
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     */
    public function get_content_object_alternative_id_from_request()
    {
        $content_object_alternative_id = Request :: get(self :: PARAM_CONTENT_OBJECT_ALTERNATIVE_ID);

        if (! isset($content_object_alternative_id))
        {
            throw new NoObjectSelectedException(Translation :: get('ContentObjectAlternative'));
        }

        return $content_object_alternative_id;
    }

    /**
     * Returns the selected ContentObjectAlternative from a given id
     *
     * @param int $content_object_alternative_id
     *
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return ContentObjectAlternative
     */
    public function get_content_object_alternative_by_id($content_object_alternative_id)
    {
        $content_object_alternative = DataManager :: retrieve_by_id(
            ContentObjectAlternative :: class_name(),
            $content_object_alternative_id);

        if (! $content_object_alternative)
        {
            throw new ObjectNotExistException(Translation :: get('ContentObjectAlternative'));
        }

        return $content_object_alternative;
    }

    /**
     * Returns the selected content object id
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     *
     * @return int
     */
    public function get_selected_content_object_id()
    {
        $content_object_id = $this->get_parent()->get_selected_content_object_id();

        if (! isset($content_object_id))
        {
            throw new NoObjectSelectedException(
                Translation :: get('ContentObject', null, \Chamilo\Core\Repository\Manager :: context()));
        }

        return $content_object_id;
    }

    /**
     * Returns the selected content object
     *
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return ContentObject
     */
    public function get_selected_content_object()
    {
        $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
            $this->get_selected_content_object_id());

        if (! $content_object)
        {
            throw new ObjectNotExistException(
                Translation :: get('ContentObject', null, \Chamilo\Core\Repository\Manager :: context()));
        }

        return $content_object;
    }

    /**
     * Displays the selected content object This function is used in multiple components
     *
     * @param ContentObject $content_object
     */
    protected function display_content_object($content_object = null)
    {
        if (! $content_object)
        {
            $content_object = $this->get_selected_content_object();
        }

        $display = ContentObjectRenditionImplementation :: factory(
            $content_object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FULL,
            $this);

        return $display->render();
    }

    /**
     * Retrieves the content objects by the given ids
     *
     * @param array $selected_content_object_ids
     *
     * @return \libraries\storage\ResultSet
     */
    protected function retrieve_content_objects_by_id($selected_content_object_ids = array())
    {
        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            $selected_content_object_ids);

        $parameters = new DataClassRetrievesParameters($condition);

        return \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_objects(
            ContentObject :: class_name(),
            $parameters);
    }

    /**
     * Returns the allowed metadata elements for the given content objects and the current selected content object
     *
     * @param array $content_object_ids
     *
     * @return \core\metadata\element\storage\data_class\Element[]
     */
    protected function get_allowed_metadata_elements($content_object_ids = array())
    {
        $content_object_ids[] = $this->get_selected_content_object_id();

        $content_objects = $this->retrieve_content_objects_by_id($content_object_ids);

        return \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Linker\Type\Storage\DataManager :: get_common_metadata_elements(
            $content_objects->as_array());
    }

    /**
     * Returns additional parameters that need to be registered
     *
     * @return array \common\libraries\multitype
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_CONTENT_OBJECT_ID);
    }
}
