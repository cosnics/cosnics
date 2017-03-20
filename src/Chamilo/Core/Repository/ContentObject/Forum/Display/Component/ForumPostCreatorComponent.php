<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Component\ForumPostFormAction\ForumPostFormActionCreate;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Form\ForumPostForm;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumPostCreatorComponent extends ForumPostFormActionCreate
{

    protected $forumpost;

    protected $form;

    /**
     * Makes a new creator form and if its validate makes a new post
     */
    public function run()
    {
        $this->forumpost = new ForumPost();
        $first_post = DataManager::retrieve_first_post($this->get_complex_content_object_item()->get_ref());
        $post_id = Request::get(self::PARAM_SELECTED_FORUM_POST);
        
        if ($post_id != null)
        {
            $selected_post = DataManager::retrieve_forum_post_of_topic(
                $this->get_complex_content_object_item()->get_ref(), 
                $post_id);

            if(!$selected_post instanceof ForumPost)
            {
                throw new ObjectNotExistException(Translation::getInstance()->getTranslation('ForumPost'), $post_id);
            }

            $this->forumpost->set_title($selected_post->get_title());
        }
        else
        {
            $this->forumpost->set_title($first_post->get_title());
        }
        
        $this->form = new ForumPostForm(
            ForumPostForm::TYPE_CREATE, 
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_CREATE_FORUM_POST, 
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id())), 
            $this->forumpost, 
            $post_id);
        
        if ($this->form->validate())
        {
            $success = parent::create_forum_post(ForumPostForm::TYPE_CREATE);
            
            $this->my_redirect($success);
        }
        else
        {
            $this->add_common_breadcrumbtrails();
            
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $this->form->toHtml();
            $html[] = ('<iframe style="width:100%; height:350px; border:1px solid #EBEBEB;" src="' . $this->get_url(
                array(self::PARAM_ACTION => self::ACTION_PREVIEW_TOPIC)) . '"></iframe>');
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    /**
     * redirect
     * 
     * @param $success type
     */
    private function my_redirect($success)
    {
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectCreated' : 'ObjectNotCreated'), 
                array('OBJECT' => Translation::get('ForumPost')), 
                Utilities::COMMON_LIBRARIES));
        
        $params = array();
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_TOPIC;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $this->redirect($message, ($success ? false : true), $params);
    }

    /*
     * Adding breadcrumbtrail
     */
    public function add_common_breadcrumbtrails()
    {
        $trail = parent::add_common_breadcrumbtrails();
        $trail->add(
            new Breadcrumb(
                $this->get_url(), 
                Translation::get('ReplyOnTopic', null, 'Chamilo\Core\Repository\ContentObject\ForumTopic')));
    }
}
