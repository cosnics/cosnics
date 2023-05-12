<?php
namespace Chamilo\Configuration\Category\Component;

use Chamilo\Configuration\Category\Interfaces\CategoryVisibilitySupported;
use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Component that toggles the visibility of categories.
 * 
 * @author Tom Goethals
 */
class VisibilityTogglerComponent extends Manager
{

    /**
     * Toggles the visibility of the category passed by id, if possible.
     * If no category is found or the user has
     * insufficient rights, an error is shown. Otherwise, the change is made and the user is redirected to the previous
     * page.
     */
    public function run()
    {
        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_CATEGORY_ID);
        
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
                if (! $this->get_parent()->allowed_to_change_category_visibility($id))
                {
                    $bool = false;
                    continue;
                }
                
                $bool &= $this->toggle_category_visibility($id);
            }
            
            if (count($ids) == 1)
            {
                $message = $bool ? 'CategoryVisibilityToggled' : 'CategoryVisibilityNotToggled';
            }
            else
            {
                $message = $bool ? 'CategoriesVisibilityToggled' : 'CategoriesVisibilityNotToggled';
            }
            
            $this->redirectWithMessage(
                Translation::get($message), !$bool,
                array(
                    self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES, 
                    self::PARAM_CATEGORY_ID => $this->redirect_to_parent));
        }
        else
        {
            $trail = BreadcrumbTrail::getInstance();
            $trail->add(
                new Breadcrumb(
                    $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES)), 
                    Translation::get('BrowserComponent')));
            $this->set_parameter(self::PARAM_CATEGORY_ID, Request::get(self::PARAM_CATEGORY_ID));
            $trail->add(new Breadcrumb($this->get_url(), Translation::get('VisibilityTogglerComponent')));
            
            $html = [];
            
            $html[] = $this->render_header();
            $html[] = Translation::get("NoObjectSelected");
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    /**
     * Toggles the visibility on a category, if the category supports visibility toggling.
     * 
     * @param id The id of the category
     * @return True if the visibility has been succesfully changed, false otherwise.
     */
    public function toggle_category_visibility($id)
    {
        $category_class_name = get_class($this->get_parent()->getCategory());
        
        $categories = $this->get_parent()->retrieve_categories(
            new EqualityCondition(
                new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_ID), 
                new StaticConditionVariable($id)));
        $category = $categories->current();
        if ($category instanceof CategoryVisibilitySupported)
        {
            if ($this->get_parent()->allowed_to_change_category_visibility($id))
            {
                $this->redirect_to_parent = $category->get_parent();
                $category->toggle_visibility();
                return $category->update();
            }
        }
        return false;
    }
}
