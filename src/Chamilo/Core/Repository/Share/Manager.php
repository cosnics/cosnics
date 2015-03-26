<?php
namespace Chamilo\Core\Repository\Share;

use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * Class that describes the actions to share content objects from within a context.
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'share_action';
    const PARAM_TARGET_USERS = 'target_users';
    const PARAM_TARGET_GROUPS = 'target_groups';
    const ACTION_BROWSE = 'Browser';
    const ACTION_ADD_ENTITIES = 'Create';
    const ACTION_REMOVE_ENTITY = 'Delete';
    const ACTION_UPDATE_ENTITY = 'Update';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    /**
     * The content objects for which the share rights need to be editted
     *
     * @var Array
     */
    private $content_objects;

    /**
     * Returns the content objects
     *
     * @return Array
     */
    public function get_content_objects()
    {
        return $this->content_objects;
    }

    /**
     * Sets the content objects
     *
     * @param array $content_objects
     */
    public function set_content_objects(array $content_objects)
    {
        if (empty($content_objects))
        {
            throw new Exception(Translation :: get('NoContentObjectsGiven'));
        }

        $this->content_objects = $content_objects;
    }

    /**
     * Returns the content object ids
     *
     * @return array
     */
    public function get_content_object_ids()
    {
        $content_object_ids = array();

        foreach ($this->content_objects as $content_object)
        {
            $content_object_ids[] = $content_object->get_id();
        }

        return $content_object_ids;
    }

    /**
     * Displays the content objects
     *
     * @return String
     */
    public function display_content_objects()
    {
        $html = array();
        $html[] = '<div class="content_object padding_10">';

        $html[] = '<div class="title">' . Translation :: get(
            'SelectedObjects',
            array('OBJECTS' => Translation :: get('ContentObjects')),
            Utilities :: COMMON_LIBRARIES) . '</div>';

        $html[] = '<div class="description">';
        $html[] = '<ul class="attachments_list">';

        foreach ($this->content_objects as $content_object)
        {
            $namespace = ClassnameUtilities :: getInstance()->getNamespaceFromClassname($content_object->get_type());

            $html[] = '<li><img src="' . Theme :: getInstance()->getImagePath($namespace, 'Logo/' . Theme :: ICON_MINI) .
                 '" alt="' . htmlentities(Translation :: get('TypeName', null, $namespace)) . '"/> ' .
                 $content_object->get_title() . '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Retrieves the location in the user type tree the user is currently browsing e.g content object or category
     */
    public static function get_current_user_tree_location($user_id, $content_object_id)
    {
        if (! is_null($content_object_id))
        {
            return RepositoryRights :: get_instance()->get_location_by_identifier_from_users_subtree(
                RepositoryRights :: TYPE_USER_CONTENT_OBJECT,
                $content_object_id,
                $user_id);
        }
        $category_id = Request :: get(Manager :: PARAM_CATEGORY_ID);
        if (! is_null($category_id))
        {
            return RepositoryRights :: get_instance()->get_location_by_identifier_from_users_subtree(
                RepositoryRights :: TYPE_USER_CATEGORY,
                $content_object_id,
                $user_id);
        }
        else
        {
            return RepositoryRights :: get_instance()->get_user_root($user_id);
        }
    }
}
