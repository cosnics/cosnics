<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
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
                array(
                    self::PARAM_ACTION => self::ACTION_EDIT_SUBFORUM,
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item->get_id()));

            $forum_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $selected_complex_content_object_item->get_ref());

            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                            self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)),
                    $this->get_root_content_object()->get_title()));

            if ($this->get_complex_content_object_item())
            {

                $forums_with_key_cloi = array();
                $forums_with_key_cloi = $this->retrieve_children_from_root_to_cloi(
                    $this->get_root_content_object()->get_id(),
                    $this->get_complex_content_object_item()->get_id());

                if ($forums_with_key_cloi)
                {

                    foreach ($forums_with_key_cloi as $key => $value)
                    {

                        BreadcrumbTrail::getInstance()->add(
                            new Breadcrumb(
                                $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                                        self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key)),
                                $value->get_title()));
                    }
                }
                else
                {
                    throw new \Exception('The forum you requested has not been found');
                }
            }
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb(
                    $this->get_url(),
                    Translation::get('SubforumEditor', array('SUBFORUM' => $forum_object->get_title()))));
            $form = ContentObjectForm::factory(
                ContentObjectForm::TYPE_EDIT,
                new PersonalWorkspace($this->get_user()),
                $forum_object,
                'edit',
                'post',
                $url);

            if ($form->validate())
            {
                $success = $form->update_content_object();
                if ($form->is_version())
                {
                    $old_id = $selected_complex_content_object_item->get_ref();
                    $new_id = $forum_object->get_latest_version()->get_id();
                    $selected_complex_content_object_item->set_ref($new_id);
                    $selected_complex_content_object_item->update();

                    $children = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
                        ComplexContentObjectItem::class_name(),
                        new EqualityCondition(
                            new PropertyConditionVariable(
                                ComplexContentObjectItem::class_name(),
                                ComplexContentObjectItem::PROPERTY_PARENT),
                            new StaticConditionVariable($old_id),
                            ComplexContentObjectItem::get_table_name()));
                    while ($child = $children->next_result())
                    {
                        $child->set_parent($new_id);
                        $child->update();
                    }
                }

                $this->my_redirect($success);
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
            throw new NotAllowedException();
        }
    }

    private function my_redirect($success)
    {
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectUpdated' : 'ObjectNotUpdated'),
                array('OBJECT' => Translation::get('Subforum')),
                Utilities::COMMON_LIBRARIES));

        $params = array();
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_FORUM;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();

        $this->redirect($message, ($success ? false : true), $params);
    }
}
