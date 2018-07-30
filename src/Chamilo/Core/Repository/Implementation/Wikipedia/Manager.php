<?php
namespace Chamilo\Core\Repository\Implementation\Wikipedia;

use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'wikipedia';

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        $url = Setting::get('url', $external_repository->get_id());
        
        if (! $url)
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
        return $menu_items;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array(self::ACTION_BROWSE_EXTERNAL_REPOSITORY);
        
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
        $image_types = Webpage::get_image_types();
        $image_conditions = array();
        foreach ($image_types as $image_type)
        {
            $image_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(Webpage::class_name(), Webpage::PROPERTY_FILENAME), 
                '*.htm' . $image_type, 
                Webpage::get_type_name());
        }
        
        return new OrCondition($image_conditions);
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
