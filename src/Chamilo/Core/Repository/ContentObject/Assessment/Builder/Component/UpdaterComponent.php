<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\Assessment\Builder\Manager;
use Chamilo\Core\Repository\Form\ComplexContentObjectItemForm;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.complex_builder.glossary.component
 */
class UpdaterComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();

        $complex_content_object_item_id = Request::get(
            Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
        );
        $parent_complex_content_object_item = Request::get(
            Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID
        );

        $parameters = [
            Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item,
            Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id
        ];

        $complex_content_object_item = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class, $complex_content_object_item_id
        );
        $content_object = DataManager::retrieve_by_id(
            ContentObject::class, $complex_content_object_item->get_ref()
        );

        if (!$this->getPublicationAggregator()->canContentObjectBeEdited($content_object->get_id()))
        {
            $parameters[Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
            $this->redirectWithMessage(
                Translation::get('UpdateNotAllowed'), false, array_merge(
                    $parameters, [
                        Manager::PARAM_ACTION => Manager::ACTION_BROWSE
                    ]
                )
            );
        }

        $complex_content_object_item_form = ComplexContentObjectItemForm::factory(
            $content_object->context(), $complex_content_object_item, $this->get_url()
        );

        if ($complex_content_object_item_form instanceof ComplexContentObjectItemForm)
        {
            $elements = $complex_content_object_item_form->get_elements();
            $defaults = $complex_content_object_item_form->get_default_values();
        }

        $content_object_form = ContentObjectForm::factory(
            ContentObjectForm::TYPE_EDIT, $this->getCurrentWorkspace(), $content_object, 'edit',
            FormValidator::FORM_METHOD_POST, $this->get_url($parameters), null, $elements
        );
        $content_object_form->setDefaults($defaults);

        if ($content_object_form->validate())
        {
            $content_object_form->update_content_object();

            if ($content_object_form->is_version())
            {
                $old_id = $complex_content_object_item->get_ref();
                $new_id = $content_object->get_latest_version()->get_id();
                $complex_content_object_item->set_ref($new_id);

                $children = DataManager::retrieve_complex_content_object_items(
                    ComplexContentObjectItem::class, new DataClassRetrievesParameters(
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                            ), new StaticConditionVariable($old_id)
                        )
                    )
                );
                foreach ($children as $child)
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

            $parameters[Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;

            $this->redirectWithMessage(
                Translation::get(
                    'ObjectUpdated', ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                ), false, array_merge(
                    $parameters, [
                        Manager::PARAM_ACTION => Manager::ACTION_BROWSE
                    ]
                )
            );
        }
        else
        {
            $trail = BreadcrumbTrail::getInstance();

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    null, Translation::get(
                    'EditContentObject', ['CONTENT_OBJECT' => $content_object->get_title()],
                    \Chamilo\Core\Repository\Manager::context()
                )
                )
            );

            $html = [];

            $html[] = $this->render_header();
            $html[] = $content_object_form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationAggregator
     */
    protected function getPublicationAggregator()
    {
        return $this->getService(PublicationAggregator::class);
    }
}
