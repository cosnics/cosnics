<?php
namespace Chamilo\Core\Repository\Implementation\Youtube;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Libraries\Format\Structure\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'youtube';
    const PARAM_FEED_TYPE = 'feed';
    const PARAM_FEED_IDENTIFIER = 'identifier';
    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_MYVIDEOS = 2;
    const FEED_STANDARD_TYPE = 3;
    const ACTION_LOGIN = 'Login';
    const ACTION_LOGOUT = 'Logout';

    /**
     *
     * @param $application \Chamilo\Libraries\Architecture\Application\Application
     */
    public function __construct($external_repository, $application)
    {
        if (Request :: get(self :: PARAM_FEED_TYPE) == self :: FEED_TYPE_MYVIDEOS)
        {
            Request :: set_get(self :: PARAM_FEED_TYPE, self :: FEED_TYPE_GENERAL);
        }

        parent :: __construct($external_repository, $application);
        $this->set_parameter(self :: PARAM_FEED_TYPE, Request :: get(self :: PARAM_FEED_TYPE));
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        $developer_key = PlatformSetting :: get('developer_key', $external_repository->get_id());

        if (! $developer_key)
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

        if ($this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $my_videos = array();
            $my_videos['title'] = Translation :: get('MyVideos');
            $my_videos['url'] = $this->get_url(
                array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_MYVIDEOS),
                array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
            $my_videos['class'] = 'user';
            $menu_items[] = $my_videos;
        }

        $browser = array();
        $browser['title'] = Translation :: get('Public');
        $browser['url'] = $this->get_url(
            array(self :: PARAM_FEED_TYPE => self :: FEED_TYPE_GENERAL),
            array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
        $browser['class'] = 'home';
        $menu_items[] = $browser;

//         $standard_feeds = array();
//         $standard_feeds['title'] = Translation :: get('StandardFeeds');
//         $standard_feeds['url'] = $this->get_url(
//             array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY, self :: PARAM_FEED_IDENTIFIER));
//         $standard_feeds['class'] = 'category';

//         $standard_feed_items = array();

//         $standard_feed_item = array();
//         $standard_feed_item['title'] = Translation :: get('MostViewed');
//         $standard_feed_item['url'] = $this->get_url(
//             array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_viewed'),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//         $standard_feed_item['class'] = 'feed';
//         $standard_feed_items[] = $standard_feed_item;

//         $standard_feed_item = array();
//         $standard_feed_item['title'] = Translation :: get('TopRated');
//         $standard_feed_item['url'] = $this->get_url(
//             array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'top_rated'),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//         $standard_feed_item['class'] = 'feed';
//         $standard_feed_items[] = $standard_feed_item;
//         $standard_feeds['sub'] = $standard_feed_items;

//         $standard_feed_item = array();
//         $standard_feed_item['title'] = Translation :: get('RecentlyFeatured');
//         $standard_feed_item['url'] = $this->get_url(
//             array(
//                 self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE,
//                 self :: PARAM_FEED_IDENTIFIER => 'recently_featured'),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//         $standard_feed_item['class'] = 'feed';
//         $standard_feed_items[] = $standard_feed_item;

//         $standard_feed_item = array();
//         $standard_feed_item['title'] = Translation :: get('WatchOnMobile');
//         $standard_feed_item['url'] = $this->get_url(
//             array(
//                 self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE,
//                 self :: PARAM_FEED_IDENTIFIER => 'watch_on_mobile'),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//         $standard_feed_item['class'] = 'feed';
//         $standard_feed_items[] = $standard_feed_item;

//         $standard_feed_item = array();
//         $standard_feed_item['title'] = Translation :: get('MostDiscussed');
//         $standard_feed_item['url'] = $this->get_url(
//             array(
//                 self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE,
//                 self :: PARAM_FEED_IDENTIFIER => 'most_discussed'),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//         $standard_feed_item['class'] = 'feed';
//         $standard_feed_items[] = $standard_feed_item;

//         $standard_feed_item = array();
//         $standard_feed_item['title'] = Translation :: get('TopFavorites');
//         $standard_feed_item['url'] = $this->get_url(
//             array(
//                 self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE,
//                 self :: PARAM_FEED_IDENTIFIER => 'top_favorites'),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//         $standard_feed_item['class'] = 'feed';
//         $standard_feed_items[] = $standard_feed_item;

//         $standard_feed_item = array();
//         $standard_feed_item['title'] = Translation :: get('MostResponded');
//         $standard_feed_item['url'] = $this->get_url(
//             array(
//                 self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE,
//                 self :: PARAM_FEED_IDENTIFIER => 'most_responded'),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//         $standard_feed_item['class'] = 'feed';
//         $standard_feed_items[] = $standard_feed_item;
//         $standard_feed_item = array();
//         $standard_feed_item['title'] = Translation :: get('MostRecent');
//         $standard_feed_item['url'] = $this->get_url(
//             array(self :: PARAM_FEED_TYPE => self :: FEED_STANDARD_TYPE, self :: PARAM_FEED_IDENTIFIER => 'most_recent'),
//             array(ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY));
//         $standard_feed_item['class'] = 'feed';
//         $standard_feed_items[] = $standard_feed_item;

//         $standard_feeds['sub'] = $standard_feed_items;

//         $menu_items[] = $standard_feeds;

        $feeds = $this->get_application()->get_external_repository_manager_connector()->get_video_feeds();
        var_dump($feeds);

        return $menu_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#is_ready_to_be_used()
     */
    public function is_ready_to_be_used()
    {
        $action = $this->get_parameter(self :: PARAM_ACTION);

        return self :: any_object_selected() && ($action == self :: ACTION_PUBLISHER);
        return false;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array(self :: ACTION_BROWSE_EXTERNAL_REPOSITORY);

        if ($this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $actions[] = self :: ACTION_UPLOAD_EXTERNAL_REPOSITORY;
            $actions[] = self :: ACTION_EXPORT_EXTERNAL_REPOSITORY;
        }

        $is_platform = $this->get_user()->is_platform_admin();

        if ($is_platform)
        {
            $actions[] = self :: ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }

        if (! $this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $actions[] = self :: ACTION_LOGIN;
        }
        else
        {
            $actions[] = self :: ACTION_LOGOUT;
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
                new PropertyConditionVariable(File :: class_name(), File :: PROPERTY_FILENAME),
                '*.' . $video_type);
        }

        return new OrCondition($video_conditions);
    }
}
