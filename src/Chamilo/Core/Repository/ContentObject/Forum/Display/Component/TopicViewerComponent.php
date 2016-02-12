<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;
use HTML_Table;
use Pager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;

/**
 * $Id: topic_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.forum.component
 */
class TopicViewerComponent extends Manager implements DelegateComponent
{

    /**
     * Posts of a topic.
     * 
     * @var Array of posts.
     */
    private $posts;

    /**
     * Represents the topic object.
     * 
     * @var ForumTopic
     */
    private $topic;

    /**
     * Checks whether a topic is locked.
     * 
     * @var boolaen
     */
    private $is_locked;

    /**
     * The number of the page that will be displayed
     */
    private $page_nr;

    /**
     * Number of items to display per page
     */
    private $per_page;

    /**
     * The pager object to split the data in several pages
     */
    private $pager;

    /**
     * The total number of items in the list
     */
    private $total_number_of_items;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;
    /**
     * The default number of objects per page.
     */
    const DEFAULT_PER_PAGE = 5;

    public function run()
    {
        $topic = $this->get_complex_content_object_item();
        $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ForumTopic :: class_name(), 
            $topic->get_ref());
        $this->topic = $content_object;
        $this->is_locked = $content_object->is_locked();
        
        if (! $this->is_locked)
        {
            $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        }
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_FORUM, 
                        self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)), 
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
                                    self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key, 
                                    self :: PARAM_ACTION => self :: ACTION_VIEW_TOPIC)), 
                            $value->get_title()));
                }
                else
                {
                    $trail->add(
                        new Breadcrumb(
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_VIEW_FORUM, 
                                    self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key)), 
                            $value->get_title()));
                }
            }
        }
        else
        {
            throw new Exception('The forum topic you requested has not been found in this forum');
        }
        
        $this->retrieve_children($this->topic->get_id(), $this->get_condition());
        
        $this->prepare_pager();
        $pager = $this->get_pager();
        
        // Set the starting position for the data retrievement
        $offset = $pager->getOffsetByPageId();
        $from = $offset[0] - 1;
        
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 2));
        
        $html = array();
        
        $html[] = $this->render_header();
        
        $html[] = '<a name="top"></a>';
        
        if ($this->buttonToolbarRenderer)
        {
            $html[] = $this->buttonToolbarRenderer->render();
        }
        
        $html[] = '<div class="clear"></div><br />';
        $row = 0;
        $this->get_posts_table($this->get_table_data($from), $table, $row);
        $html[] = '<div>' . $table->toHtml() . '</div>';
        $nav = $this->get_navigation_html();
        $html[] = $nav;
        $html[] = '<br />';
        
        $html[] = $this->render_footer();
        
        $this->forum_topic_viewed($this->get_complex_content_object_item_id());
        
        return implode(PHP_EOL, $html);
    }

    public function retrieve_children($topic, $patterncondition)
    {
        $children = DataManager :: retrieve_forum_posts($topic, $patterncondition);
        while ($child = $children->next_result())
        {
            $this->posts[] = DataManager :: retrieve_by_id(ForumPost :: class_name(), $child->get_id());
        }
    }

    public function get_posts_table($posts, $table, &$row)
    {
        $this->create_posts_table_header($table);
        $row = 2;
        $this->create_posts_table_content($posts, $table, $row);
        $this->create_posts_table_footer($table, $row);
        
        return $table;
    }

    public function create_posts_table_header($table)
    {
        $table->setCellContents(0, 0, '');
        $table->setCellAttributes(0, 0, array('colspan' => 2, 'class' => 'category'));
        $table->setHeaderContents(1, 0, Translation :: get('Author'));
        $table->setCellAttributes(1, 0, array('width' => 130));
        $table->setHeaderContents(1, 1, Translation :: get('Message'));
    }

    public function create_posts_table_footer($table, $row)
    {
        $table->setCellContents($row, 0, '');
        $table->setCellAttributes($row, 0, array('colspan' => 2, 'class' => 'category'));
    }

    public function create_posts_table_content($posts, $table, &$row)
    {
        $post_counter = 0;
        
        foreach ($posts as $post)
        {
            
            $display = new ContentObjectResourceRenderer($this, $post->get_content());
            $html = $display->run();
            $class = ($post_counter % 2 == 0 ? 'row1' : 'row2');
            $name = "";
            
            $user = $post->get_user();
            if ($user)
            {
                $name = $user->get_fullname();
            }
            else
            {
                $name = Translation :: get('UserNotFound');
            }
            
            $table->setCellContents($row, 0, '<a name="post_' . $post->get_id() . '"></a><b>' . $name . '</b>');
            
            $table->setCellAttributes(
                $row, 
                0, 
                array('class' => $class, 'width' => 150, 'valign' => 'middle', 'align' => 'center'));
            $table->setCellContents($row, 1, '<b>' . Translation :: get('Subject') . ':</b> ' . $post->get_title());
            $table->setCellAttributes(
                $row, 
                1, 
                array('class' => $class, 'height' => 25, 'style' => 'padding-left: 10px;'));
            
            $row ++;
            
            if ($user)
            {
                
                $profilePhotoUrl = new Redirect(
                    array(
                        Application :: PARAM_CONTEXT => \Chamilo\Core\User\Ajax\Manager :: context(), 
                        Application :: PARAM_ACTION => \Chamilo\Core\User\Ajax\Manager :: ACTION_USER_PICTURE, 
                        \Chamilo\Core\User\Manager :: PARAM_USER_USER_ID => $user->get_id()));
                $info = '<br /><img style="max-width: 100px;" src="' . $profilePhotoUrl->getUrl() . '" /><br /><br />' .
                     DatetimeUtilities :: format_locale_date(null, $post->get_creation_date());
            }
            else
            {
                $info = '<br /><img style="max-width: 100px;" src="' .
                     Theme :: getInstance()->getCommonImagePath('Unknown') . '" /><br /><br />' .
                     DatetimeUtilities :: format_locale_date(null, $post->get_creation_date());
            }
            
            $message = $this->format_message($html);
            
            $attachments = $post->get_attached_content_objects();
            if (count($attachments) > 0)
            {
                $message .= '<div class="quotetitle">' . Translation :: get('Attachments') .
                     ':</div><div class="quotecontent"><ul>';
                
                foreach ($attachments as $attachment)
                {
                    $params = array();
                    $params[self :: PARAM_ACTION] = self :: ACTION_VIEW_ATTACHMENT;
                    $params[self :: PARAM_FORUM_TOPIC_ID] = $post->get_forum_topic_id();
                    $params[self :: PARAM_SELECTED_FORUM_POST] = $post->get_id();
                    $params[self :: PARAM_ATTACHMENT_ID] = $attachment->get_id();
                    $url = $this->get_url($params);
                    
                    $url = 'javascript:openPopup(\'' . $url . '\'); return false;';
                    $message .= '<li><a href="#" onClick="' . $url . '">' . $attachment->get_icon_image(
                        Theme :: ICON_MINI) . ' ' . $attachment->get_title() . '</a></li>';
                }
                
                $message .= '</ul></div>';
            }
            
            $table->setCellContents($row, 0, $info);
            $table->setCellAttributes(
                $row, 
                0, 
                array('class' => $class, 'align' => 'center', 'valign' => 'top', 'height' => 150));
            $table->setCellContents($row, 1, $message);
            $table->setCellAttributes(
                $row, 
                1, 
                array('class' => $class, 'valign' => 'top', 'style' => 'padding: 10px; padding-top: 10px;'));
            
            $row ++;
            
            $bottom_bar = array();
            $bottom_bar[] = '<div style="float: left; padding-top: 4px;">';
            
            $object = $post;
            if ($object->get_creation_date() != $object->get_modification_date())
            {
                $bottom_bar[] = Translation :: get(
                    'LastChangedAt', 
                    array(
                        'TIME' => DatetimeUtilities :: format_locale_date(
                            Translation :: get('DateFormatShort', null, Utilities :: COMMON_LIBRARIES) . ', ' .
                                 Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES), 
                                $object->get_modification_date())));
            }
            
            $bottom_bar[] = '</div>';
            $bottom_bar[] = '<div style="float: right;">';
            $bottom_bar[] = $this->get_post_actions($post, $post_counter);
            $bottom_bar[] = '</div>';
            
            $table->setCellContents($row, 0, '<a href="#top"><small>' . Translation :: get('Top') . '</small></a>');
            $table->setCellAttributes($row, 0, array('class' => $class, 'style' => 'padding: 5px;'));
            $table->setCellContents($row, 1, implode(PHP_EOL, $bottom_bar));
            $table->setCellAttributes($row, 1, array('class' => $class, 'align' => 'right', 'style' => 'padding: 5px;'));
            
            $row ++;
            
            $table->setCellContents($row, 0, ' ');
            $table->setCellAttributes($row, 0, array('colspan' => '2', 'class' => 'spacer'));
            
            $row ++;
            
            $post_counter ++;
        }
    }

    private function format_message($message)
    {
        $message = preg_replace(
            '/\[quote=("|&quot;)(.*)("|&quot;)\]/', 
            "<div class=\"quotetitle\">$2 " . Translation :: get('Wrote') . ":</div><div class=\"quotecontent\">", 
            $message);
        return str_replace('[/quote]', '</div>', $message);
    }

    public function get_post_actions($forum_post, $post_counter)
    {
        $post = $forum_post->get_id();
        
        $toolbar = new Toolbar();
        
        $parameters = array();
        $parameters[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $parameters[self :: PARAM_SELECTED_FORUM_POST] = $forum_post->get_id();
        
        if (! $this->is_locked)
        {
            $parameters[self :: PARAM_ACTION] = self :: ACTION_QUOTE_FORUM_POST;
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Quote'), 
                    Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Forum\Display', 
                        'Buttons/IconPostQuote', 
                        'gif'), 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
            
            $parameters[self :: PARAM_ACTION] = self :: ACTION_CREATE_FORUM_POST;
            
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Reply'), 
                    Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Forum\Display', 
                        'Buttons/PmReply', 
                        'gif'), 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON));
            
            if (($forum_post->get_user_id() == $this->get_user_id() || $this->get_user()->is_platform_admin() == true) ||
                 $this->is_forum_manager($this->get_user()))
            {
                $parameters[self :: PARAM_ACTION] = self :: ACTION_EDIT_FORUM_POST;
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES), 
                        Theme :: getInstance()->getImagePath(
                            'Chamilo\Core\Repository\ContentObject\Forum\Display', 
                            'Buttons/IconPostEdit', 
                            'gif'), 
                        $this->get_url($parameters), 
                        ToolbarItem :: DISPLAY_ICON));
            }
            
            if (! $this->topic->is_first_post($forum_post))
            {
                if (($forum_post->get_user_id() == $this->get_user_id() || $this->get_user()->is_platform_admin() == true) ||
                     $this->is_forum_manager($this->get_user()))
                {
                    $parameters[self :: PARAM_ACTION] = self :: ACTION_DELETE_FORUM_POST;
                    
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES), 
                            Theme :: getInstance()->getImagePath(
                                'Chamilo\Core\Repository\ContentObject\Forum\Display', 
                                'Buttons/IconPostDelete', 
                                'gif'), 
                            $this->get_url($parameters), 
                            ToolbarItem :: DISPLAY_ICON, 
                            true));
                }
            }
        }
        
        return $toolbar->as_html();
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar($this->get_url($par));
            $commonActions = new ButtonGroup();
            
            $parameters = array();
            $parameters[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
            $parameters[self :: PARAM_ACTION] = self :: ACTION_CREATE_FORUM_POST;
            $commonActions->addButton(
                new Button(
                    Translation :: get('ReplyOnTopic', null, 'Chamilo\Core\Repository\ContentObject\ForumTopic'), 
                    Theme :: getInstance()->getCommonImagePath('Action/Reply'), 
                    $this->get_url($parameters), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $par = array();
            $par[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
            $par[self :: PARAM_ACTION] = self :: ACTION_VIEW_TOPIC;
            
            $commonActions->addButton(
                new Button(
                    Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), 
                    Theme :: getInstance()->getCommonImagePath('Action/Browser'), 
                    $this->get_url($par), 
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            
            $buttonToolbar->addButtonGroup($commonActions);
            
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }
        
        return $this->buttonToolbarRenderer;
    }

    /**
     * Returns the condition of the search action bar
     */
    public function get_condition()
    {
        // search condition
        $condition = $this->get_search_condition();
        
        // append with extra conditions
        // no extras
        
        return $condition;
    }

    /**
     * Constructs the conditions of the search field.
     * 
     * @return Condition
     */
    public function get_search_condition()
    {
        if ($this->buttonToolbarRenderer)
        {
            $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();
            if (isset($query) && $query != '')
            {
                $conditions = array();
                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(ForumPost :: class_name(), ForumPost :: PROPERTY_TITLE), 
                    '*' . $query . '*', 
                    ForumPost :: get_table_name(), 
                    false);
                $conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable(ForumPost :: class_name(), ForumPost :: PROPERTY_CONTENT), 
                    '*' . $query . '*', 
                    ForumPost :: get_table_name(), 
                    false);
                
                return new OrCondition($conditions);
            }
        }
        
        return null;
    }

    /**
     * Prepares the pager (counts objects, sets page, etc)
     */
    public function prepare_pager()
    {
        // set the prefix
        $this->param_prefix = ForumTopic :: get_table_name() . '_';
        
        // count the total number of objects
        $this->total_number_of_items = count($this->posts);
        
        $last_post = Request :: get('last_post');
        
        if ($last_post)
        {
            
            $this->page_nr = $this->total_number_of_items / self :: DEFAULT_PER_PAGE;
            if ($this->total_number_of_items % self :: DEFAULT_PER_PAGE)
            {
                $this->page_nr = $this->page_nr + 1;
            }
        }
        else
        {
            $this->page_nr = 1;
        }
        
        $this->page_nr = Request :: get($this->param_prefix . 'page_nr') ? Request :: get(
            $this->param_prefix . 'page_nr') : $this->page_nr;
        $_SESSION[$this->param_prefix . 'page_nr'] = $this->page_nr;
        
        // set the number of objects per page
        $this->per_page = self :: DEFAULT_PER_PAGE;
    }

    /**
     * Get the Pager object to split the showed data in several pages
     */
    public function get_pager()
    {
        if (is_null($this->pager))
        {
            $total_number_of_items = $this->total_number_of_items;
            $params['mode'] = 'Sliding';
            $params['perPage'] = $this->per_page;
            $params['totalItems'] = $total_number_of_items;
            $params['urlVar'] = $this->param_prefix . 'page_nr';
            $params['prevImg'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Prev') .
                 '"  style="vertical-align: middle;"/>';
            $params['nextImg'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Next') .
                 '"  style="vertical-align: middle;"/>';
            $params['firstPageText'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/First') .
                 '"  style="vertical-align: middle;"/>';
            $params['lastPageText'] = '<img src="' . Theme :: getInstance()->getCommonImagePath('Action/Last') .
                 '"  style="vertical-align: middle;"/>';
            $params['firstPagePre'] = '';
            $params['lastPagePre'] = '';
            $params['firstPagePost'] = '';
            $params['lastPagePost'] = '';
            $params['spacesBeforeSeparator'] = '';
            $params['spacesAfterSeparator'] = '';
            $params['currentPage'] = $this->page_nr;
            $params['excludeVars'] = array('message');
            
            $this->pager = Pager :: factory($params);
        }
        
        return $this->pager;
    }

    /**
     * Get the HTML-code with the navigational buttons to browse through the data-pages.
     */
    public function get_navigation_html()
    {
        $pager = $this->get_pager();
        $pager_links = $pager->getLinks();
        return $pager_links['first'] . ' ' . $pager_links['back'] . ' ' . $pager->getCurrentPageId() . ' / ' .
             $pager->numPages() . ' ' . $pager_links['next'] . ' ' . $pager_links['last'];
    }

    /**
     * Get table data to show on current page
     * 
     * @see SortableTable#get_table_data
     */
    public function get_table_data($from = 1)
    {
        return array_slice($this->posts, $from, self :: DEFAULT_PER_PAGE);
    }
}
