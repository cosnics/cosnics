<?php
namespace Chamilo\Core\Repository\Implementation\Slideshare;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'slideshare';
    const PARAM_FEED_TYPE = 'feed';
    const FEED_TYPE_GENERAL = 1;

    /**
     *
     * @param $application Application
     */
    public function __construct($external_repository, $application)
    {
        parent::__construct($external_repository, $application);
        $this->set_parameter(self::PARAM_FEED_TYPE, Request::get(self::PARAM_FEED_TYPE));
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        $key = Setting::get('consumer_key', $external_repository->get_id());
        $secret = Setting::get('consumer_secret', $external_repository->get_id());
        
        if (! $key || ! $secret)
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
        
        $my_photos = array();
        $my_photos['title'] = Translation::get('Slideshare');
        $my_photos['url'] = $this->get_url();
        $my_photos['class'] = 'user';
        $menu_items[] = $my_photos;
        
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
        return array(Renderer::TYPE_GALLERY, Renderer::TYPE_TABLE);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */
    public function get_content_object_type_conditions()
    {
        $video_types = File::get_video_types();
        $video_conditions = array();
        foreach ($video_types as $video_type)
        {
            $video_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(File::class_name(), File::PROPERTY_FILENAME), 
                '*.' . $video_type, 
                File::get_type_name());
        }
        
        return new OrCondition($video_conditions);
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
