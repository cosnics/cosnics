<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.complex_builder.component
 */
class ParentChangerComponent extends Manager
{
    const PARAM_NEW_PARENT = 'new_parent';

    public function run()
    {
        $complex_content_object_item_ids = $this->getRequest()->get(
            \Chamilo\Core\Repository\Builder\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
        );
        $parent_complex_content_object_item = Request::get(
            \Chamilo\Core\Repository\Builder\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID
        );
        $root_content_object = $this->get_root_content_object();

        $parameters = array(
            \Chamilo\Core\Repository\Builder\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item,
            \Chamilo\Core\Repository\Builder\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_ids
        );

        if (!empty($complex_content_object_item_ids))
        {
            if (!is_array($complex_content_object_item_ids))
            {
                $complex_content_object_item_ids = array($complex_content_object_item_ids);
            }

            $parents = $this->get_possible_parents($root_content_object, $parent_complex_content_object_item);

            $form = new FormValidator('move', FormValidator::FORM_METHOD_POST, $this->get_url($parameters));
            $form->addElement('select', self::PARAM_NEW_PARENT, Translation::get('NewParent'), $parents);
            $form->addElement('submit', 'submit', Translation::get('Move', null, Utilities::COMMON_LIBRARIES));
            if ($form->validate())
            {
                $selected_parent = $form->exportValue(self::PARAM_NEW_PARENT);
                if ($selected_parent == 0)
                {
                    $parent = $root_content_object->get_id();
                }
                else
                {
                    $parent = DataManager::retrieve_by_id(
                        ComplexContentObjectItem::class, $selected_parent
                    );
                    $parent = $parent->get_ref();
                }

                $failures = 0;
                $size = 0;

                if ((!$parent_complex_content_object_item && $parent != $root_content_object->get_id()) ||
                    $parent_complex_content_object_item != $selected_parent)
                {
                    $complex_content_object_items = DataManager::retrieve_complex_content_object_items(
                        ComplexContentObjectItem::class, new InCondition(
                            new PropertyConditionVariable(
                                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_ID
                            ), $complex_content_object_item_ids
                        )
                    );
                    $size = $complex_content_object_items->count();
                    $old_parent = 0;

                    foreach ($complex_content_object_items as $complex_content_object_item)
                    {
                        if (!$old_parent)
                        {
                            $old_parent = $complex_content_object_item->get_parent();
                        }
                        $children = [];
                        $ref = $complex_content_object_item->get_ref();
                        $children = $this->get_children_from_content_object($ref, null, $children, $level = 1);

                        if ($ref != $parent && !array_key_exists($selected_parent, $children))
                        {
                            $complex_content_object_item->set_parent($parent);
                            $complex_content_object_item->set_display_order(
                                DataManager::select_next_display_order($parent)
                            );
                            $complex_content_object_item->update();
                        }
                        else
                        {
                            $failures ++;
                        }
                    }

                    $this->fix_display_order_values($old_parent);
                }

                if ($failures == 0)
                {
                    if ($size > 1)
                    {
                        $message = 'ObjectMoved';
                    }
                    else
                    {
                        $message = 'ObjectsMoved';
                    }
                }
                else
                {
                    if ($size > 1)
                    {
                        $message = 'ObjectNotMoved';
                    }
                    else
                    {
                        $message = 'ObjectsNotMoved';
                    }
                }

                $parameters[\Chamilo\Core\Repository\Builder\Manager::PARAM_ACTION] =
                    \Chamilo\Core\Repository\Builder\Manager::ACTION_BROWSE;
                $parameters[\Chamilo\Core\Repository\Builder\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] =
                    null;
                $this->redirect(
                    Translation::get(
                        $message, array('OBJECTS' => Translation::get('ComplexContentObjectItems')),
                        Utilities::COMMON_LIBRARIES
                    ), ($failures > 0), $parameters
                );
            }
            else
            {
                $menu_trail = $this->get_complex_content_object_breadcrumbs();
                $menu_trail->add(
                    new Breadcrumb(
                        $this->get_url($parameters), Translation::get('Move', null, Utilities::COMMON_LIBRARIES)
                    )
                );

                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', array('OBJECT' => Translation::get('ContentObject')),
                        Utilities::COMMON_LIBRARIES
                    )
                )
            );
        }
    }

    private function fix_display_order_values($parent_id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($parent_id)
        );
        $parameters = new DataClassRetrievesParameters(
            $condition, null, null, new OrderBy(array(
                new OrderProperty(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER
                    )
                )
            ))
        );
        $complex_content_object_items = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $parameters
        );

        $i = 1;

        foreach ($complex_content_object_items as $complex_content_object_item)
        {
            $complex_content_object_item->set_display_order($i);
            $complex_content_object_item->update();
            $i ++;
        }
    }

    private function get_children_from_content_object($content_object_id, $current_parent, $parents, $level = 1)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($content_object_id)
        );
        $children = DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $condition
        );

        foreach ($children as $child)
        {
            $ref_id = $child->get_ref();
            $ref_object = DataManager::retrieve_by_id(
                ContentObject::class, $ref_id
            );

            if (!$ref_object instanceof ComplexContentObjectSupport)
            {
                continue;
            }

            if ($child->get_id() == $current_parent)
            {
                $current = ' (' . Translation::get('Current') . ')';
            }
            else
            {
                $current = '';
            }

            $parents[$child->get_id()] = str_repeat('--', $level) . ' ' . $ref_object->get_title() . $current;

            $parents = $this->get_children_from_content_object($ref_id, $current_parent, $parents, $level + 1);
        }

        return $parents;
    }

    private function get_possible_parents($root_content_object, $parent_complex_content_object_item)
    {
        if (!$parent_complex_content_object_item)
        {
            $current = ' (' . Translation::get('Current', null, Utilities::COMMON_LIBRARIES) . ')';
        }

        $parents = array(0 => $root_content_object->get_title() . $current);
        $parents = $this->get_children_from_content_object(
            $root_content_object->get_id(), $parent_complex_content_object_item, $parents
        );

        return $parents;
    }
}
