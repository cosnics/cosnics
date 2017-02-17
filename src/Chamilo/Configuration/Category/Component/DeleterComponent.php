<?php
namespace Chamilo\Configuration\Category\Component;

use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * $Id: deleter.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * 
 * @package application.common.category_manager.component
 */
/**
 * Component to delete a category
 * 
 * @author Sven Vanpoucke
 * @author Stijn Van Hoecke
 */
class DeleterComponent extends Manager
{

    /*
     * ¨* Variable that holds the parent to redirect to.
     */
    private $redirect_to_parent = 0;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = $this->getRequest()->get(self::PARAM_CATEGORY_ID);
        
        if (! $this->get_user())
        {
            throw new NotAllowedException();
        }
        
        if ($ids)
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $bool = true;
            
            foreach ($ids as $id)
            {
                if ($bool)
                {
                    $bool = $this->delete_categories_recursive($id);
                }
            }
            
            if (count($ids) == 1)
            {
                $message = $bool ? 'CategoryDeleted' : 'CategoryNotDeleted';
            }
            else
            {
                $message = $bool ? 'CategoriesDeleted' : 'CategoriesNotDeleted';
            }
            
            $this->redirect(
                Translation::get($message), 
                ($bool ? false : true), 
                array(
                    self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES, 
                    self::PARAM_CATEGORY_ID => $this->redirect_to_parent));
        }
        else
        {
            $trail = BreadcrumbTrail::getInstance();
            $trail->add_help('category_manager_deleter');
            $trail->add(
                new Breadcrumb(
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES)), 
                    Translation::get('BrowserComponent')));
            $this->set_parameter(self::PARAM_CATEGORY_ID, Request::get(self::PARAM_CATEGORY_ID));
            $trail->add(new Breadcrumb($this->get_url(), Translation::get('DeleterComponent')));
            
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = Translation::get("NoObjectSelected");
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function delete_categories_recursive($id)
    {
        $category_class_name = get_class($this->get_parent()->get_category());
        
        $continue = true;
        $children = $this->get_parent()->retrieve_categories(
            new EqualityCondition(
                new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_PARENT), 
                new StaticConditionVariable($id)));
        foreach ($children as $child)
        {
            $continue = $this->delete_categories_recursive($child);
        }
        
        if ($continue)
        {
            if ($this->get_parent()->allowed_to_delete_category($id))
            {
                $categories = $this->get_parent()->retrieve_categories(
                    new EqualityCondition(
                        new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_ID), 
                        new StaticConditionVariable($id)));
                $category = $categories->next_result();

                if(!$category instanceof PlatformCategory)
                {
                    return false;
                }

                $this->redirect_to_parent = $category->get_parent();
                
                return $category->delete();
            }
        }
        
        return false;
    }
}
