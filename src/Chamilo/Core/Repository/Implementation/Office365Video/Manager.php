<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'office365_video';
    const PARAM_CHANNEL_ID = 'channel_id';
    const ACTION_LOGIN = 'Login';
    const ACTION_LOGOUT = 'Logout';
    const DEFAULT_ACTION = self::ACTION_LOGIN;

    /**
     *
     * @param $application \Chamilo\Libraries\Architecture\Application\Application
     */
    public function __construct($external_repository, $application)
    {
        parent::__construct($external_repository, $application);
        $this->set_parameter(self::PARAM_CHANNEL_ID, $this->getSelectedChannelId());
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        $developer_key = Configuration::getInstance()->get_setting(
            array($external_repository->get_id(), 'developer_key'));
        
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
     * @param \core\repository\external\ExternalObject\Storage\DataClass $object
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
        
        $channels = $this->get_external_repository_manager_connector()->getChannels();
        foreach ($channels as $id => $name)
        {
            $menu_item = array();
            $menu_item['title'] = $name;
            $menu_item['url'] = $this->get_url(
                array(self::PARAM_CHANNEL_ID => $id), 
                array(ActionBarSearchForm::PARAM_SIMPLE_SEARCH_QUERY));
            $my_videos['class'] = 'user';
            $menu_items[] = $menu_item;
        }
        
        return $menu_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
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
                '*.' . $video_type);
        }
        
        return new OrCondition($video_conditions);
    }

    /**
     * \brief Returns value of PARAM_CHANNEL_ID.
     * \return string Value of PARAM_CHANNEL_ID if not null. Else ID of first channel.
     */
    private function getSelectedChannelId()
    {
        $channelId = Request::get(self::PARAM_CHANNEL_ID);
        if (empty($channelId))
        {
            $dataConnector = $this->get_external_repository_manager_connector();
            if ($dataConnector->isUserLoggedIn())
            {
                $channels = $dataConnector->getChannels();
                if (! empty($channels))
                {
                    $channelId = array_keys($channels)[0];
                }
            }
        }
        
        return $channelId;
    }
}
