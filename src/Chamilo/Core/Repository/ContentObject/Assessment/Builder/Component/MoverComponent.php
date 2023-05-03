<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.complex_builder.glossary.component
 */
class MoverComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = Request::get(Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request::get(
            Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID
        );
        $direction = Request::get(Manager::PARAM_DIRECTION);
        $succes = true;

        if (isset($id))
        {
            $complex_content_object_item = DataManager::retrieve_by_id(
                ComplexContentObjectItem::class, $id
            );
            $parent = $complex_content_object_item->get_parent();
            $max = DataManager::count_complex_content_object_items(
                ComplexContentObjectItem::class, new DataClassCountParameters(
                    new EqualityCondition(
                        new PropertyConditionVariable(
                            ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                        ), new StaticConditionVariable($parent)
                    )
                )
            );

            $display_order = $complex_content_object_item->get_display_order();
            $new_place =
                ($display_order + ($direction == \Chamilo\Core\Repository\Manager::PARAM_DIRECTION_UP ? - 1 : 1));

            if ($new_place > 0 && $new_place <= $max)
            {
                $complex_content_object_item->set_display_order($new_place);

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_DISPLAY_ORDER
                    ), new StaticConditionVariable($new_place), ComplexContentObjectItem::getStorageUnitName()
                );
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                    ), new StaticConditionVariable($parent), ComplexContentObjectItem::getStorageUnitName()
                );
                $condition = new AndCondition($conditions);
                $items = DataManager::retrieve_complex_content_object_items(
                    ComplexContentObjectItem::class, $condition
                );
                $new_complex_content_object_item = $items->current();
                $new_complex_content_object_item->set_display_order($display_order);

                if (!$complex_content_object_item->update() || !$new_complex_content_object_item->update())
                {
                    $succes = false;
                }
            }

            $this->redirectWithMessage(
                $succes ? Translation::get(
                    'ObjectsMoved', ['OBJECTS' => Translation::get('ComplexContentObjectItems')],
                    StringUtilities::LIBRARIES
                ) : Translation::get(
                    'ObjectsNotMoved', ['OBJECTS' => Translation::get('ComplexContentObjectItems')],
                    StringUtilities::LIBRARIES
                ), false, [
                    Manager::PARAM_ACTION => Manager::ACTION_BROWSE,
                    Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item
                ]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectsSelected', ['OBJECTS' => Translation::get('ContentObjectItems')],
                        StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }
}
