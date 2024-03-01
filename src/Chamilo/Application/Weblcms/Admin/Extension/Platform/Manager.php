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
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

abstract class Manager extends Application
{
    public const ACTION_BROWSE = 'Browser';
    public const ACTION_CREATE = 'Creator';
    public const ACTION_DELETE = 'Deleter';
    public const ACTION_ENTITY = 'Entity';
    public const ACTION_TARGET = 'Target';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_CREATE;

    public const PARAM_ADMIN_ID = 'admin_id';
    public const PARAM_ENTITY_ID = 'entity_id';
    public const PARAM_ENTITY_TYPE = 'entity_type';
    public const PARAM_TARGET_TYPE = 'target_type';

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function get_entity_types()
    {
        $types = [];

        $types[] = UserEntity::class;
        $types[] = PlatformGroupEntity::class;

        return $types;
    }

    public static function get_selected_class($type, $helper = false)
    {
        switch ($type)
        {
            case UserEntity::ENTITY_TYPE :
                $class = UserEntity::class;
                break;
            case PlatformGroupEntity::ENTITY_TYPE :
                $class = PlatformGroupEntity::class;
                break;
            case CourseCategoryEntity::ENTITY_TYPE :
                $class = CourseCategoryEntity::class;
                break;
            case CourseEntity::ENTITY_TYPE :
                $class = CourseEntity::class;
                break;
        }

        if ($helper)
        {
            switch ($type)
            {
                case UserEntity::ENTITY_TYPE :
                    $class = UserEntityHelper::class;
                    break;
                case PlatformGroupEntity::ENTITY_TYPE :
                    $class = PlatformGroupEntityHelper::class;
                    break;
                case CourseCategoryEntity::ENTITY_TYPE :
                    $class = CourseCategoryEntityHelper::class;
                    break;
                case CourseEntity::ENTITY_TYPE :
                    $class = CourseEntityHelper::class;
                    break;
            }
        }

        return $class;
    }

    public function get_selected_entity_class($helper = false)
    {
        return self::get_selected_class($this->get_selected_entity_type(), $helper);
    }

    public function get_selected_entity_id()
    {
        return $this->getRequest()->getFromRequestOrQuery(self::PARAM_ENTITY_ID);
    }

    public function get_selected_entity_type()
    {
        $selected_type = $this->getRequest()->query->get(self::PARAM_ENTITY_TYPE, UserEntity::ENTITY_TYPE);

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($selected_type)
        );

        if (DataManager::count(Admin::class, new DataClassCountParameters($condition)) == 0 &&
            $selected_type == UserEntity::ENTITY_TYPE)
        {
            return PlatformGroupEntity::ENTITY_TYPE;
        }
        else
        {
            return $selected_type;
        }
    }

    public function get_selected_target_class($helper = false)
    {
        return self::get_selected_class($this->get_selected_target_type(), $helper);
    }

    public function get_selected_target_type()
    {
        $selected_type = $this->getRequest()->query->get(self::PARAM_TARGET_TYPE, CourseEntity::ENTITY_TYPE);

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->get_selected_entity_type())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($this->get_selected_entity_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_TARGET_TYPE),
            new StaticConditionVariable($selected_type)
        );

        $condition = new AndCondition($conditions);

        if (DataManager::count(Admin::class, new DataClassCountParameters($condition)) == 0 &&
            $selected_type == CourseEntity::ENTITY_TYPE)
        {
            return CourseCategoryEntity::ENTITY_TYPE;
        }
        else
        {
            return $selected_type;
        }
    }

    public function get_tabs($current_tab): TabsCollection
    {
        $tabs = new TabsCollection();

        $tabs->add(
            new LinkTab(
                self::ACTION_CREATE, Translation::get(self::ACTION_CREATE . 'Component'),
                new FontAwesomeGlyph('plus', ['fa-lg'], null, 'fas'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE]), $current_tab == self::ACTION_CREATE
            )
        );

        $count = DataManager::count(Admin::class);

        if ($count > 0)
        {
            $tabs->add(
                new LinkTab(
                    self::ACTION_ENTITY, Translation::get(self::ACTION_ENTITY . 'Component'),
                    new FontAwesomeGlyph('chess-pawn', ['fa-lg'], null, 'fas'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_ENTITY]), $current_tab == self::ACTION_ENTITY
                )
            );
        }

        if ($current_tab == self::ACTION_TARGET && $this->get_selected_entity_type() && $this->get_selected_entity_id())
        {
            $tabs->add(
                new LinkTab(
                    self::ACTION_TARGET, Translation::get(self::ACTION_TARGET . 'Component'),
                    new FontAwesomeGlyph('bullseye', ['fa-lg'], null, 'fas'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_TARGET]), $current_tab == self::ACTION_TARGET
                )
            );
        }

        return $tabs;
    }

    public function get_target_types()
    {
        $types = [];

        $types[] = CourseEntity::class;
        $types[] = CourseCategoryEntity::class;

        return $types;
    }
}
