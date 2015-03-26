<?php
namespace Chamilo\Core\Repository\Builder\Action;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * $Id: complex_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder
 */
/**
 * This class represents a basic complex builder structure.
 * When a builder is needed for a certain type of complex
 * object an extension should be written. We will make use of the repoviewer for selection, creation of objects
 *
 * @author vanpouckesven
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION;
    const BROWSER_COMPONENT = 'Browser';
    const CREATOR_COMPONENT = 'Creator';
    const DELETER_COMPONENT = 'Deleter';
    const MOVER_COMPONENT = 'Mover';
    const PARENT_CHANGER_COMPONENT = 'ParentChanger';
    const UPDATER_COMPONENT = 'Updater';
    const VIEWER_COMPONENT = 'Viewer';
    const ATTACHMENT_VIEWER_COMPONENT = 'AttachmentViewer';
    const ACTION_PREVIEW = 'Preview';
    const DEFAULT_ACTION = self :: BROWSER_COMPONENT;

    public static function factory($type, $application)
    {
        $class = __NAMESPACE__ . '\Component\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() .
             'Component';

        if (! class_exists($class))
        {
            throw new Exception(Translation :: get('ComponentTypeDoesNotExist', array('TYPE' => $type)));
        }

        return new $class($application);
    }

    public function get_root_content_object()
    {
        return $this->get_parent()->get_root_content_object();
    }

    public function get_complex_content_object_item()
    {
        return $this->get_parent()->get_complex_content_object_item();
    }

    public function get_additional_links()
    {
        return $this->get_parent()->get_additional_links();
    }

    /**
     * Common functionality
     */
    public function get_complex_content_object_table_html()
    {
        return $this->get_parent()->get_complex_content_object_table_html();
    }

    public function get_complex_content_object_table_condition()
    {
        return $this->get_parent()->get_complex_content_object_table_condition();
    }

    public function get_complex_content_object_menu()
    {
        return $this->get_parent()->get_complex_content_object_menu();
    }

    public function get_complex_content_object_breadcrumbs()
    {
        return $this->get_parent()->get_complex_content_object_breadcrumbs();
    }

    public function get_creation_links(ContentObject $content_object, $types = array(), $additional_links = array())
    {
        return $this->get_parent()->get_creation_links($content_object, $types, $additional_links);
    }

    public function get_complex_content_object_item_view_url($complex_content_object_item, $root_content_object_id)
    {
        return $this->get_parent()->get_complex_content_object_item_view_url(
            $complex_content_object_item,
            $root_content_object_id);
    }

    public function get_complex_content_object_parent_changer_url($complex_content_object_item, $root_content_object_id)
    {
        return $this->get_complex_content_object_parent_changer_url(
            $complex_content_object_item,
            $root_content_object_id);
    }

    public function get_action_bar(ContentObject $content_object)
    {
        return $this->get_parent()->get_action_bar($content_object);
    }

    public function get_parent_content_object_id()
    {
        return $this->get_parent()->get_parent_content_object_id();
    }

    public function get_parent_content_object()
    {
        return $this->get_parent()->get_parent_content_object();
    }

    /**
     * Builds the attachment url
     *
     * @param $attachment ContentObject
     * @param $selected_complex_content_object_item_id int [OPTIONAL] default null
     * @return string
     */
    public function get_content_object_display_attachment_url($attachment,
        $selected_complex_content_object_item_id = null)
    {
        return $this->get_parent()->get_content_object_display_attachment_url(
            $attachment,
            $selected_complex_content_object_item_id);
    }

    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Repository\Manager :: PARAM_CONTENT_OBJECT_ID);
    }
}
