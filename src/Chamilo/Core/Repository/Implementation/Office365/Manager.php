<?php
namespace Chamilo\Core\Repository\Implementation\Office365;

use Chamilo\Core\Repository\Implementation\Office365\Menu\CategoryTreeMenu;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'office365';
    const PARAM_FOLDER = 'folder';
    const PARAM_IMPORT_AS_LINK = 'import_as_link';
    const ACTION_LOGIN = 'Login';
    const ACTION_LOGOUT = 'Logout';
    const DEFAULT_ACTION = self::ACTION_LOGIN;

    private $categoryTreeMenu;

    public function getCategoryTreeMenu()
    {
        return $this->categoryTreeMenu;
    }

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
        return false;
    }

    /**
     *
     * @param \core\repository\external\ExternalObject $object
     * @return string
     */
    public function get_external_repository_object_viewing_url($object)
    {
        $parameters = array();

        if ($object->get_type() == ExternalObject::TYPE_FOLDER)
        {
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY] = $this->get_external_repository_manager_connector()->get_external_repository_instance_id();
            $parameters[Manager::PARAM_FOLDER] = $object->get_id();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_BROWSE_EXTERNAL_REPOSITORY;
        }
        else
        {
            $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        }

        return $this->get_url($parameters);
    }

    /**
     *
     * @return array
     */
    public function get_menu_items()
    {
        if ($this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $menu_items = array();

            $shared = array();
            $shared['title'] = Translation::get('SharedWithMe');
            $shared['url'] = $this->get_url(array(self::PARAM_FOLDER => DataConnector::DOCUMENTS_SHARED));
            $shared['class'] = 'external_repository';
            $menu_items[] = $shared;

            $recent = array();
            $recent['title'] = Translation::get('Recent');
            $recent['url'] = $this->get_url(array(self::PARAM_FOLDER => DataConnector::DOCUMENTS_RECENT));
            $recent['class'] = 'recent';
            $menu_items[] = $recent;

            return $menu_items;
        }
        else
        {
            return $this->display_warning_page(Translation::get('YouMustBeLoggedIn'));
        }
    }

    public function get_menu()
    {
        if (! isset($this->categoryTreeMenu))
        {
            $this->categoryTreeMenu = new CategoryTreeMenu(
                $this->get_external_repository_manager_connector(),
                $this->get_menu_items());
        }

        return $this->categoryTreeMenu;
    }

    /*
     * (non-PHPdoc) @see
     * application/common/external_repository_manager/ExternalRepositoryManager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array(self::ACTION_BROWSE_EXTERNAL_REPOSITORY);

        if (! $this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $actions[] = self::ACTION_LOGIN;
        }
        else
        {
            $actions[] = self::ACTION_LOGOUT;
        }

        return $actions;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */
    public function get_content_object_type_conditions()
    {
    }

    /**
     *
     * @param $object ExternalObject
     * @return array
     */
    public function get_external_repository_object_actions(\Chamilo\Core\Repository\External\ExternalObject $object)
    {
        if ($object->get_type() == ExternalObject::TYPE_FOLDER)
        {
            return array(
                self::ACTION_BROWSE_EXTERNAL_REPOSITORY => new ToolbarItem(
                    Translation::getInstance()->getTranslation('View', null, Utilities::COMMON_LIBRARIES),
                    Theme::getInstance()->getImagePath(
                        'Chamilo\Core\Repository\Implementation\Office365',
                        'Action/ViewFolder'),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_BROWSE_EXTERNAL_REPOSITORY,
                            self::PARAM_FOLDER => $object->get_id(),
                            self::PARAM_EXTERNAL_REPOSITORY => $this->get_external_repository_manager_connector()->get_external_repository_instance_id())),
                    ToolbarItem::DISPLAY_ICON));
        }
      
        $actions = parent::get_external_repository_object_actions($object);
        if (in_array(self :: ACTION_IMPORT_EXTERNAL_REPOSITORY, array_keys($actions)))
        {
            unset($actions[self :: ACTION_IMPORT_EXTERNAL_REPOSITORY]);
         
            $actions[self :: ACTION_IMPORT_EXTERNAL_REPOSITORY] = new ToolbarItem(
                Translation :: get('ImportAsFile'),
                ContentObject :: icon_path('Chamilo\Core\Repository\ContentObject\File', Theme :: ICON_MINI),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_EXTERNAL_REPOSITORY,
                                     self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id(),
                                     self :: PARAM_IMPORT_AS_LINK => false)),
                ToolbarItem :: DISPLAY_ICON);

            $actions[self :: PARAM_IMPORT_AS_LINK] = new ToolbarItem(
                Translation :: get('ImportAsLink'),
                ContentObject :: icon_path('Chamilo\Core\Repository\ContentObject\Link', Theme :: ICON_MINI),
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_IMPORT_EXTERNAL_REPOSITORY,
                                     self :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id(),
                                     self :: PARAM_IMPORT_AS_LINK => true)),
                ToolbarItem :: DISPLAY_ICON);
        }

        return $actions;
      }

    /**
     *
     * @return string
     */
    public function get_repository_type()
    {
        return self::REPOSITORY_TYPE;
    }

    /**
     * Copies properties of given external object to the given content object.
     *
     * @param Repository\ContentObject $contentObject Supported data object classes:
     *     - Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
     *     - Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;
     * @param ExternalObject $externalObject
     */
    protected function synchronizeContentObjectWithExternalObject($contentObject, $externalObject)
    {
        $contentObject->set_title($externalObject->get_title());

        if (PlatformSetting::get('description_required', \Chamilo\Core\Repository\Manager::context()) &&
             StringUtilities::getInstance()->isNullOrEmpty($externalObject->get_description()))
        {
            $contentObject->set_description('-');
        }
        else
        {
            $contentObject->set_description($externalObject->get_description());
        }

        $contentObject->set_owner_id($this->get_user_id());

        if ($contentObject instanceof \Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File)
        {
            $contentObject->set_filename(Filesystem::create_safe_name($externalObject->get_title()));
            $contentObject->set_in_memory_file($externalObject->get_content_data());
        }
        else if ($contentObject instanceof \Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link)
        {
            $contentObject->set_url($externalObject->getUrl());
            $contentObject->set_show_in_iframe(false);
        }
        else
        {
            throw new \Exception('Synchronizing Office 365 external object with data object of type "' . 
                                 get_class($contentObject) . '" not implemented yet.'); 
        }
    }
}
