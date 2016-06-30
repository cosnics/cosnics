<?php
namespace Chamilo\Core\Repository\Implementation\Matterhorn;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @author magali.gillard
 */
abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'matterhorn';
    const PARAM_FEED_TYPE = 'feed';
    const PARAM_FEED_IDENTIFIER = 'identifier';
    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_MY_VIDEO = 2;
    const ACTION_STREAM = 'Streamer';

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#support_sorting_direction()
     */
    public function support_sorting_direction()
    {
        return true;
    }

    /**
     *
     * @param \common\extensions\external_repository_manager\ExternalObject $object
     * @return string
     */
    public function get_external_repository_object_viewing_url($object)
    {
        $parameters = array();
        $parameters[self :: PARAM_ACTION] = self :: ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

        return $this->get_url($parameters);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_menu_items()
     */
    public function get_menu_items()
    {
        $menu_items = array();

        return $menu_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array();
        $actions[] = self :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
        $actions[] = self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY;
        // $actions[] = self :: ACTION_BROWSE_WORKFLOW;

        $is_platform = $this->get_user()->is_platform_admin();
        $has_settings = $this->get_external_repository()->has_settings();

        if ($has_settings && $is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }
        return $actions;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_available_renderers()
     */
    public function get_available_renderers()
    {
        return array(Renderer :: TYPE_GALLERY, Renderer :: TYPE_SLIDESHOW, Renderer :: TYPE_TABLE);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */
    public function get_content_object_type_conditions()
    {
        $video_types = File :: get_video_types();
        $video_conditions = array();
        foreach ($video_types as $video_type)
        {
            $video_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(File :: class_name, File :: PROPERTY_FILENAME),
                '*.' . $video_type);
        }

        return new OrCondition($video_conditions);
    }

    /**
     *
     * @return string
     */
    public function get_repository_type()
    {
        return self :: REPOSITORY_TYPE;
    }

    public function get_instance_identifier()
    {
        return array('url');
    }

    public function get_external_repository_object_actions(\Chamilo\Core\Repository\External\ExternalObject $object)
    {
        $toolbar_items = parent :: get_external_repository_object_actions($object);

        if ($object->is_deletable())
        {
            $parameters = array();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_DELETE_EXTERNAL_REPOSITORY;
            $parameters[self :: PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
            $deleting_url = $this->get_url($parameters);

            $toolbar_items[self :: ACTION_DELETE_EXTERNAL_REPOSITORY] = new ToolbarItem(
                Translation :: get('DeleteRepository'),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $deleting_url,
                ToolbarItem :: DISPLAY_ICON,
                true);
        }

        return $toolbar_items;
    }
}
