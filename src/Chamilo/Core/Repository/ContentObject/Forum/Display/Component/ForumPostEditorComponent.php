<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Component\ForumPostFormAction\ForumPostFormAction;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Form\ForumPostForm;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumPostEditorComponent extends ForumPostFormAction
{

    private $forumpost;

    private $selected_forum_post_id;

    /**
     * Makes a new editing form and if its validate edits the post.
     */
    public function run()
    {
        $this->selected_forum_post_id = Request::get(self::PARAM_SELECTED_FORUM_POST);
        $this->forumpost = DataManager::retrieve_by_id(ForumPost::class, $this->selected_forum_post_id);

        if ($this->forumpost->get_user_id() == $this->get_user_id() || $this->get_parent()->is_allowed(EDIT_RIGHT))
        {

            $form = new ForumPostForm(
                ForumPostForm::TYPE_EDIT, $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_EDIT_FORUM_POST,
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                    self::PARAM_SELECTED_FORUM_POST => $this->selected_forum_post_id
                )
            ), $this->forumpost, $this->selected_forum_post_id
            );

            if ($form->validate())
            {

                $values = $form->exportValues();
                $this->forumpost->set_title($values[ForumPost::PROPERTY_TITLE]);
                $this->forumpost->set_content($values[ForumPost::PROPERTY_CONTENT]);

                $success = $this->forumpost->update();

                if ($success)
                {

                    // Process attachments
                    // Add the new attachments after the edit.
                    foreach ($values['attachments']['content_object'] as $value)
                    {
                        $help = $value;
                        if (DataManager::retrieve_attached_object($this->selected_forum_post_id, $help) == null)
                        {
                            $this->forumpost->attach_content_object($value, ContentObject::ATTACHMENT_NORMAL);
                        }
                    }
                }

                // Remove the attachments that were removed during the edit.
                foreach ($this->forumpost->get_attached_content_objects() as $object)
                {
                    $counter = 0;
                    $found = false;
                    $new_list_counter = count($values['attachments']['content_object']);
                    while ($counter < $new_list_counter)
                    {
                        if ($object->get_id() == $values['attachments']['content_object'][$counter])
                        {
                            $found = true;
                            break;
                        }
                        else
                        {
                            $counter ++;
                        }
                    }
                    if (!$found)
                    {
                        DataManager::detach_content_object($this->forumpost, $object->get_id());
                    }
                }

                $this->my_redirect($success);
            }
            else
            {
                $this->add_common_breadcrumbtrails();

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

    /**
     * add breadcrumbtrails
     */
    public function add_common_breadcrumbtrails()
    {
        $trail = parent::add_common_breadcrumbtrails();
        $trail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_SELECTED_FORUM_POST => $this->selected_forum_post_id)),
                Translation::get('EditPost', null, 'Chamilo\Core\Repository\ContentObject\ForumTopic')
            )
        );
    }

    /**
     * @param $success
     */
    private function my_redirect($success)
    {
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectUpdated' : 'ObjectNotUpdated'), array('OBJECT' => Translation::get('ForumPost')),
                Utilities::COMMON_LIBRARIES
            )
        );
        $params = [];
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_TOPIC;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $this->redirect($message, !$success, $params);
    }
}
