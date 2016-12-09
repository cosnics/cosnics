<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseCategoryEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\Helper\CourseCategoryEntityHelper;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\Helper\CourseEntityHelper;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\Helper\PlatformGroupEntityHelper;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\Helper\UserEntityHelper;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\PlatformGroupEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\UserEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

abstract class Manager extends Application
{
    // Actions
    const ACTION_CREATE = 'Creator';
    const ACTION_ENTITY = 'Entity';
    const ACTION_TARGET = 'Target';
    const ACTION_DELETE = 'Deleter';
    const ACTION_BROWSE = 'Browser';
    
    // Parameters
    const PARAM_ADMIN_ID = 'admin_id';
    const PARAM_ENTITY_TYPE = 'entity_type';
    const PARAM_ENTITY_ID = 'entity_id';
    const PARAM_TARGET_TYPE = 'target_type';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_CREATE;

    public function get_tabs($current_tab, $content)
    {
        $tabs = new DynamicVisualTabsRenderer(__CLASS__, $content);
        
        $tabs->add_tab(
            new DynamicVisualTab(
                self::ACTION_CREATE, 
                Translation::get(self::ACTION_CREATE . 'Component'), 
                Theme::getInstance()->getImagePath(self::package(), 'Tab/' . self::ACTION_CREATE), 
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE)), 
                ($current_tab == self::ACTION_CREATE ? true : false)));
        
        $count = DataManager::count(Admin::class_name());
        
        if ($count > 0)
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    self::ACTION_ENTITY, 
                    Translation::get(self::ACTION_ENTITY . 'Component'), 
                    Theme::getInstance()->getImagePath(self::package(), 'Tab/' . self::ACTION_ENTITY), 
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_ENTITY)), 
                    ($current_tab == self::ACTION_ENTITY ? true : false)));
        }
        
        if ($current_tab == self::ACTION_TARGET && $this->get_selected_entity_type() && $this->get_selected_entity_id())
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    self::ACTION_TARGET, 
                    Translation::get(self::ACTION_TARGET . 'Component'), 
                    Theme::getInstance()->getImagePath(self::package(), 'Tab/' . self::ACTION_TARGET), 
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_TARGET)), 
                    ($current_tab == self::ACTION_TARGET ? true : false)));
        }
        
        return $tabs;
    }

    public function get_entity_types()
    {
        $types = array();
        
        $types[] = UserEntity::class_name();
        $types[] = PlatformGroupEntity::class_name();
        
        return $types;
    }

    public function get_target_types()
    {
        $types = array();
        
        $types[] = CourseEntity::class_name();
        $types[] = CourseCategoryEntity::class_name();
        
        return $types;
    }

    public function get_selected_entity_type()
    {
        $selected_type = Request::get(self::PARAM_ENTITY_TYPE, UserEntity::ENTITY_TYPE);
        
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Admin::class_name(), Admin::PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable($selected_type));
        
        if (DataManager::count(Admin::class_name(), new DataClassCountParameters($condition)) == 0 &&
             $selected_type == UserEntity::ENTITY_TYPE)
        {
            return PlatformGroupEntity::ENTITY_TYPE;
        }
        else
        {
            return $selected_type;
        }
    }

    public function get_selected_target_type()
    {
        $selected_type = Request::get(self::PARAM_TARGET_TYPE, CourseEntity::ENTITY_TYPE);
        
        $conditions = array();
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class_name(), Admin::PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable($this->get_selected_entity_type()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class_name(), Admin::PROPERTY_ENTITY_ID), 
            new StaticConditionVariable($this->get_selected_entity_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class_name(), Admin::PROPERTY_TARGET_TYPE), 
            new StaticConditionVariable($selected_type));
        
        $condition = new AndCondition($conditions);
        
        if (DataManager::count(Admin::class_name(), new DataClassCountParameters($condition)) == 0 &&
             $selected_type == CourseEntity::ENTITY_TYPE)
        {
            return CourseCategoryEntity::ENTITY_TYPE;
        }
        else
        {
            return $selected_type;
        }
    }

    public function get_selected_entity_id()
    {
        return $this->getRequest()->get(self::PARAM_ENTITY_ID);
    }

    public function get_selected_target_id()
    {
        return Request::get(self::PARAM_TARGET_ID);
    }

    public function get_selected_entity_class($helper = false)
    {
        return self::get_selected_class($this->get_selected_entity_type(), $helper);
    }

    public function get_selected_target_class($helper = false)
    {
        return self::get_selected_class($this->get_selected_target_type(), $helper);
    }

    public static function get_selected_class($type, $helper = false)
    {
        switch ($type)
        {
            case UserEntity::ENTITY_TYPE :
                $class = UserEntity::class_name();
                break;
            case PlatformGroupEntity::ENTITY_TYPE :
                $class = PlatformGroupEntity::class_name();
                break;
            case CourseCategoryEntity::ENTITY_TYPE :
                $class = CourseCategoryEntity::class_name();
                break;
            case CourseEntity::ENTITY_TYPE :
                $class = CourseEntity::class_name();
                break;
        }
        
        if ($helper)
        {
            switch ($type)
            {
                case UserEntity::ENTITY_TYPE :
                    $class = UserEntityHelper::class_name();
                    break;
                case PlatformGroupEntity::ENTITY_TYPE :
                    $class = PlatformGroupEntityHelper::class_name();
                    break;
                case CourseCategoryEntity::ENTITY_TYPE :
                    $class = CourseCategoryEntityHelper::class_name();
                    break;
                case CourseEntity::ENTITY_TYPE :
                    $class = CourseEntityHelper::class_name();
                    break;
            }
        }
        
        return $class;
    }
}
