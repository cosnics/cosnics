<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\ForumPostRendition;
use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\PagerRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Forum\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class TopicViewerComponent extends Manager implements DelegateComponent
{
    const DEFAULT_PER_PAGE = 5;

    /**
     *
     * @var integer
     */
    private $pageNumber;

    /**
     *
     * @var \Chamilo\Libraries\Format\Table\Pager
     */
    private $pager;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     *
     * @var boolean
     */
    private $isLocked;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic
     */
    private $forumTopic;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost[]
     */
    private $forumTopicPosts;

    public function run()
    {
        $this->setBreadcrumbs();
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = '<a name="top"></a>';
        
        if (! $this->isLocked())
        {
            $html[] = $this->getButtonToolbarRenderer()->render();
        }
        
        $html[] = $this->renderPosts();
        $html[] = $this->renderPager();
        $html[] = $this->render_footer();
        
        $this->forum_topic_viewed($this->get_complex_content_object_item_id());
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic
     */
    public function getForumTopic()
    {
        if (! isset($this->forumTopic))
        {
            $complexForumTopic = $this->get_complex_content_object_item();
            $this->forumTopic = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ForumTopic::class_name(), 
                $complexForumTopic->get_ref());
        }
        
        return $this->forumTopic;
    }

    /**
     *
     * @return boolean
     */
    public function isLocked()
    {
        if (! isset($this->isLocked))
        {
            $this->isLocked = $this->getForumTopic()->is_locked();
        }
        
        return $this->isLocked;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost[]
     */
    public function getForumTopicPosts()
    {
        if (! isset($this->forumTopicPosts))
        {
            $children = DataManager::retrieve_forum_posts($this->getForumTopic()->getId(), $this->get_condition());
            
            $this->forumTopicPosts = array();
            
            while ($child = $children->next_result())
            {
                $this->forumTopicPosts[] = DataManager::retrieve_by_id(ForumPost::class_name(), $child->get_id());
            }
        }
        
        return $this->forumTopicPosts;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost[]
     */
    public function getVisibleForumTopicPosts()
    {
        return array_slice(
            $this->getForumTopicPosts(), 
            $this->getPager()->getCurrentRangeOffset(), 
            $this->getItemsPerPage());
    }

    public function setBreadcrumbs()
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_FORUM, 
                        self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)), 
                $this->get_root_content_object()->get_title()));
        
        $complex_content_objects_path = $this->retrieve_children_from_root_to_cloi(
            $this->get_root_content_object()->get_id(), 
            $this->get_complex_content_object_item()->get_id());
        
        if ($complex_content_objects_path)
        {
            foreach ($complex_content_objects_path as $key => $value)
            {
                if ($value instanceof ForumTopic)
                {
                    $trail->add(
                        new Breadcrumb(
                            $this->get_url(
                                array(
                                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key, 
                                    self::PARAM_ACTION => self::ACTION_VIEW_TOPIC)), 
                            $value->get_title()));
                }
                else
                {
                    $trail->add(
                        new Breadcrumb(
                            $this->get_url(
                                array(
                                    self::PARAM_ACTION => self::ACTION_VIEW_FORUM, 
                                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key)), 
                            $value->get_title()));
                }
            }
        }
        else
        {
            throw new Exception('The forum topic you requested has not been found in this forum');
        }
    }

    public function renderPosts()
    {
        $html = array();

        $forumPosts = $this->getVisibleForumTopicPosts();

        if(count($forumPosts) == 0)
        {
            $html[] = '<div class="alert alert-info">';
            $html[] = Translation::getInstance()->getTranslation('NoForumPostsFound', null, Manager::context());
            $html[] = '</div>';
        }
        else
        {
            $html[] = '<ul class="media-list forum">';

            foreach ($forumPosts as $forumTopicPost)
            {
                $html[] = '<li class="media well">';
                $html[] = $this->renderPostUser($forumTopicPost);
                $html[] = $this->renderPostBody($forumTopicPost);
                $html[] = '</li>';
            }

            $html[] = '</ul>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param ForumPost $forumPost
     * @return string
     */
    public function renderPostBody(ForumPost $forumPost)
    {
        $rendition = new ForumPostRendition($this, $forumPost);
        $html = array();
        
        $html[] = '<div class="media-body">';
        $html[] = $this->renderPostActions($forumPost);
        $html[] = $this->renderPostTitle($forumPost);
        $html[] = $this->renderPostDates($forumPost);
        $html[] = $rendition->render();
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    public function renderPostTitle(ForumPost $forumPost)
    {
        $html = array();
        
        $html[] = '<h4 class="media-body-title">';
        $html[] = $forumPost->get_title();
        $html[] = '</h4>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param ForumPost $forumTopicPost
     * @return string
     */
    public function renderPostDates(ForumPost $forumTopicPost)
    {
        $html = array();
        
        $html[] = '<div class="forum-post-panel">';
        $html[] = '<small>';
        
        $html[] = $this->renderPostDate('clock-o', 'text-muted', $forumTopicPost->get_creation_date());
        
        if ($forumTopicPost->get_modification_date() != $forumTopicPost->get_creation_date())
        {
            $html[] = '&nbsp;';
            $html[] = $this->renderPostDate('pencil', 'text-danger', $forumTopicPost->get_modification_date());
        }
        
        $html[] = '</small>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    public function renderPostDate($glyphType, $textClass, $date)
    {
        $dateFormat = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
        $fontAwesomeGlyph = new FontAwesomeGlyph($glyphType);
        
        $html = array();
        
        $html[] = '<span class="' . $textClass . '">';
        $html[] = $fontAwesomeGlyph->render();
        $html[] = DatetimeUtilities::format_locale_date($dateFormat, $date);
        $html[] = '</span>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param ForumPost $forumTopicPost
     * @return string
     */
    public function renderPostUser(ForumPost $forumTopicPost)
    {
        $user = $forumTopicPost->get_user();
        
        if ($user instanceof User)
        {
            $profilePhotoUrl = new Redirect(
                array(
                    Application::PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager::context(), 
                    Application::PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager::ACTION_USER_PICTURE, 
                    \Chamilo\Core\User\Manager::PARAM_USER_USER_ID => $user->get_id()));
            $profilePhotoSource = $profilePhotoUrl->getUrl();
            $userName = $user->get_fullname();
        }
        else
        {
            $profilePhotoSource = Theme::getInstance()->getImagePath(self::package(), 'Unknown', 'png');
            $userName = Translation::get('UserNotFound');
        }
        
        $html[] = '<div class="pull-left user-info" href="#">';
        $html[] = '<img class="avatar img-thumbnail" src="' . $profilePhotoSource . '" width="64"
                    alt="' . $userName . '">';
        $html[] = '<strong>';
        $html[] = '<small>';
        $html[] = '<span class="text-primary">' . $userName . '</span>';
        $html[] = '</small>';
        $html[] = '</strong>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    public function renderPostActions(ForumPost $forumPost)
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonToolBar->setClasses(array('pull-right'));
        
        if (! $this->isLocked())
        {
            $parameters = array();
            $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
            $parameters[self::PARAM_SELECTED_FORUM_POST] = $forumPost->get_id();
            $parameters[self::PARAM_ACTION] = self::ACTION_QUOTE_FORUM_POST;
            
            $buttonToolBar->addItem(
                new Button(
                    Translation::get('Quote'), 
                    new FontAwesomeGlyph('quote-right'), 
                    $this->get_url($parameters), 
                    Button::DISPLAY_ICON, 
                    false, 
                    'btn-link'));
            
            $parameters = array();
            $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
            $parameters[self::PARAM_SELECTED_FORUM_POST] = $forumPost->get_id();
            $parameters[self::PARAM_ACTION] = self::ACTION_CREATE_FORUM_POST;
            
            $buttonToolBar->addItem(
                new Button(
                    Translation::get('Reply'), 
                    new FontAwesomeGlyph('comment'), 
                    $this->get_url($parameters), 
                    Button::DISPLAY_ICON, 
                    false, 
                    'btn-link'));
            
            if (($forumPost->get_user_id() == $this->get_user_id() || $this->get_user()->is_platform_admin() == true) ||
                 $this->is_forum_manager($this->get_user()))
            {
                $parameters = array();
                $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $parameters[self::PARAM_SELECTED_FORUM_POST] = $forumPost->get_id();
                $parameters[self::PARAM_ACTION] = self::ACTION_EDIT_FORUM_POST;
                
                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('Edit', null, Utilities::COMMON_LIBRARIES), 
                        new FontAwesomeGlyph('pencil'), 
                        $this->get_url($parameters), 
                        Button::DISPLAY_ICON, 
                        false, 
                        'btn-link'));
            }
            
            if (! $this->getForumTopic()->is_first_post($forumPost))
            {
                if (($forumPost->get_user_id() == $this->get_user_id() || $this->get_user()->is_platform_admin() == true) ||
                     $this->is_forum_manager($this->get_user()))
                {
                    $parameters = array();
                    $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                    $parameters[self::PARAM_SELECTED_FORUM_POST] = $forumPost->get_id();
                    $parameters[self::PARAM_ACTION] = self::ACTION_DELETE_FORUM_POST;
                    
                    $buttonToolBar->addItem(
                        new Button(
                            Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), 
                            new BootstrapGlyph('remove'), 
                            $this->get_url($parameters), 
                            Button::DISPLAY_ICON, 
                            true, 
                            'btn-link'));
                }
            }
        }
        
        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        
        return $buttonToolBarRenderer->render();
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url());
            
            $parameters = array();
            $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
            $parameters[self::PARAM_ACTION] = self::ACTION_CREATE_FORUM_POST;
            
            $buttonToolbar->addItem(
                new Button(
                    Translation::get('ReplyOnTopic', null, 'Chamilo\Core\Repository\ContentObject\ForumTopic'), 
                    new BootstrapGlyph('plus'), 
                    $this->get_url($parameters), 
                    Button::DISPLAY_ICON_AND_LABEL, 
                    false, 
                    'btn-primary'));
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the condition of the search action bar
     */
    public function get_condition()
    {
        if ($this->buttonToolbarRenderer)
        {
            $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
            if (isset($query) && $query != '')
            {
                $conditions = array();
                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(ForumPost::class_name(), ForumPost::PROPERTY_TITLE), 
                    '*' . $query . '*', 
                    ForumPost::get_table_name(), 
                    false);
                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(ForumPost::class_name(), ForumPost::PROPERTY_CONTENT), 
                    '*' . $query . '*', 
                    ForumPost::get_table_name(), 
                    false);
                
                return new OrCondition($conditions);
            }
        }
        
        return null;
    }

    public function getTotalNumberOfItems()
    {
        return count($this->getForumTopicPosts());
    }

    /**
     *
     * @return integer
     */
    public function getPageNumber()
    {
        if (! isset($this->pageNumber))
        {
            $requestedLastPost = Request::get('last_post');
            
            if ($requestedLastPost)
            {
                $pageNumber = (int) ceil($this->getTotalNumberOfItems() / self::DEFAULT_PER_PAGE);
            }
            else
            {
                $pageNumber = 1;
            }
            
            $requestedPageNumber = Request::get(ForumTopic::get_table_name() . '_' . 'page_nr');
            
            $this->pageNumber = $requestedPageNumber ? $requestedPageNumber : $pageNumber;
        }
        
        return $this->pageNumber;
    }

    /**
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        return self::DEFAULT_PER_PAGE;
    }

    /**
     * Get the Pager object to split the showed data in several pages
     */
    public function getPager()
    {
        if (is_null($this->pager))
        {
            $this->pager = new Pager($this->getItemsPerPage(), 1, $this->getTotalNumberOfItems(), $this->getPageNumber());
        }
        
        return $this->pager;
    }

    /**
     *
     * @return string
     */
    public function renderPager()
    {
        try
        {
            $pagerRenderer = new PagerRenderer($this->getPager());
            return $pagerRenderer->renderPaginationWithPageLimit(
                $this->get_parameters(),
                ForumTopic::get_table_name() . '_' . 'page_nr');
        }
        catch(\Exception $ex)
        {

        }
    }

    /**
     * Get table data to show on current page
     * 
     * @see SortableTable#get_table_data
     */
    public function get_table_data($from = 1)
    {
        return array_slice($this->posts, $from, self::DEFAULT_PER_PAGE);
    }
}
