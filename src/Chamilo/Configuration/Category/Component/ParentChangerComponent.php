<?php
namespace Chamilo\Configuration\Category\Component;

use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: parent_changer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 *
 * @package application.common.category_manager.component
 */
class ParentChangerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $user = $this->get_user();

        $ids = $this->getRequest()->request->get(self::PARAM_CATEGORY_ID);
        if (empty($ids))
        {
            $ids = $this->getRequest()->query->get(self::PARAM_CATEGORY_ID);
        }

        if (!$user)
        {
            throw new NotAllowedException();
        }

        if (!is_array($ids))
        {
            $ids = array($ids);
        }

        $this->set_parameter(self::PARAM_CATEGORY_ID, $ids);

        $category_class_name = get_class($this->get_parent()->get_category());

        if (count($ids) != 0)
        {
            $selected_category = $this->get_parent()->retrieve_categories(
                new EqualityCondition(
                    new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_ID),
                    new StaticConditionVariable($ids[0])
                ),
                null,
                null,
                array()
            )->next_result();

            if (!$selected_category)
            {
                throw new ObjectNotExistException(Translation::get('Category'), $ids[0]);
            }

            $parent = $selected_category->get_parent();

            $success = true;

            $categories = array();

            foreach ($ids as $id)
            {
                if ($this->get_parent()->allowed_to_edit_category($id))
                {
                    $categories[] = $this->get_parent()->retrieve_categories(
                        new EqualityCondition(
                            new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_ID),
                            new StaticConditionVariable($id)
                        ),
                        null,
                        null,
                        array()
                    )->next_result();
                }
            }

            $form = $this->get_move_form($categories, $parent);
            $trail = BreadcrumbTrail::getInstance();
            $trail->add_help('category_manager_parent_changer');

            if (count($ids) > 1)
            {
                $trail->add(new Breadcrumb($this->get_url(), Translation::get('ParentChangerComponent')));
            }
            else
            {
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(),
                        Translation::get('MoveCategory', array('CATEGORY' => $categories[0]->get_name()))
                    )
                );
            }

            if ($form->validate())
            {
                $new_parent = $form->exportValue('category');

                if (!$this->get_parent()->allowed_to_add_category($new_parent))
                {
                    throw new NotAllowedException();
                }

                foreach ($categories as $category)
                {
                    $category->set_parent($new_parent);
                    $category->set_display_order($this->get_parent()->get_next_category_display_order($new_parent));

                    $success &= $category->update(true);
                }

                $this->clean_display_order_old_parent($parent);

                $this->redirect(
                    Translation::get($success ? 'CategoryMoved' : 'CategoryNotMoved'),
                    ($success ? false : true),
                    array(self::PARAM_ACTION => self::ACTION_BROWSE_CATEGORIES, self::PARAM_CATEGORY_ID => $parent)
                );
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
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = Display::error_message(Translation::get('NoObjectSelected', null, Utilities::COMMON_LIBRARIES));
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    private $tree;

    public function get_move_form($categories, $current_parent)
    {
        foreach ($categories as $category)
        {
            $selected_categories[] = $category->get_id();
        }

        if ($current_parent != 0)
        {
            $this->tree[0] = Translation::get('Root');
        }

        $this->build_category_tree(0, $selected_categories, $current_parent);
        $form = new FormValidator(
            'select_category',
            'post',
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_CHANGE_CATEGORY_PARENT
                )
            )
        );

        foreach ($categories as $category)
        {
            $category_names[] = $category->get_name();
        }
        $form->addElement(
            'static',
            null,
            Translation::get(
                'ObjectSelected',
                array('OBJECT' => Translation::get(count($selected_categories) > 1 ? 'Categories' : 'Category')),
                Utilities::COMMON_LIBRARIES
            ),
            implode('<br>', $category_names)
        );

        $form->addElement(
            'select',
            'category',
            Translation::get('Category', null, Utilities::COMMON_LIBRARIES),
            $this->tree
        );
        $form->addElement('submit', 'submit', Translation::get('Ok', null, Utilities::COMMON_LIBRARIES));

        return $form;
    }

    private $level = 1;

    public function build_category_tree($parent_id, $selected_categories, $current_parent)
    {
        $category_class_name = get_class($this->get_parent()->get_category());
        $condition = new EqualityCondition(
            new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_PARENT),
            new StaticConditionVariable($parent_id)
        );

        $categories = $this->get_parent()->retrieve_categories($condition, null, null, array());

        $tree = array();
        while ($cat = $categories->next_result())
        {
            if (in_array($cat->get_id(), $selected_categories))
            {
                continue;
            }

            if ($cat->get_id() != $current_parent)
            {
                $this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
            }

            $this->level ++;
            $this->build_category_tree($cat->get_id(), $selected_categories, $current_parent);
            $this->level --;
        }
    }

    public function clean_display_order_old_parent($parent)
    {
        $category_class_name = get_class($this->get_parent()->get_category());
        $condition = new EqualityCondition(
            new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_PARENT),
            new StaticConditionVariable($parent)
        );

        $categories = $this->get_parent()->retrieve_categories(
            $condition,
            null,
            null,
            array(
                new OrderBy(
                    new PropertyConditionVariable($category_class_name, PlatformCategory::PROPERTY_DISPLAY_ORDER)
                )
            )
        );

        $i = 1;

        while ($cat = $categories->next_result())
        {
            $cat->set_display_order($i);
            $cat->update();
            $i ++;
        }
    }
}
