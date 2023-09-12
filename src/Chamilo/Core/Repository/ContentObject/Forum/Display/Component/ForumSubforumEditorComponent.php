<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package repository.lib.complex_display.forum.component
 */
class ForumSubforumEditorComponent extends Manager implements DelegateComponent
{

    public function run()
    {
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $selected_complex_content_object_item = $this->get_selected_complex_content_object_item();

            $url = $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_EDIT_SUBFORUM,
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item->get_id(
                    )
                ]
            );

            $forum_object = DataManager::retrieve_by_id(
                ContentObject::class, $selected_complex_content_object_item->get_ref()
            );

            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                            self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null
                        ]
                    ), $this->get_root_content_object()->get_title()
                )
            );

            if ($this->get_complex_content_object_item())
            {

                $forums_with_key_cloi = [];
                $forums_with_key_cloi = $this->retrieve_children_from_root_to_cloi(
                    $this->get_root_content_object()->get_id(), $this->get_complex_content_object_item()->get_id()
                );

                if ($forums_with_key_cloi)
                {

                    foreach ($forums_with_key_cloi as $key => $value)
                    {

                        $this->getBreadcrumbTrail()->add(
                            new Breadcrumb(
                                $this->get_url(
                                    [
                                        self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                                        self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key
                                    ]
                                ), $value->get_title()
                            )
                        );
                    }
                }
                else
                {
                    throw new Exception('The forum you requested has not been found');
                }
            }

            $this->getBreadcrumbTrail()->add(
                new Breadcrumb(
                    $this->get_url(), Translation::get('SubforumEditor', ['SUBFORUM' => $forum_object->get_title()])
                )
            );

            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT, $this->getCurrentWorkspace(), $forum_object, 'edit',
                FormValidator::FORM_METHOD_POST, $url
            );

            if ($form->validate())
            {
                $success = $form->update_content_object();
                if ($form->is_version())
                {
                    $old_id = $selected_complex_content_object_item->get_ref();
                    $new_id = $forum_object->get_latest_version()->get_id();
                    $selected_complex_content_object_item->set_ref($new_id);
                    $selected_complex_content_object_item->update();

                    $children = DataManager::retrieve_complex_content_object_items(
                        ComplexContentObjectItem::class, new EqualityCondition(
                            new PropertyConditionVariable(
                                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                            ), new StaticConditionVariable($old_id), ComplexContentObjectItem::getStorageUnitName()
                        )
                    );
                    foreach ($children as $child)
                    {
                        $child->set_parent($new_id);
                        $child->update();
                    }
                }

                $this->my_redirect($success);
            }
            else
            {
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

    private function my_redirect($success)
    {
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectUpdated' : 'ObjectNotUpdated'), ['OBJECT' => Translation::get('Subforum')],
                StringUtilities::LIBRARIES
            )
        );

        $params = [];
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirectWithMessage($message, !$success, $params);
    }
}
