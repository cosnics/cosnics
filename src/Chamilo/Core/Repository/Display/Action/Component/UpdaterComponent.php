<?php
namespace Chamilo\Core\Repository\Display\Action\Component;

use Chamilo\Core\Repository\Display\Action\Manager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @author Original author unknown
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UpdaterComponent extends Manager
{
    public function run()
    {
        $selected_complex_content_object_item = $this->get_selected_complex_content_object_item();
        $content_object = DataManager::retrieve_by_id(
            ContentObject::class, $selected_complex_content_object_item->get_ref()
        );

        if (!$content_object)
        {
            throw new NoObjectSelectedException(
                Translation::getInstance()->getTranslation('ContentObject', null, 'Chamilo\Core\Repository')
            );
        }

        $isOwner = $content_object->get_owner_id() == $this->getUser()->getId();

        if ($this->get_parent()->get_parent()->is_allowed_to_edit_content_object() || $isOwner)
        {
            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, $this->getCurrentWorkspace(), $content_object, 'edit',
                FormValidator::FORM_METHOD_POST, $this->get_url(
                [
                    \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Display\Manager::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_selected_complex_content_object_item_id(
                    ),
                    \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(
                    )
                ]
            )
            );

            if ($form->validate())
            {
                $succes = $form->update_content_object();
                if ($succes && $form->is_version())
                {
                    $old_id = $selected_complex_content_object_item->get_ref();
                    $new_id = $content_object->get_latest_version()->get_id();
                    $selected_complex_content_object_item->set_ref($new_id);
                    $selected_complex_content_object_item->update();
                    $condition = new EqualityCondition(
                        new PropertyConditionVariable(
                            ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                        ), new StaticConditionVariable($old_id), ComplexContentObjectItem::getStorageUnitName()
                    );
                    $parameters = new DataClassRetrievesParameters($condition);
                    $children = DataManager::retrieve_complex_content_object_items(
                        ComplexContentObjectItem::class, $parameters
                    );
                    $failures = 0;
                    foreach ($children as $child)
                    {
                        $child->set_parent($new_id);
                        if (!$child->update())
                        {
                            $failures ++;
                        }
                    }

                    $succes = ($succes) && ($failures == 0);
                }

                $message = htmlentities(
                    Translation::get(
                        ($succes ? 'ObjectUpdated' : 'ObjectNotUpdated'),
                        ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                    )
                );

                $params = [];
                $params[\Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] =
                    $this->get_complex_content_object_item_id();
                $params[\Chamilo\Core\Repository\Display\Manager::PARAM_ACTION] =
                    \Chamilo\Core\Repository\Display\Manager::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

                $this->redirectWithMessage($message, (!$succes), $params);
            }
            else
            {
                $trail = $this->getBreadcrumbTrail();
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            [
                                \Chamilo\Core\Repository\Display\Manager::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_selected_complex_content_object_item_id(
                                ),
                                \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(
                                )
                            ]
                        ), Translation::get('Edit', null, StringUtilities::LIBRARIES)
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
            throw new NotAllowedException();
        }
    }

    protected function getCurrentWorkspace(): Workspace
    {
        return $this->getService('Chamilo\Core\Repository\CurrentWorkspace');
    }
}
