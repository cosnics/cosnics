<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;
use HTML_Table;
use Pager;

/**
 * $Id: topic_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_display.forum.component
 */
class TopicPreviewerComponent extends Manager implements DelegateComponent
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
     * @var boolean
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
        
        $this->retrieve_children($this->topic->get_id());
        
        $this->prepare_pager();
        $pager = $this->get_pager();
        
        // Set the starting position for the data retrievement
        $offset = $pager->getOffsetByPageId();
        $from = $offset[0] - 1;
        
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 2));
        
        Page :: getInstance()->setViewMode(Page :: VIEW_MODE_HEADERLESS);
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = '<a name="top"></a>';
        
        $row = 0;
        $this->get_posts_table($this->get_table_data($from), $table, $row);
        
        $html[] = '<div>' . $table->toHtml() . '</div>';
        $html[] = $this->get_navigation_html();
        $html[] = '<br />';
        $html[] = $this->render_footer();
        
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
                     Theme :: getInstance()->getCommonImagePath('Unknown') . '' . '" /><br /><br />' .
                     DatetimeUtilities :: format_locale_date(null, $post->get_creation_date());
            }
            
            $message = $this->format_message($html);
            
            $attachments = $post->get_attached_content_objects($post->get_id());
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
                    $message .= '<li><a href="#" onClick="' . $url . '">' . '<img src="' .
                         $attachment->get_icon_path(Theme :: ICON_MINI) . '" alt="' . htmlentities(
                            Translation :: get(ContentObject :: type_to_class($attachment->get_type()) . 'TypeName')) .
                         '"/> ' . $attachment->get_title() . '</a></li>';
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
        $message = str_replace('[/quote]', '</div>', $message);
        return $message;
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
        return array_slice(array_reverse($this->posts), $from, self :: DEFAULT_PER_PAGE);
    }
}
