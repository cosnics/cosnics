<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\MetadataOld;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

/**
 * The manager of this package.
 *
 * @package repository\content_object_metadata_element_linker
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'metadata_action';
    const ACTION_EDIT_METADATA_BATCH = 'MetadataBatchEditor';
    const DEFAULT_ACTION = self :: ACTION_EDIT_METADATA_BATCH;

    /**
     * Returns the selected ContentObject from the request parameters
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return ContentObject[]
     */
    public function get_content_objects_from_request()
    {
        $content_object_ids = $this->get_selected_content_object_ids();

        $content_objects = array();

        foreach ($content_object_ids as $content_object_id)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $content_object_id);

            if (! $content_object)
            {
                throw new ObjectNotExistException(
                    Translation :: get('ContentObject', null, \Chamilo\Core\Repository\Manager :: context()));
            }

            $content_objects[] = $content_object;
        }

        return $content_objects;
    }

    /**
     * Returns the selected content object ids
     *
     * @return int[]
     *
     * @throws \libraries\architecture\NoObjectSelectedException
     */
    protected function get_selected_content_object_ids()
    {
        $content_object_ids = Request :: get(\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);

        if (! isset($content_object_ids))
        {
            throw new NoObjectSelectedException(
                Translation :: get('ContentObject', null, \Chamilo\Core\Repository\Manager :: context()));
        }

        if (! is_array($content_object_ids))
        {
            $content_object_ids = array($content_object_ids);
        }

        return $content_object_ids;
    }
}
