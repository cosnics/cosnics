<?php
namespace Chamilo\Core\Repository\External\Action;

use Chamilo\Core\Repository\External\ExternalObject;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CONFIGURE = 'Configurer';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_DOWNLOAD = 'Downloader';
    public const ACTION_EDIT = 'Editor';
    public const ACTION_EXPORT = 'Exporter';
    public const ACTION_EXTERNAL_SYNC = 'ExternalSyncer';
    public const ACTION_FOLDER = 'Folder';
    public const ACTION_IMPORT = 'Importer';
    public const ACTION_INTERNAL_SYNC = 'InternalSyncer';
    public const ACTION_SELECT = 'Selecter';
    public const ACTION_VIEW = 'Viewer';

    // Default action

    public const DEFAULT_ACTION = self::ACTION_BROWSE;

    public function count_external_repository_objects($condition)
    {
        return $this->get_parent()->count_external_repository_objects($condition);
    }

    public function delete_external_repository_object($id)
    {
        return $this->get_parent()->delete_external_repository_object($id);
    }

    public function export_external_repository_object($object)
    {
        return $this->get_parent()->export_external_repository_object($object);
    }

    public function get_content_object_type_conditions()
    {
        return $this->get_parent()->get_content_object_type_conditions();
    }

    public function get_external_repository()
    {
        return $this->get_parent()->get_external_repository();
    }

    public function get_external_repository_object_actions(ExternalObject $object)
    {
        return $this->get_parent()->get_external_repository_object_actions($object);
    }

    /**
     *
     * @param \core\repository\external\ExternalObject $object
     */
    public function get_external_repository_object_viewing_url($object)
    {
        return $this->get_parent()->get_external_repository_object_viewing_url($object);
    }

    public function get_menu(): string
    {
        return $this->get_parent()->get_menu();
    }

    public function get_menu_items()
    {
        return $this->get_parent()->get_menu_items();
    }

    public function get_repository_type()
    {
        return $this->get_parent()->get_repository_type();
    }

    public function get_setting($variable)
    {
        return $this->get_parent()->get_setting($variable);
    }

    public function get_user_setting($variable)
    {
        return $this->get_parent()->get_user_setting($variable);
    }

    public function import_external_repository_object(ExternalObject $object)
    {
        return $this->get_parent()->import_external_repository_object($object);
    }

    public function retrieve_external_repository_object($id)
    {
        return $this->get_parent()->retrieve_external_repository_object($id);
    }

    public function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        return $this->get_parent()->retrieve_external_repository_objects($condition, $order_property, $offset, $count);
    }

    public function support_sorting_direction()
    {
        return $this->get_parent()->support_sorting_direction();
    }

    public function synchronize_external_repository_object(ExternalObject $object)
    {
        return $this->get_parent()->synchronize_external_repository_object($object);
    }

    public function synchronize_internal_repository_object(ExternalObject $object)
    {
        return $this->get_parent()->synchronize_internal_repository_object($object);
    }

    public function translate_search_query($query)
    {
        return $this->get_parent()->translate_search_query($query);
    }
}
