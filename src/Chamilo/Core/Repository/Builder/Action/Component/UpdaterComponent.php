<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.component
 */
class UpdaterComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        $complex_content_object_item_id = Request :: get(
            \Chamilo\Core\Repository\Builder\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(
            \Chamilo\Core\Repository\Builder\Manager :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        $parameters = array(
            \Chamilo\Core\Repository\Builder\Manager :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item,
            \Chamilo\Core\Repository\Builder\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id);

        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_item(
            $complex_content_object_item_id);
        $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
            $complex_content_object_item->get_ref());

        if (! \Chamilo\Core\Repository\Publication\Storage\DataManager\DataManager :: is_content_object_editable(
            $content_object->get_id()))
        {
            $parameters[\Chamilo\Core\Repository\Builder\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
            $this->redirect(
                Translation :: get('UpdateNotAllowed'),
                false,
                array_merge(
                    $parameters,
                    array(
                        \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager :: ACTION_BROWSE)));
        }

        $complex_content_object_item_form = \Chamilo\Core\Repository\Form\ComplexContentObjectItemForm :: factory(
            $content_object->context(),
            $complex_content_object_item,
            $this->get_url());

        if ($complex_content_object_item_form instanceof \Chamilo\Core\Repository\Form\ComplexContentObjectItemForm)
        {
            $elements = $complex_content_object_item_form->get_elements();
            $defaults = $complex_content_object_item_form->get_default_values();
        }

        $content_object_form = ContentObjectForm :: factory(
            ContentObjectForm :: TYPE_EDIT,
            $content_object,
            'edit',
            'post',
            $this->get_url($parameters),
            null,
            $elements);
        $content_object_form->setDefaults($defaults);

        if ($content_object_form->validate())
        {
            $content_object_form->update_content_object();

            if ($content_object_form->is_version())
            {
                $old_id = $complex_content_object_item->get_ref();
                $new_id = $content_object->get_latest_version()->get_id();
                $complex_content_object_item->set_ref($new_id);

                $children = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
                    ComplexContentObjectItem :: class_name(),
                    new DataClassRetrievesParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                ComplexContentObjectItem :: class_name(),
                                ComplexContentObjectItem :: PROPERTY_PARENT),
                            new StaticConditionVariable($old_id))));
                while ($child = $children->next_result())
                {
                    $child->set_parent($new_id);
                    $child->update();
                }
            }

            if ($complex_content_object_item_form)
            {
                $complex_content_object_item_form->update_from_values($content_object_form->exportValues());
            }
            else
            {
                $complex_content_object_item->update();
            }

            $parameters[\Chamilo\Core\Repository\Builder\Manager :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;

            $this->redirect(
                Translation :: get(
                    'ObjectUpdated',
                    array('OBJECT' => Translation :: get('ContentObject')),
                    Utilities :: COMMON_LIBRARIES),
                false,
                array_merge(
                    $parameters,
                    array(
                        \Chamilo\Core\Repository\Builder\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Builder\Manager :: ACTION_BROWSE)));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add_help('repository builder');

            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb(
                    null,
                    Translation :: get(
                        'EditContentObject',
                        array(
                            'CONTENT_OBJECT' => $content_object->get_title(),
                            'ICON' => Theme :: getInstance()->getImage(
                                'Logo/16',
                                'png',
                                Translation :: get(
                                    'TypeName',
                                    null,
                                    ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                                        $content_object->get_type())),
                                null,
                                ToolbarItem :: DISPLAY_ICON,
                                false,
                                ClassnameUtilities :: getInstance()->getNamespaceFromClassname(
                                    $content_object->get_type()))),
                        \Chamilo\Core\Repository\Manager :: context())));

            $html = array();

            $html[] = $this->render_header();
            $html[] = $content_object_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}
