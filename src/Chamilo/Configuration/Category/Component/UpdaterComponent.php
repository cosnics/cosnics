<?php
namespace Chamilo\Configuration\Category\Component;

use Chamilo\Configuration\Category\Form\CategoryForm;
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
 * $Id: updater.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * 
 * @package application.common.category_manager.component
 */
class UpdaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $category_id = Request::get(self::PARAM_CATEGORY_ID);
        
        if (! $this->get_parent()->allowed_to_edit_category($category_id))
        {
            throw new NotAllowedException();
        }
        
        $user = $this->get_user();
        
        $category_class_name = get_class($this->get_parent()->get_category());
        $categories = $this->get_parent()->retrieve_categories(
            new EqualityCondition(
                new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_ID), 
                new StaticConditionVariable($category_id)));
        $category = $categories->next_result();
        
        $trail = BreadcrumbTrail::getInstance();
        $trail->add_help('category_manager_updater');
        $this->set_parameter(self::PARAM_CATEGORY_ID, Request::get(self::PARAM_CATEGORY_ID));
        $trail->add(
            new Breadcrumb(
                $this->get_url(), 
                Translation::get('UpdaterComponent', array('TITLE' => $category->get_name()))));
        
        $form = new CategoryForm(
            CategoryForm::TYPE_EDIT, 
            $this->get_url(array(self::PARAM_CATEGORY_ID => $category->get_id())), 
            $category, 
            $user, 
            $this);
        
        if ($form->validate())
        {
            $success = $form->update_category();
            
            $this->redirect(
                Translation::get($success ? 'CategoryUpdated' : 'CategoryNotUpdated'), 
                ($success ? false : true), 
                array(
                    self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES, 
                    self::PARAM_CATEGORY_ID => $category->get_parent()));
        }
        else
        {
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }
}
