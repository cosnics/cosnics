<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ComplexForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;
use HTML_Table;

/**
 * $Id: forum_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.forum.component
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumViewerComponent extends Manager implements DelegateComponent
{

    private $subforums;

    private $topics;

    /**
     *
     * @var boolean
     */
    private $isLocked;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum
     */
    private $forum;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        // $topics_table = $this->get_topics_table_html();
        // $forum_table = $this->get_forums_table_html();

        $trail = BreadcrumbTrail :: get_instance();

        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_FORUM,
                        self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null)),
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

                    $trail->add(
                        new Breadcrumb(
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_VIEW_FORUM,
                                    self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key)),
                            $value->get_title()));
                }
            }
            else
            {
                throw new Exception('The forum you requested has not been found');
            }
        }

        $html = array();

        $html[] = $this->render_header();

        if (! $this->is_locked)
        {
            $html[] = $this->buttonToolbarRenderer->render();
        }

        $html[] = $this->renderForum();

        // $html[] = $topics_table->toHtml();

        // if (count($this->forums) > 0)
        // {
        // $html[] = '<br /><br />';
        // $html[] = $forum_table->toHtml();
        // }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
     */
    public function getForum()
    {
        if (! isset($this->forum))
        {
            if (! $this->get_complex_content_object_item())
            {
                $this->forum = $this->get_root_content_object();
            }
            else
            {
                $this->forum = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                    ContentObject :: class_name(),
                    $this->get_complex_content_object_item()->get_ref());
            }
        }

        return $this->forum;
    }

    /**
     *
     * @return boolean
     */
    public function isLocked()
    {
        if (! isset($this->isLocked))
        {
            $this->isLocked = $this->getForum()->is_locked();
        }

        return $this->isLocked;
    }

    public function getTopics()
    {
        if (! isset($this->topics))
        {
            $this->prepareTopicsAndSubforums();
        }

        return $this->topics;
    }

    public function getSubforums()
    {
        if (! isset($this->subforums))
        {
            $this->prepareTopicsAndSubforums();
        }

        return $this->subforums;
    }

    private function prepareTopicsAndSubforums()
    {
        $order_property = array();
        $order_property[] = new OrderBy(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(),
                ComplexContentObjectItem :: PROPERTY_ADD_DATE));

        $parameters = new DataClassRetrievesParameters(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_PARENT),
                new StaticConditionVariable($this->getForum()->get_id())),
            null,
            null,
            $order_property);

        $children = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            $parameters);

        $this->topics = array();
        $this->subforums = array();

        while ($child = $children->next_result())
        {
            $contentObject = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $child->get_ref());
            $child->set_ref($contentObject);

            if ($contentObject->get_type() == ForumTopic :: class_name())
            {
                $this->topics[] = $child;
            }
            else
            {
                $this->subforums[] = $child;
            }
        }

        $this->topics = $this->sortTopics($this->topics);
    }

    private function sortTopics($topics)
    {
        $sorted_array = array();

        foreach ($topics as $key => $value)
        {
            $type = ($value->get_forum_type()) ? $value->get_forum_type() : 100;
            $sorted_array[$type][] = $value;
        }

        ksort($sorted_array);

        $array = array();

        foreach ($sorted_array as $key => $value)
        {
            foreach ($value as $key2 => $value2)
            {
                $array[] = $value2;
            }
        }

        return $array;
    }

    /**
     *
     * @return string
     */
    public function renderForum()
    {
        $html = array();

        $html[] = $this->renderTopics();
        // $html[] = $this->renderTopicsForSubforums();

        return implode(PHP_EOL, $html);
    }

    public function renderTopics()
    {
        if (count($this->getTopics()) == 0)
        {
            return '<div class="alert alert-info text-center">' . Translation :: get('NoTopics') . '</div>';
        }

        $table = new HTML_Table(array('class' => 'table forum table-striped'));

        $header = $table->getHeader();

        $header->setHeaderContents(0, 0, '');
        $header->setCellAttributes(0, 0, array('class' => 'cell-stat'));
        $header->setHeaderContents(0, 1, '');
        $header->setHeaderContents(0, 2, Translation :: get("Author", null, Forum :: package()));
        $header->setCellAttributes(0, 2, array('class' => 'cell-stat-2x text-center'));
        $header->setHeaderContents(0, 3, Translation :: get("Replies", null, Forum :: package()));
        $header->setCellAttributes(0, 3, array('class' => 'cell-stat-2x text-center'));
        $header->setHeaderContents(0, 4, Translation :: get("Views", null, Forum :: package()));
        $header->setCellAttributes(0, 4, array('class' => 'cell-stat text-center hidden-xs hidden-sm'));
        $header->setHeaderContents(0, 5, Translation :: get("LastPostForum", null, Forum :: package()));
        $header->setCellAttributes(0, 5, array('class' => 'cell-stat-2x hidden-xs hidden-sm'));
        $header->setHeaderContents(0, 6, '');
        $header->setCellAttributes(0, 6, array('class' => 'cell-stat-2x'));

        $row = 0;

        foreach ($this->getTopics() as $topic)
        {
            $title = '<a href="' . $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_VIEW_TOPIC,
                    self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $topic->get_id())) . '">' .
                 $topic->get_ref()->get_title() . '</a>';

            $count = $topic->get_ref()->get_total_posts();

            $table->setCellContents($row, 0, $this->renderGlyph($topic));
            $table->setCellAttributes($row, 0, array('class' => 'text-center'));
            $table->setCellContents($row, 1, $title);

            $table->setCellContents($row, 2, $this->renderAuthor($topic));
            $table->setCellAttributes($row, 2, array('class' => 'text-primary text-center hidden-xs hidden-sm'));
            $table->setCellContents($row, 3, ($count > 0) ? $count - 1 : $count);
            $table->setCellAttributes($row, 3, array('class' => 'text-primary text-center hidden-xs hidden-sm'));
            $table->setCellContents($row, 4, $this->forum_count_topic_views($topic->get_id()));
            $table->setCellAttributes($row, 4, array('class' => 'text-primary text-center hidden-xs hidden-sm'));
            $table->setCellContents($row, 5, $this->renderLastPost($topic));
            $table->setCellAttributes($row, 5, array('class' => 'hidden-xs hidden-sm'));
            $table->setCellContents($row, 6, $this->renderTopicActions($topic));
            $table->setCellAttributes($row, 6, array('class' => 'text-center'));

            $row ++;
        }

        return $table->toHtml();
    }

    /**
     *
     * @param ComplexForumTopic $topic
     * @return \Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph
     */
    public function renderGlyph(ComplexForumTopic $topic)
    {
        $forumGlyph = new BootstrapGlyph('file', array('text-muted'), Translation :: get('NoNewPosts'));

        switch ($topic->get_forum_type())
        {
            case 1 :
                $forumGlyph = new BootstrapGlyph('star', array(), Translation :: get('Sticky'));
                break;
            case 2 :
                $forumGlyph = new BootstrapGlyph('exclamation-sign', array(), Translation :: get('Important'));
                break;
        }

        if ($this->isLocked() || $topic->get_ref()->get_locked())
        {
            $forumGlyph = new BootstrapGlyph('lock', array(), Translation :: get('Locked'));
        }

        return $forumGlyph->render();
    }

    /**
     *
     * @param ComplexForumTopic $topic
     * @return string
     */
    public function renderAuthor(ComplexForumTopic $topic)
    {
        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            User :: class_name(),
            (int) $topic->get_user_id());
        $name = "";

        if (! $user)
        {
            return Translation :: get('UserNotFound');
        }
        else
        {
            return $user->get_fullname();
        }
    }

    /**
     *
     * @param ComplexForumTopic $topic
     * @return string
     */
    public function renderLastPost(ComplexForumTopic $topic)
    {
        $last_post = DataManager :: retrieve_by_id(ForumPost :: class_name(), $topic->get_ref()->get_last_post());

        if ($last_post)
        {
            if ($topic->get_ref()->is_locked() && (! $this->get_user()->is_platform_admin() ||
                 ! ($this->get_user_id() == $topic->get_ref()->get_owner_id())))
            {
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    User :: class_name(),
                    (int) $last_post->get_user_id());
                $name = "";
                if (! $user)
                {
                    $name = Translation :: get('UserNotFound');
                }
                else
                {
                    $name = $user->get_fullname();
                }

                return DatetimeUtilities :: format_locale_date(null, $last_post->get_creation_date()) .
                     '<br /><span class="text-primary">' . $name . '</span>';
            }
            else
            {

                $link = $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_TOPIC,
                        'pid' => $this->pid,
                        self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $topic->get_id(),
                        self :: PARAM_LAST_POST => $last_post->get_id()));
                $name = "";

                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    User :: class_name(),
                    (int) $last_post->get_user_id());

                if (! $user)
                {
                    $name = Translation :: get('UserNotFound');
                }
                else
                {
                    $name = $user->get_fullname();
                }

                return DatetimeUtilities :: format_locale_date(null, $last_post->get_creation_date()) .
                     '<br /><span class="text-primary">' . $name . '</span> <a href="' . $link . '"><img title="' .
                     Translation :: get('ViewLastPost') . '" src="' . Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Forum\Display',
                        'Icon/TopicLatest',
                        'gif') . '" /></a>';
            }
        }
        else
        {
            return '-';
        }
    }

    /**
     *
     * @param ComplexForumTopic $topic
     * @return string
     */
    public function renderTopicActions(ComplexForumTopic $topic)
    {
        $buttonToolBar = new ButtonToolBar();

        $parameters = array();

        $parameters[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $parameters[self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $topic->get_id();

        if (! $this->is_locked)
        {
            if ($this->get_user()->get_id() == $topic->get_user_id() || $this->get_user()->is_platform_admin() || $this->is_forum_manager(
                $this->get_user()))
            {
                $parameters[self :: PARAM_ACTION] = self :: ACTION_DELETE_TOPIC;

                if (! $this->get_complex_content_object_item())
                {
                    $parent_cloi = null;
                }
                else
                {
                    $parent_cloi = Request :: get('cloi');
                }

                $parameters[self :: PARAM_CURRENT_SESSION_PARENT_CLOI] = $parent_cloi;

                $buttonToolBar->addItem(
                    new Button(
                        Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                        new BootstrapGlyph('remove'),
                        $this->get_url($parameters),
                        Button :: DISPLAY_ICON,
                        true,
                        'btn-link'));

                $parameters[self :: PARAM_CURRENT_SESSION_PARENT_CLOI] = null;

                if ($topic->get_forum_type() == 1 && $this->get_user()->is_platform_admin())
                {
                    $parameters[self :: PARAM_ACTION] = self :: ACTION_MAKE_STICKY;

                    $buttonToolBar->addItem(
                        new Button(
                            Translation :: get('UnSticky'),
                            new FontAwesomeGlyph('star-o'),
                            $this->get_url($parameters),
                            Button :: DISPLAY_ICON,
                            false,
                            'btn-link'));
                }
                else
                {
                    if ($topic->get_forum_type() == 2 && ($this->get_user()->is_platform_admin() || $this->is_forum_manager(
                        $this->get_user())))
                    {
                        $parameters[self :: PARAM_ACTION] = self :: ACTION_MAKE_IMPORTANT;

                        $buttonToolBar->addItem(
                            new Button(
                                Translation :: get('UnImportant'),
                                new FontAwesomeGlyph('circle-o'),
                                $this->get_url($parameters),
                                Button :: DISPLAY_ICON,
                                false,
                                'btn-link'));
                    }
                    else
                    {
                        if ($this->get_user()->is_platform_admin() || $this->is_forum_manager($this->get_user()))
                        {
                            $parameters[self :: PARAM_ACTION] = self :: ACTION_MAKE_STICKY;
                            $buttonToolBar->addItem(
                                new Button(
                                    Translation :: get('MakeSticky'),
                                    new FontAwesomeGlyph('star'),
                                    $this->get_url($parameters),
                                    Button :: DISPLAY_ICON,
                                    false,
                                    'btn-link'));

                            $parameters[self :: PARAM_ACTION] = self :: ACTION_MAKE_IMPORTANT;
                            $buttonToolBar->addItem(
                                new Button(
                                    Translation :: get('MakeImportant'),
                                    new FontAwesomeGlyph('exclamation-circle'),
                                    $this->get_url($parameters),
                                    Button :: DISPLAY_ICON,
                                    false,
                                    'btn-link'));
                        }
                    }
                }
                if ($this->get_user()->is_platform_admin() || $this->is_forum_manager($this->get_user()))
                {
                    if (! $this->is_locked)
                    {
                        if ($topic->get_ref()->get_locked())
                        {
                            $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_LOCK;
                            $buttonToolBar->addItem(
                                new Button(
                                    Translation :: get('Unlock'),
                                    new FontAwesomeGlyph('unlock'),
                                    $this->get_url($parameters),
                                    Button :: DISPLAY_ICON,
                                    false,
                                    'btn-link'));
                        }
                        else
                        {
                            $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_LOCK;
                            $buttonToolBar->addItem(
                                new Button(
                                    Translation :: get('Lock'),
                                    new FontAwesomeGlyph('lock'),
                                    $this->get_url($parameters),
                                    Button :: DISPLAY_ICON,
                                    false,
                                    'btn-link'));
                        }
                    }
                }
            }

            $subscribed = DataManager :: retrieve_subscribe($topic->get_ref()->get_id(), $this->get_user_id());

            if (! $subscribed)
            {
                $parameters[self :: PARAM_ACTION] = self :: ACTION_TOPIC_SUBSCRIBE;
                $buttonToolBar->addItem(
                    new Button(
                        Translation :: get('Subscribe', null, ForumTopic :: package()),
                        new FontAwesomeGlyph('envelope'),
                        $this->get_url($parameters),
                        Button :: DISPLAY_ICON,
                        true,
                        'btn-link'));
            }
            else
            {
                $parameters[self :: PARAM_ACTION] = self :: ACTION_TOPIC_UNSUBSCRIBE;
                $parameters[self :: PARAM_SUBSCRIBE_ID] = $topic->get_ref()->get_id();
                $buttonToolBar->addItem(
                    new Button(
                        Translation :: get('UnSubscribe', null, ForumTopic :: package()),
                        new FontAwesomeGlyph('envelope-o'),
                        $this->get_url($parameters),
                        Button :: DISPLAY_ICON,
                        true,
                        'btn-link'));
            }
        }

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    // TODO: OLD CODE
    public function get_forums_table_html()
    {
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));

        $this->create_forums_table_header($table);
        $row = 2;
        $this->create_forums_table_content($table, $row);
        $this->create_forums_table_footer($table, $row);

        return $table;
    }

    public function create_forums_table_header($table)
    {
        $table->setCellContents(0, 0, '<b>' . Translation :: get('Subforums') . '</b>');
        $table->setCellAttributes(0, 0, array('colspan' => 6, 'class' => 'category'));

        $table->setHeaderContents(1, 0, Translation :: get('Forum'));
        $table->setCellAttributes(1, 0, array('colspan' => 2));
        $table->setHeaderContents(1, 2, Translation :: get('Topics'));
        $table->setCellAttributes(1, 2, array('width' => 50));
        $table->setHeaderContents(1, 3, Translation :: get('Posts'));
        $table->setCellAttributes(1, 3, array('width' => 50));
        $table->setHeaderContents(1, 4, Translation :: get('LastPostForum'));
        $table->setCellAttributes(1, 4, array('width' => 140));
        $table->setHeaderContents(1, 5, '');

        if ($this->is_locked)
        {
            $table->setCellAttributes(1, 5, array('width' => 40));
        }
        else
        {
            $table->setCellAttributes(1, 5, array('width' => 65));
        }
    }

    public function create_forums_table_footer($table, $row)
    {
        $table->setCellContents($row, 0, '');
        $table->setCellAttributes($row, 0, array('colspan' => 6, 'class' => 'category'));
    }

    public function create_forums_table_content($table, &$row)
    {
        if (count($this->forums) == 0)
        {
            $table->setCellAttributes(
                $row,
                0,
                array('colspan' => 6, 'style' => 'text-align: center; padding-top: 10px;'));
            $table->setCellContents($row, 0, '<h3>' . Translation :: get('NoSubforums') . '</h3>');
            $row ++;

            return;
        }

        foreach ($this->forums as $forum)
        {
            $last_post = DataManager :: retrieve_by_id(ForumPost :: class_name(), $forum->get_ref()->get_last_post());

            if ($forum->get_ref()->is_locked() && (! $this->get_user()->is_platform_admin() ||
                 ! ($this->get_user_id() == $forum->get_ref()->get_owner_id()) || ! $this->is_forum_manager(
                    $this->get_user())))
            {
                $title = $forum->get_ref()->get_title();
            }
            else
            {
                $title = '<a href="' . $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_FORUM,
                        self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forum->get_id())) . '">' .
                     $forum->get_ref()->get_title() . '</a><br />' . strip_tags($forum->get_ref()->get_description());
            }

            $src = Theme :: getInstance()->getImagePath(
                'Chamilo\Core\Repository\ContentObject\Forum\Display',
                'Forum/Read');

            if ($this->is_locked || $forum->get_ref()->get_locked())
            {
                $src = Theme :: getInstance()->getCommonImagePath('Action/Lock');
            }

            $table->setCellContents(
                $row,
                0,
                '<img title="' . Translation :: get('NoNewPosts') . '" src="' . $src . '" />');
            $table->setCellAttributes(
                $row,
                0,
                array('width' => 50, 'class' => 'row1', 'style' => 'height:50px; text-align: center;'));
            $table->setCellContents($row, 1, $title);
            $table->setCellAttributes($row, 1, array('class' => 'row1'));
            $table->setCellContents($row, 2, $forum->get_ref()->get_total_topics());
            $table->setCellAttributes($row, 2, array('class' => 'row2', 'align' => 'center'));
            $table->setCellContents($row, 3, $forum->get_ref()->get_total_posts());
            $table->setCellAttributes($row, 3, array('class' => 'row2', 'align' => 'center'));

            if ($last_post)
            {
                $name = "";
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    User :: class_name(),
                    (int) $last_post->get_user_id());

                if ($user)
                {
                    $name = $user->get_fullname();
                }
                else
                {
                    $name = Translation :: get('UserNotFound');
                }

                if ($forum->get_ref()->is_locked() && (! $this->is_forum_manager($this->get_user()) ||
                     ! $this->get_user()->is_platform_admin() ||
                     ! ($this->get_user_id() == $forum->get_ref()->get_owner_id())))
                {
                    $title = DatetimeUtilities :: format_locale_date(null, $last_post->get_creation_date()) . '<br />' .
                     $name;
                $table->setCellContents($row, 4, $title);
            }
            else
            {
                $link = $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_VIEW_TOPIC,
                        'pid' => $forum->get_ref(),
                        self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forum->get_ref()->get_last_topic_changed_cloi(),
                        self :: PARAM_LAST_POST => $last_post->get_id()));
                $table->setCellContents(
                    $row,
                    4,
                    DatetimeUtilities :: format_locale_date(null, $last_post->get_creation_date()) . '<br />' . $name .
                         ' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') . '" src="' . Theme :: getInstance()->getImagePath(
                            'Chamilo\Core\Repository\ContentObject\Forum\Display',
                            'Icon/TopicLatest',
                            'gif') . '" /></a>');
            }
        }
        else
        {
            $table->setCellContents($row, 4, '-');
        }

        $table->setCellAttributes($row, 4, array('align' => 'center', 'class' => 'row2'));
        $table->setCellContents($row, 5, $this->get_forum_actions($forum, true, true));
        $table->setCellAttributes($row, 5, array('class' => 'row2'));
        $row ++;
    }
}

public function getButtonToolbarRenderer()
{
    if (! isset($this->buttonToolbarRenderer))
    {
        $buttonToolbar = new ButtonToolBar();
        $commonActions = new ButtonGroup();
        $commonActions->addButton(
            new Button(
                Translation :: get('NewTopic'),
                Theme :: getInstance()->getCommonImagePath('Action/Add'),
                $this->get_url(
                    array(
                        self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                        self :: PARAM_ACTION => self :: ACTION_CREATE_TOPIC)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->get_user()->is_platform_admin() || $this->get_user_id() == $this->forum->get_owner_id() || $this->is_forum_manager(
            $this->get_user()))
        {
            $commonActions->addButton(
                new Button(
                    Translation :: get('NewSubForum'),
                    Theme :: getInstance()->getCommonImagePath('Action/Add'),
                    $this->get_url(
                        array(
                            self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                            self :: PARAM_ACTION => self :: ACTION_CREATE_SUBFORUM)),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $buttonToolbar->addButtonGroup($commonActions);
        $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
    }

    return $this->buttonToolbarRenderer;
}

public function get_forum_actions($forum)
{
    $tool_bar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

    $parameters = array();
    $parameters[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
    $parameters[self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
    if (! $this->is_locked)
    {
        if ($this->get_user()->get_id() == $forum->get_user_id() || $this->get_user()->is_platform_admin() || $this->is_forum_manager(
            $this->get_user()))
        {
            if (! $this->get_complex_content_object_item())
            {
                $parent_cloi = null;
            }
            else
            {
                $parent_cloi = Request :: get('cloi');
            }

            $parameters[self :: PARAM_ACTION] = self :: ACTION_DELETE_SUBFORUM;
            $parameters[self :: PARAM_CURRENT_SESSION_PARENT_CLOI] = $parent_cloi;
            $delete = new ToolbarItem(
                Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                $this->get_url($parameters),
                ToolbarItem :: DISPLAY_ICON,
                true);
            $parameters[self :: PARAM_CURRENT_SESSION_PARENT_CLOI] = null;

            $tool_bar->add_item($delete);
            $parameters[self :: PARAM_ACTION] = self :: ACTION_EDIT_SUBFORUM;
            $tool_bar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_url($parameters),
                    ToolbarItem :: DISPLAY_ICON));
            if ($this->get_user()->is_platform_admin() || $this->is_forum_manager($this->get_user()))
            {
                if (! $this->is_locked)
                {
                    if ($forum->get_ref()->get_locked())
                    {
                        $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_LOCK;
                        $tool_bar->add_item(
                            new ToolbarItem(
                                Translation :: get('Unlock'),
                                Theme :: getInstance()->getCommonImagePath('Action/Unlock'),
                                $this->get_url($parameters),
                                ToolbarItem :: DISPLAY_ICON));
                    }
                    else
                    {
                        $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_LOCK;
                        $tool_bar->add_item(
                            new ToolbarItem(
                                Translation :: get('Lock'),
                                Theme :: getInstance()->getCommonImagePath('Action/Lock'),
                                $this->get_url($parameters),
                                ToolbarItem :: DISPLAY_ICON));
                    }
                }
            }
        }
        $subscribed = \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager :: retrieve_subscribe(
            $forum->get_ref()->get_id(),
            $this->get_user_id());

        if (! $subscribed)
        {
            $parameters[self :: PARAM_ACTION] = self :: ACTION_FORUM_SUBSCRIBE;
            $parameters[self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
            $tool_bar->add_item(
                new ToolbarItem(
                    Translation :: get('Subscribe'),
                    Theme :: getInstance()->getImagePath(
                        ContentObject :: get_content_object_type_namespace('Forum'),
                        'Action/Mail'),
                    $this->get_url($parameters),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }
        else
        {
            $parameters[self :: PARAM_ACTION] = self :: ACTION_FORUM_UNSUBSCRIBE;
            $parameters[self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
            $parameters[self :: PARAM_SUBSCRIBE_ID] = $subscribed->get_id();
            $tool_bar->add_item(
                new ToolbarItem(
                    Translation :: get('UnSubscribe'),
                    Theme :: getInstance()->getImagePath(
                        ContentObject :: get_content_object_type_namespace('Forum'),
                        'Action/Unmail'),
                    $this->get_url($parameters),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }
    }

    return $tool_bar->as_html();
}

/**
 * ask the parent of the usee is a forum manager
 *
 * @param $user type return boolean
 */
public function is_forum_manager($user)
{
    $parent = $this->get_parent();

    return $parent->is_forum_manager($user);
}
}
