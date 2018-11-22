<?php

namespace Chamilo\Core\Menu\Storage\DataClass;

use Chamilo\Core\Menu\ItemTitles;
use Chamilo\Core\Menu\Rights;
use Chamilo\Core\Menu\Storage\DataManager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\RepositoryImplementationCategoryItem;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Menu\Storage\DataClass\WorkspaceCategoryItem;
use Chamilo\Core\User\Integration\Chamilo\Core\Menu\Storage\DataClass\WidgetItem;
use Chamilo\Libraries\Storage\DataClass\CompositeDataClass;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListener;
use Chamilo\Libraries\Storage\DataClass\Listeners\DisplayOrderDataClassListenerSupport;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @package Chamilo\Core\Menu\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Item extends CompositeDataClass implements DisplayOrderDataClassListenerSupport
{

    // Properties
    const DISPLAY_BOTH = 3;

    const DISPLAY_ICON = 1;

    const DISPLAY_TEXT = 2;

    const PROPERTY_DISPLAY = 'display';

    const PROPERTY_HIDDEN = 'hidden';

    // Types

    const PROPERTY_ICON_CLASS = 'icon_class';

    const PROPERTY_PARENT = 'parent';

    const PROPERTY_SORT = 'sort';

    const TYPE_APPLICATION = 1;

    // Display options

    const TYPE_CATEGORY = 3;

    const TYPE_LINK = 2;

    const TYPE_LINK_APPLICATION = 4;

    private $titles;

    public function __construct($default_properties = array(), $additional_properties = null)
    {
        parent::__construct($default_properties, $additional_properties);
        $this->add_listener(new DisplayOrderDataClassListener($this));
    }

    public function delete()
    {
        $location = Rights::getInstance()->get_location_by_identifier(
            \Chamilo\Core\Menu\Manager::context(), Rights::TYPE_ITEM, $this->get_id()
        );

        if ($location)
        {
            if (!$location->delete())
            {
                return false;
            }
        }
        $success = parent::delete($this);
        if (!$success)
        {
            return false;
        }

        foreach ($this->get_titles()->get_titles() as $title)
        {
            if (!$title->delete())
            {
                return false;
            }
        }

        return true;
    }

    public function getIconClass()
    {
        return $this->get_default_property(self::PROPERTY_ICON_CLASS);
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Get the default properties of all items.
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_PARENT, self::PROPERTY_TYPE, self::PROPERTY_SORT, self::PROPERTY_HIDDEN,
                self::PROPERTY_DISPLAY, self::PROPERTY_ICON_CLASS
            )
        );
    }

    public function get_display()
    {
        return $this->get_default_property(self::PROPERTY_DISPLAY);
    }

    public function get_display_order_context_properties()
    {
        return array(new PropertyConditionVariable(Item::class_name(), self::PROPERTY_PARENT));
    }

    public function get_display_order_property()
    {
        return new PropertyConditionVariable(Item::class_name(), self::PROPERTY_SORT);
    }

    public function get_hidden()
    {
        return $this->get_default_property(self::PROPERTY_HIDDEN);
    }

    public function get_parent()
    {
        return $this->get_default_property(self::PROPERTY_PARENT);
    }

    public function get_parent_object()
    {
        return DataManager::retrieve_by_id(Item::class_name(), $this->get_parent());
    }

    public function get_sort()
    {
        return $this->get_default_property(self::PROPERTY_SORT);
    }

    public function get_titles()
    {
        if (!isset($this->titles))
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(ItemTitle::class_name(), ItemTitle::PROPERTY_ITEM_ID),
                new StaticConditionVariable($this->get_id())
            );
            $parameters = new DataClassRetrievesParameters(
                $condition, null, null,
                array(new OrderBy(new PropertyConditionVariable(ItemTitle::class_name(), ItemTitle::PROPERTY_SORT)))
            );
            $titles = DataManager::retrieves(ItemTitle::class_name(), $parameters);

            $this->titles = new ItemTitles($titles);
        }

        return $this->titles;
    }

    public function set_titles($titles)
    {
        $this->titles = $titles;
    }

    /**
     *
     * @return string
     */
    public function get_type_integer()
    {
        return self::type_integer($this->get_type());
    }

    /**
     *
     * @return string
     */
    public function get_type_string()
    {
        return self::type_string($this->get_type());
    }

    public function hasParent()
    {
        return $this->get_parent() != 0;
    }

    /**
     *
     * @return boolean
     */
    public function is_hidden()
    {
        return (bool) $this->get_hidden();
    }

    public function setIconClass($iconClass = '')
    {
        $this->set_default_property(self::PROPERTY_ICON_CLASS, $iconClass);
    }

    public function set_display($display = self::DISPLAY_ICON)
    {
        $this->set_default_property(self::PROPERTY_DISPLAY, $display);
    }

    public function set_hidden($hidden = 0)
    {
        $this->set_default_property(self::PROPERTY_HIDDEN, $hidden);
    }

    public function set_parent($parent)
    {
        $this->set_default_property(self::PROPERTY_PARENT, $parent);
    }

    public function set_sort($sort)
    {
        $this->set_default_property(self::PROPERTY_SORT, $sort);
    }

    public function show_icon()
    {
        return $this->get_display() == self::DISPLAY_BOTH || $this->get_display() == self::DISPLAY_ICON;
    }

    public function show_title()
    {
        return $this->get_display() == self::DISPLAY_TEXT || $this->get_display() == self::DISPLAY_BOTH;
    }

    /**
     *
     * @return string
     */
    public static function type_integer($type)
    {
        switch ($type)
        {
            case ApplicationItem::class_name() :
                return self::TYPE_APPLICATION;
                break;
            case LinkItem::class_name() :
                return self::TYPE_LINK;
                break;
            case CategoryItem::class_name() :
                return self::TYPE_CATEGORY;
                break;
            case LinkApplicationItem::class_name() :
                return self::TYPE_LINK_APPLICATION;
                break;
            case LanguageCategoryItem::class_name() :
                return self::TYPE_CATEGORY;
                break;
            case RepositoryImplementationCategoryItem::class_name() :
                return self::TYPE_APPLICATION;
                break;
            case WorkspaceCategoryItem::class_name() :
                return self::TYPE_APPLICATION;
                break;
            case WidgetItem::class_name() :
                return self::TYPE_APPLICATION;
                break;
        }
    }

    /*
     * (non-PHPdoc) @see \libraries\storage\DisplayOrderDataClassListenerSupport::get_display_order_property()
     */

    /**
     *
     * @return string
     */
    public static function type_string($type)
    {
        switch ($type)
        {
            case ApplicationItem::class_name() :
                return 'Application';
                break;
            case LinkItem::class_name() :
                return 'Link';
                break;
            case CategoryItem::class_name() :
                return 'Category';
                break;
            case LinkApplicationItem::class_name() :
                return 'LinkApplication';
                break;
        }
    }

    /*
     * (non-PHPdoc) @see \libraries\storage\DisplayOrderDataClassListenerSupport::get_display_order_context_properties()
     */

    public function update()
    {
        $success = parent::update($this);
        if (!$success)
        {
            return false;
        }

        $parent = $this->get_parent();

        if ($parent == 0)
        {
            $parent_id = Rights::getInstance()->get_root_id(\Chamilo\Core\Menu\Manager::context());
        }
        else
        {
            $parent_id = Rights::getInstance()->get_location_id_by_identifier(
                \Chamilo\Core\Menu\Manager::context(), Rights::TYPE_ITEM, $parent
            );
        }

        foreach ($this->get_titles()->get_titles() as $title)
        {
            if ($title->get_id())
            {
                if (!$title->update())
                {
                    return false;
                }
            }
            else
            {
                if (!$title->create())
                {
                    return false;
                }
            }
        }

        $location = Rights::getInstance()->get_location_by_identifier(
            \Chamilo\Core\Menu\Manager::context(), Rights::TYPE_ITEM, $this->get_id()
        );

        if ($location)
        {
            return $location->move($parent_id);
        }
        else
        {
            return false;
        }

        return true;
    }
}
