<?php
namespace Chamilo\Core\Repository\Implementation\Flickr;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop
 */
abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const ACTION_LOGIN = 'Login';

    const ACTION_LOGOUT = 'Logout';

    const FEED_TYPE_GENERAL = 1;
    const FEED_TYPE_MOST_INTERESTING = 2;
    const FEED_TYPE_MOST_RECENT = 3;
    const FEED_TYPE_MY_PHOTOS = 4;

    const PARAM_FEED_TYPE = 'feed';

    const REPOSITORY_TYPE = 'flickr';

    /**
     *
     * @param $application Application
     */
    public function __construct($user, $application = null)
    {
        parent::__construct($user, $application);
        $this->set_parameter(self::PARAM_FEED_TYPE, Request::get(self::PARAM_FEED_TYPE));
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */

    public function get_available_renderers()
    {
        return array(Renderer::TYPE_GALLERY, Renderer::TYPE_SLIDESHOW, Renderer::TYPE_TABLE);
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#support_sorting_direction()
     */

    public function get_content_object_type_conditions()
    {
        $image_types = File::get_image_types();
        $image_conditions = array();
        foreach ($image_types as $image_type)
        {
            $image_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(File::class_name(), File::PROPERTY_FILENAME), '*.' . $image_type
            );
        }

        return new OrCondition($image_conditions);
    }

    public function get_external_repository_actions()
    {
        $actions = array(self::ACTION_BROWSE_EXTERNAL_REPOSITORY);

        if ($this->get_external_repository()->get_setting('session_token'))
        {
            $actions[] = self::ACTION_UPLOAD_EXTERNAL_REPOSITORY;
            $actions[] = self::ACTION_EXPORT_EXTERNAL_REPOSITORY;
        }

        $is_platform = $this->get_user()->is_platform_admin();
        $has_setting = $this->get_external_repository()->has_settings();

        if ($has_setting)
        {
            $actions[] = self::ACTION_CONFIGURE_EXTERNAL_REPOSITORY;
        }

        if (!$this->get_external_repository()->get_setting('session_token')->get_value())
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
     *
     * @param \core\repository\external\ExternalObject $object
     *
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
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
     */

    public function get_menu_items()
    {
        $menu_items = array();

        if ($this->get_external_repository()->get_setting('session_token'))
        {
            $my_photos = array();
            $my_photos['title'] = Translation::get('MyPhotos');
            $my_photos['url'] = $this->get_url(
                array(self::PARAM_FEED_TYPE => self::FEED_TYPE_MY_PHOTOS),
                array(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY)
            );

            $glyph = new FontAwesomeGlyph('user', array(), null, 'fas');
            $my_photos['class'] = $glyph->getClassNamesString();

            $menu_items[] = $my_photos;
        }

        $general = array();
        $general['title'] = Translation::get('Public');
        $general['url'] = $this->get_url(
            array(self::PARAM_FEED_TYPE => self::FEED_TYPE_GENERAL), array(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY)
        );

        $glyph = new FontAwesomeGlyph('home', array(), null, 'fas');
        $general['class'] = $glyph->getClassNamesString();

        $menu_items[] = $general;

        $most_recent = array();
        $most_recent['title'] = Translation::get('MostRecent');
        $most_recent['url'] = $this->get_url(
            array(self::PARAM_FEED_TYPE => self::FEED_TYPE_MOST_RECENT),
            array(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY)
        );

        $glyph = new FontAwesomeGlyph('history', array(), null, 'fas');
        $most_recent['class'] = $glyph->getClassNamesString();

        $menu_items[] = $most_recent;

        $most_interesting = array();
        $most_interesting['title'] = Translation::get('MostInteresting');
        $most_interesting['url'] = $this->get_url(
            array(self::PARAM_FEED_TYPE => self::FEED_TYPE_MOST_INTERESTING),
            array(ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY)
        );

        $glyph = new FontAwesomeGlyph('star', array(), null, 'fas');
        $most_interesting['class'] = $glyph->getClassNamesString();

        $menu_items[] = $most_interesting;

        return $menu_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_available_renderers()
     */

    /**
     *
     * @return string
     */
    public function get_repository_type()
    {
        return self::REPOSITORY_TYPE;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */

    public function support_sorting_direction()
    {
        return true;
    }

    public function validate_settings($external_repository)
    {
        $key = Setting::get('key', $external_repository->get_id());
        $secret = Setting::get('secret', $external_repository->get_id());

        if (!$key || !$secret)
        {
            return false;
        }

        return true;
    }
}
