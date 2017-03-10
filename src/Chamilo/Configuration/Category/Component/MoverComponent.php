<?php
namespace Chamilo\Configuration\Category\Component;

use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: mover.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * 
 * @package application.common.category_manager.component
 */
class MoverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $category_id = Request::get(self::PARAM_CATEGORY_ID);
        $direction = Request::get(self::PARAM_DIRECTION);
        $user = $this->get_user();
        
        if (! isset($user) || ! isset($category_id) || ! $this->get_parent()->allowed_to_edit_category($category_id))
        {
            throw new NotAllowedException();
        }
        
        $category_class_name = get_class($this->get_parent()->get_category());
        
        $categories = $this->get_parent()->retrieve_categories(
            new EqualityCondition(
                new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_ID), 
                new StaticConditionVariable($category_id)));
        $category = $categories->next_result();
        $parent = $category->get_parent();
        
        $max = $this->get_parent()->count_categories(
            new EqualityCondition(
                new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_PARENT), 
                new StaticConditionVariable($parent)));
        
        $display_order = $category->get_display_order();
        $new_place = $display_order + $direction;
        
        $succes = false;
        
        if ($new_place > 0 && $new_place <= $max)
        {
            $category->set_display_order($new_place);
            
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_DISPLAY_ORDER), 
                new StaticConditionVariable($new_place));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_PARENT), 
                new StaticConditionVariable($parent));
            $condition = new AndCondition($conditions);
            $categories = $this->get_parent()->retrieve_categories($condition);
            $newcategory = $categories->next_result();

            if($newcategory)
            {
                $newcategory->set_display_order($display_order);

                if ($category->update() && $newcategory->update())
                {
                    $sucess = true;
                }
            }
        }
        
        $this->redirect(
            Translation::get($sucess ? 'CategoryMoved' : 'CategoryNotMoved'), 
            ($sucess ? false : true), 
            array(
                self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES, 
                self::PARAM_CATEGORY_ID => $category->get_parent()));
    }
}
