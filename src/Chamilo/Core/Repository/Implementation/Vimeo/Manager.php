<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

/**
 *
 * @author Hans De Bisschop
 */
abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'vimeo';
    const PARAM_FEED_TYPE = 'feed';
    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_MY_PHOTOS = 2;

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
        $my_photos['title'] = Translation::get('MyVideos');
        $my_photos['url'] = $this->get_url(
            array(self::PARAM_FEED_TYPE => self::FEED_TYPE_MY_PHOTOS), 
            array(ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY));
        $my_photos['class'] = 'user';
        // $menu_items[] = $my_photos;
        
        $general = array();
        $general['title'] = Translation::get('Public');
        $general['url'] = $this->get_url(
            array(self::PARAM_FEED_TYPE => self::FEED_TYPE_GENERAL), 
            array(ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY));
        $general['class'] = 'home';
        // $menu_items[] = $general;
        
        // $most_recent = array();
        // $most_recent['title'] = Translation :: get('MostRecent');
        // $most_recent['url'] = $this->get_url(array(self :: PARAM_FEED_TYPE =>
        // self :: FEED_TYPE_MOST_RECENT),
        // array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
        // $most_recent['class'] = 'recent';
        // $menu_items[] = $most_recent;
        //
        // $most_interesting = array();
        // $most_interesting['title'] = Translation :: get('MostInteresting');
        // $most_interesting['url'] = $this->get_url(array(self ::
        // PARAM_FEED_TYPE => self ::
        // FEED_TYPE_MOST_INTERESTING), array(ActionBarSearchForm ::
        // PARAM_SIMPLE_SEARCH_QUERY));
        // $most_interesting['class'] = 'interesting';
        // $menu_items[] = $most_interesting;
        
        return $menu_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array(self::ACTION_BROWSE_EXTERNAL_REPOSITORY);
        // self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY,
        // self :: ACTION_EXPORT_EXTERNAL_REPOSITORY);
        
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
        return array(Renderer::TYPE_GALLERY, Renderer::TYPE_SLIDESHOW, Renderer::TYPE_TABLE);
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
