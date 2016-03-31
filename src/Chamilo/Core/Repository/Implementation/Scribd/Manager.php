<?php
namespace Chamilo\Core\Repository\Implementation\Scribd;

use Chamilo\Core\Repository\External\ExternalObject;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Hans De Bisschop
 */
abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'scribd';
    const ACTION_DATA_OBJECT_VIEWER = 'DataObjectViewer';
    const PARAM_DOWNLOAD_FORMAT = 'download_format';

    /**
     *
     * @param $application Application
     */
    public function __construct($external_repository, $application)
    {
        parent :: __construct($external_repository, $application);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        $username = Setting :: get('username', $external_repository->get_id());
        $password = Setting :: get('password', $external_repository->get_id());

        if (! $username || ! $password)
        {
            return false;
        }
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
     * @param \core\repository\external\ExternalObject $object
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
        $actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY, self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY);

        return $actions;
    }

    public function get_external_repository_object_actions(ExternalObject $object)
    {
        $actions = parent :: get_external_repository_object_actions($object);
        if (in_array(self :: ACTION_IMPORT_EXTERNAL_REPOSITORY, array_keys($actions)))
        {
            unset($actions[self :: ACTION_IMPORT_EXTERNAL_REPOSITORY]);
            $download_formats = $object->get_download_formats();

            foreach ($download_formats as $download_format)
            {
                $actions[$download_format] = new ToolbarItem(
                    Translation :: get(
                        'Import' . StringUtilities :: getInstance()->createString($download_format)->upperCamelize()),
                    Theme :: getInstance()->getImagePath(__NAMESPACE__, 'Download/' . $download_format),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_IMPORT_EXTERNAL_REPOSITORY,
                            self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id(),
                            self :: PARAM_DOWNLOAD_FORMAT => $download_format)),
                    ToolbarItem :: DISPLAY_ICON);
            }
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
        return null;
    }

    /**
     *
     * @return string
     */
    public function get_repository_type()
    {
        return self :: REPOSITORY_TYPE;
    }
}
