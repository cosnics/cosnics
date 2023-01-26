<?php
namespace Chamilo\Core\Repository\Implementation\Youtube;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\EndsWithCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    public const ACTION_LOGIN = 'Login';
    public const ACTION_LOGOUT = 'Logout';

    public const CONTEXT = __NAMESPACE__;

    public const FEED_TYPE_GENERAL = 1;
    public const FEED_TYPE_MYVIDEOS = 2;

    public const PARAM_FEED_IDENTIFIER = 'identifier';
    public const PARAM_FEED_TYPE = 'feed';

    public const REPOSITORY_TYPE = 'youtube';

    /**
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

    public function get_available_renderers()
    {
        return [Renderer::TYPE_GALLERY, Renderer::TYPE_SLIDESHOW, Renderer::TYPE_TABLE];
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#support_sorting_direction()
     */

    public function get_content_object_type_conditions()
    {
        $video_types = File::get_video_types();
        $video_conditions = [];
        foreach ($video_types as $video_type)
        {
            $video_conditions[] = new EndsWithCondition(
                new PropertyConditionVariable(File::class, File::PROPERTY_FILENAME), $video_type
            );
        }

        return new OrCondition($video_conditions);
    }

    public function get_external_repository_actions()
    {
        $actions = [self::ACTION_BROWSE_EXTERNAL_REPOSITORY];

        if ($this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $actions[] = self::ACTION_UPLOAD_EXTERNAL_REPOSITORY;
            $actions[] = self::ACTION_EXPORT_EXTERNAL_REPOSITORY;
        }

        $is_platform = $this->get_user()->is_platform_admin();

        if ($is_platform)
        {
            $actions[] = self::ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }

        if (!$this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
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
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_menu_items()
     */

    /**
     * @param \core\repository\external\ExternalObject\Storage\DataClass $object
     *
     * @return string
     */
    public function get_external_repository_object_viewing_url($object)
    {
        $parameters = [];
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();

        return $this->get_url($parameters);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
     */

    public function get_menu_items()
    {
        $menu_items = [];

        if ($this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $my_videos = [];
            $my_videos['title'] = Translation::get('MyChannel');
            $my_videos['url'] = $this->get_url(
                [self::PARAM_FEED_TYPE => self::FEED_TYPE_MYVIDEOS],
                [ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, self::PARAM_FEED_IDENTIFIER]
            );

            $glyph = new FontAwesomeGlyph('user', [], null, 'fas');
            $my_videos['class'] = $glyph->getClassNamesString();

            $menu_items[] = $my_videos;
        }

        $browser = [];
        $browser['title'] = Translation::get('Public');
        $browser['url'] = $this->get_url(
            [self::PARAM_FEED_TYPE => self::FEED_TYPE_GENERAL],
            [ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY, self::PARAM_FEED_IDENTIFIER]
        );

        $glyph = new FontAwesomeGlyph('home', [], null, 'fas');
        $browser['class'] = $glyph->getClassNamesString();

        $menu_items[] = $browser;

        $feeds = $this->get_external_repository_manager_connector()->get_video_feeds();

        return $menu_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_available_renderers()
     */

    public function support_sorting_direction()
    {
        return false;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */

    public function validate_settings($external_repository)
    {
        $developer_key = Setting::get('developer_key', $external_repository->getId());

        if (!$developer_key)
        {
            return false;
        }

        return true;
    }
}
