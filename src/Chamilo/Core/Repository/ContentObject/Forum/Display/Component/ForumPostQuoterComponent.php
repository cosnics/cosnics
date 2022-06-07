<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Component\ForumPostFormAction\ForumPostFormActionCreate;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Form\ForumPostForm;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumPostQuoterComponent extends ForumPostFormActionCreate
{

    protected $forumpost;

    protected $form;

    protected $selected_forum_post_id;

    /**
     * makes a new quoter form and if its validate makes a new quoter post
     */
    public function run()
    {
        $this->selected_forum_post_id = Request::get(self::PARAM_SELECTED_FORUM_POST);
        
        $quote_lo = DataManager::retrieve_by_id(ForumPost::class, $this->selected_forum_post_id);
        
        $this->forumpost = new ForumPost();
        
        if (substr($quote_lo->get_title(), 0, 3) == 'RE:')
        {
            $reply = $quote_lo->get_title();
        }
        else
        {
            $reply = 'RE: ' . $quote_lo->get_title();
        }
        
        $this->forumpost->set_title($reply);
        
        $quoteUser = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class,
            (int) $quote_lo->get_user_id());
        
        $quoteContent = [];
        
        $quoteContent[] = '<blockquote>';
        $quoteContent[] = $quote_lo->get_content();
        $quoteContent[] = '<footer>' . $quoteUser->get_fullname() . '</footer>';
        $quoteContent[] = '</blockquote>';
        $quoteContent[] = '<p></p>';
        
        $this->forumpost->set_content(implode(PHP_EOL, $quoteContent));
        
        $this->form = new ForumPostForm(
            ForumPostForm::TYPE_QUOTE, 
            $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_QUOTE_FORUM_POST, 
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(), 
                    self::PARAM_SELECTED_FORUM_POST => $this->selected_forum_post_id)), 
            $this->forumpost, 
            $this->selected_forum_post_id);
        
        if ($this->form->validate())
        {
            $success = parent::create_forum_post(ForumPostForm::TYPE_QUOTE);
            
            $this->my_redirect($success);
        }
        else
        {
            $this->add_common_breadcrumbtrails();
            
            $html = [];
            
            $html[] = $this->render_header();
            $html[] = $this->form->toHtml();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    /**
     * redirect
     * 
     * @param type $success
     */
    private function my_redirect($success)
    {
        $message = htmlentities(
            Translation::get(
                ($success ? 'ObjectCreated' : 'ObjectNotCreated'), 
                array('OBJECT' => Translation::get('ForumPost')), 
                StringUtilities::LIBRARIES));
        $params = [];
        $params[self::PARAM_ACTION] = self::ACTION_VIEW_TOPIC;
        $params[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $this->redirect($message, !$success, $params);
    }

    /**
     * add the breaddcrumps
     */
    public function add_common_breadcrumbtrails()
    {
        $trail = parent::add_common_breadcrumbtrails();
        $trail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_SELECTED_FORUM_POST => $this->selected_forum_post_id)), 
                Translation::get('ReplyOnPost', null, 'Chamilo\Core\Repository\ContentObject\ForumTopic')));
    }
}
