<?php
namespace Chamilo\Core\Repository\Implementation\Box;

use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'box';
    const PARAM_FEED_TYPE = 'feed';
    const PARAM_FEED_IDENTIFIER = 'identifier';
    const FEED_TYPE_GENERAL = 1;

    /**
     *
     * @param $applicationConfiguration ApplicationConfigurationInterface
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->set_parameter(self::PARAM_FEED_TYPE, Request::get(self::PARAM_FEED_TYPE));
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        // $key = ExternalRepositorySetting :: get('key');
        // $secret = ExternalRepositorySetting :: get('secret');
        //
        // if (! $key || ! $secret)
        // {
        // return false;
        // }
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
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        
        return $this->get_url($parameters);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_menu_items()
     */
    public function get_menu_items()
    {
        $menu_items = array();
        
        $general = array();
        $general['title'] = Translation::get('Box.net');
        $general['url'] = $this->get_url(
            array(self::PARAM_FEED_TYPE => self::FEED_TYPE_GENERAL), 
            array(ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY));
        $general['class'] = 'home';
        $menu_items[] = $general;
        
        $folders = $this->get_external_repository_manager_connector()->retrieve_folders(
            $this->get_url(array(self::PARAM_FOLDER => '__PLACEHOLDER__')));
        $menu_items = array_merge($menu_items, $folders);
        return $menu_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array(
            self::ACTION_BROWSE_EXTERNAL_REPOSITORY, 
            self::ACTION_UPLOAD_EXTERNAL_REPOSITORY, 
            self::ACTION_EXPORT_EXTERNAL_REPOSITORY);
        
        $is_platform = $this->get_user()->is_platform_admin() && (count(
            Setting::get_all($this->get_external_repository()->get_id())) > 0);
        
        if ($is_platform)
        {
            $actions[] = self::ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }
        return $actions;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_available_renderers()
     */
    public function get_available_renderers()
    {
        return array(Renderer::TYPE_TABLE);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */
    public function get_content_object_type_conditions()
    {
    }

    /**
     *
     * @return string
     */
    public function get_repository_type()
    {
        return self::REPOSITORY_TYPE;
    }
}
