<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
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

    private $action_bar;

    private $forums;

    private $topics;

    private $is_locked;

    private $forum;

    public function run()
    {
        if (! $this->get_complex_content_object_item())
        {
            $forum = $this->get_root_content_object();
        }
        else
        {
            $forum = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                $this->get_complex_content_object_item()->get_ref());
        }

        $this->is_locked = $forum->is_locked();

        $this->forum = $forum;

        $this->retrieve_children($forum);

        $this->action_bar = $this->get_action_bar();
        $topics_table = $this->get_topics_table_html();
        $forum_table = $this->get_forums_table_html();

        $trail = BreadcrumbTrail :: get_instance();

        // TODO: search right path but at a greater level not here
        // for the trail only the root content and the forum because we can't
        // know the path if
        // a subforum has different parents.

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
            $html[] = $this->action_bar->as_html();
        }

        $html[] = $topics_table->toHtml();

        if (count($this->forums) > 0)
        {
            $html[] = '<br /><br />';
            $html[] = $forum_table->toHtml();
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Get the subforums of a $forum
     *
     * @param $forum type
     *
     * @return $forums
     */
    private function retrieve_children_subforum($forum)
    {
        $forums = array();

        $children = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_PARENT),
                new StaticConditionVariable($forum->get_id()),
                ComplexContentObjectItem :: get_table_name()));
        while ($child = $children->next_result())
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($child->get_ref());
            $child->set_ref($lo);
            if ($lo->get_type() != ForumTopic :: class_name())
            {
                $forums[] = $child;
            }
        }

        return $forums;
    }

    public function retrieve_children($current_forum)
    {
        $order_property[] = new OrderBy(
            new PropertyConditionVariable(
                ComplexContentObjectItem :: class_name(),
                ComplexContentObjectItem :: PROPERTY_ADD_DATE));
        $parameters = new DataClassRetrievesParameters(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_PARENT),
                new StaticConditionVariable($current_forum->get_id())),
            null,
            null,
            $order_property);
        $children = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_complex_content_object_items(
            ComplexContentObjectItem :: class_name(),
            $parameters);
        while ($child = $children->next_result())
        {
            $lo = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object($child->get_ref());
            $child->set_ref($lo);
            if ($lo->get_type() == ForumTopic :: class_name())
            {
                $this->topics[] = $child;
            }
            else
            {
                $this->forums[] = $child;
            }
        }

        $this->sort_topics();
    }

    private function sort_topics()
    {
        $sorted_array = array();
        foreach ($this->topics as $key => $value)
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

        $this->topics = $array;
    }

    public function get_topics_table_html()
    {
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));

        $this->create_topics_table_header($table);
        $row = 2;
        $this->create_topics_table_content($table, $row);
        $this->create_topics_table_footer($table, $row);

        return $table;
    }

    public function create_topics_table_header($table)
    {
        $table->setCellContents(0, 0, '<b>' . Translation :: get('Topics') . '</b>');
        $table->setCellAttributes(0, 0, array('colspan' => 7, 'class' => 'category'));

        $table->setHeaderContents(1, 0, Translation :: get('Topics'));
        $table->setCellAttributes(1, 0, array('colspan' => 2));
        $table->setHeaderContents(1, 2, Translation :: get('Author'));
        $table->setCellAttributes(1, 2, array('width' => 130));
        $table->setHeaderContents(1, 3, Translation :: get('Replies'));
        $table->setCellAttributes(1, 3, array('width' => 50));
        $table->setHeaderContents(1, 4, Translation :: get('Views'));
        $table->setCellAttributes(1, 4, array('width' => 50));
        $table->setHeaderContents(1, 5, Translation :: get('LastPostTopic'));
        $table->setCellAttributes(1, 5, array('width' => 140));
        $table->setHeaderContents(1, 6, '');

        if ($this->is_locked)
        {
            $table->setCellAttributes(1, 6, array('width' => 60));
        }
        else
        {
            $table->setCellAttributes(1, 6, array('width' => 110));
        }
    }

    public function create_topics_table_footer($table, $row)
    {
        $table->setCellContents($row, 0, '');
        $table->setCellAttributes($row, 0, array('colspan' => 7, 'class' => 'category'));
    }

    public function create_topics_table_content($table, &$row)
    {
        if (count($this->topics) == 0)
        {
            $table->setCellAttributes(
                $row,
                0,
                array('colspan' => 7, 'style' => 'text-align: center; padding-top: 10px;'));
            $table->setCellContents($row, 0, '<h3>' . Translation :: get('NoTopics') . '</h3>');
            $row ++;
            return;
        }

        foreach ($this->topics as $topic)
        {
            // if ($topic->get_ref()->is_locked() && (! $this->get_user()->is_platform_admin() || !
            // ($this->get_user_id() == $topic->get_ref()->get_owner_id())))
            // {
            // $title = $topic->get_ref()->get_title();
            // }
            // else
            // {
            $title = '<a href="' . $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_VIEW_TOPIC,
                    self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $topic->get_id())) . '">' .
                 $topic->get_ref()->get_title() . '</a>';
            // }

            $last_post = DataManager :: retrieve_by_id(ForumPost :: class_name(), $topic->get_ref()->get_last_post());

            $count = $topic->get_ref()->get_total_posts();
            $src = Theme :: getInstance()->getImagePath(
                'Chamilo\Core\Repository\ContentObject\Forum\Display',
                'topic_read');
            $hover = 'NoNewPosts';
            switch ($topic->get_forum_type())
            {
                case 1 :
                    $src = Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Forum\Display',
                        'sticky_read');
                    $hover = 'Sticky';

                    break;
                case 2 :
                    $src = Theme :: getInstance()->getImagePath(
                        'Chamilo\Core\Repository\ContentObject\Forum\Display',
                        'important_read');
                    $hover = 'Important';
                    break;
            }

            if ($this->is_locked || $topic->get_ref()->get_locked())
            {
                $src = Theme :: getInstance()->getCommonImagesPath() . 'action_lock.png';
                $hover = 'Locked';
            }

            $table->setCellContents($row, 0, '<img title="' . Translation :: get($hover) . '" src="' . $src . '"/>');
            $table->setCellAttributes($row, 0, array('width' => 25, 'class' => 'row1', 'style' => 'height: 30px;'));
            $table->setCellContents($row, 1, $title);
            $table->setCellAttributes($row, 1, array('class' => 'row1'));

            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                User :: class_name(),
                (int) $topic->get_user_id());
            $name = "";
            if (! $user)
            {
                $name = Translation :: get('UserNotFound');
            }
            else
            {
                $name = $user->get_fullname();
            }

            $table->setCellContents($row, 2, $name);
            $table->setCellAttributes($row, 2, array('align' => 'center', 'class' => 'row2'));
            $table->setCellContents($row, 3, ($count > 0) ? $count - 1 : $count);
            $table->setCellAttributes($row, 3, array('align' => 'center', 'class' => 'row1'));

            $views = $this->forum_count_topic_views($topic->get_id());

            $table->setCellContents($row, 4, $views);
            $table->setCellAttributes($row, 4, array('align' => 'center', 'class' => 'row2'));

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

                    $table->setCellContents(
                        $row,
                        5,
                        DatetimeUtilities :: format_locale_date(null, $last_post->get_creation_date()) . '<br />' . $name);
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

                    $table->setCellContents(
                        $row,
                        5,
                        DatetimeUtilities :: format_locale_date(null, $last_post->get_creation_date()) . '<br />' . $name .
                             ' <a href="' . $link . '"><img title="' . Translation :: get('ViewLastPost') . '" src="' . Theme :: getInstance()->getImagePath(
                                'Chamilo\Core\Repository\ContentObject\Forum\Display',
                                'icon_topic_latest') . '" /></a>');
                }
            }
            else
            {
                $table->setCellContents($row, 5, '-');
            }

            $table->setCellAttributes($row, 5, array('align' => 'center', 'class' => 'row1'));
            $table->setCellContents($row, 6, $this->get_topic_actions($topic));
            $table->setCellAttributes($row, 6, array('align' => 'center', 'class' => 'row1'));
            $row ++;
        }
    }

    public function get_topic_actions($topic)
    {
        $tool_bar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

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
                $tool_bar->add_item(
                    new ToolbarItem(
                        Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png',
                        $this->get_url($parameters),
                        ToolbarItem :: DISPLAY_ICON,
                        true));
                $parameters[self :: PARAM_CURRENT_SESSION_PARENT_CLOI] = null;

                if ($topic->get_forum_type() == 1 && $this->get_user()->is_platform_admin())
                {
                    $parameters[self :: PARAM_ACTION] = self :: ACTION_MAKE_STICKY;

                    $tool_bar->add_item(
                        new ToolbarItem(
                            Translation :: get('UnSticky'),
                            Theme :: getInstance()->getCommonImagesPath() . 'action_remove_sticky.png',
                            $this->get_url($parameters),
                            ToolbarItem :: DISPLAY_ICON));

                    $tool_bar->add_item(
                        new ToolbarItem(
                            Translation :: get('ImportantNa'),
                            Theme :: getInstance()->getCommonImagesPath() . 'action_make_important_na.png',
                            null,
                            ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    if ($topic->get_forum_type() == 2 && ($this->get_user()->is_platform_admin() || $this->is_forum_manager(
                        $this->get_user())))
                    {
                        $parameters[self :: PARAM_ACTION] = self :: ACTION_MAKE_IMPORTANT;

                        $tool_bar->add_item(
                            new ToolbarItem(
                                Translation :: get('StickyNa'),
                                Theme :: getInstance()->getCommonImagesPath() . 'action_make_sticky_na.png',
                                null,
                                ToolbarItem :: DISPLAY_ICON));

                        $tool_bar->add_item(
                            new ToolbarItem(
                                Translation :: get('UnImportant'),
                                Theme :: getInstance()->getCommonImagesPath() . 'action_remove_important.png',
                                $this->get_url($parameters),
                                ToolbarItem :: DISPLAY_ICON));
                    }
                    else
                    {
                        if ($this->get_user()->is_platform_admin() || $this->is_forum_manager($this->get_user()))
                        {
                            $parameters[self :: PARAM_ACTION] = self :: ACTION_MAKE_STICKY;
                            $tool_bar->add_item(
                                new ToolbarItem(
                                    Translation :: get('MakeSticky'),
                                    Theme :: getInstance()->getCommonImagesPath() . 'action_make_sticky.png',
                                    $this->get_url($parameters),
                                    ToolbarItem :: DISPLAY_ICON));

                            $parameters[self :: PARAM_ACTION] = self :: ACTION_MAKE_IMPORTANT;
                            $tool_bar->add_item(
                                new ToolbarItem(
                                    Translation :: get('MakeImportant'),
                                    Theme :: getInstance()->getCommonImagesPath() . 'action_make_important.png',
                                    $this->get_url($parameters),
                                    ToolbarItem :: DISPLAY_ICON));
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
                            $tool_bar->add_item(
                                new ToolbarItem(
                                    Translation :: get('Unlock'),
                                    Theme :: getInstance()->getCommonImagesPath() . 'action_unlock.png',
                                    $this->get_url($parameters),
                                    ToolbarItem :: DISPLAY_ICON));
                        }
                        else
                        {
                            $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_LOCK;
                            $tool_bar->add_item(
                                new ToolbarItem(
                                    Translation :: get('Lock'),
                                    Theme :: getInstance()->getCommonImagesPath() . 'action_lock.png',
                                    $this->get_url($parameters),
                                    ToolbarItem :: DISPLAY_ICON));
                        }
                    }
                }
            }

            if ($this->get_parent()->is_allowed(EDIT_RIGHT))
            {
            }

            $subscribed = DataManager :: retrieve_subscribe($topic->get_ref()->get_id(), $this->get_user_id());

            if (! $subscribed)
            {
                $parameters[self :: PARAM_ACTION] = self :: ACTION_TOPIC_SUBSCRIBE;
                $tool_bar->add_item(
                    new ToolbarItem(
                        Translation :: get(
                            'Subscribe',
                            null,
                            ContentObject :: get_content_object_type_namespace('forum_topic')),
                        Theme :: getInstance()->getImagePath(
                            ContentObject :: get_content_object_type_namespace('forum'),
                            'action_mail'),
                        $this->get_url($parameters),
                        ToolbarItem :: DISPLAY_ICON,
                        true));
            }
            else
            {
                // $parameters[self :: PARAM_SUBSCRIBE_ID] =
                // $subscribed->get_id();
                $parameters[self :: PARAM_ACTION] = self :: ACTION_TOPIC_UNSUBSCRIBE;
                $parameters[self :: PARAM_SUBSCRIBE_ID] = $topic->get_ref()->get_id();
                $tool_bar->add_item(
                    new ToolbarItem(
                        Translation :: get(
                            'UnSubscribe',
                            null,
                            ContentObject :: get_content_object_type_namespace('forum_topic')),
                        Theme :: getInstance()->getImagePath(
                            ContentObject :: get_content_object_type_namespace('forum'),
                            'action_unmail'),
                        $this->get_url($parameters),
                        ToolbarItem :: DISPLAY_ICON,
                        true));
            }
        }
        return $tool_bar->as_html();
    }

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
                'forum_read');

            if ($this->is_locked || $forum->get_ref()->get_locked())
            {
                $src = Theme :: getInstance()->getCommonImagesPath() . 'action_lock.png';
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
                            'icon_topic_latest') . '" /></a>');
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

public function get_action_bar()
{
    $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

    $action_bar->add_common_action(
        new ToolbarItem(
            Translation :: get('NewTopic'),
            Theme :: getInstance()->getCommonImagesPath() . 'action_add.png',
            $this->get_url(
                array(
                    self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                    self :: PARAM_ACTION => self :: ACTION_CREATE_TOPIC)),
            ToolbarItem :: DISPLAY_ICON_AND_LABEL));

    if ($this->get_user()->is_platform_admin() || $this->get_user_id() == $this->forum->get_owner_id() || $this->is_forum_manager(
        $this->get_user()))
    {
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('NewSubForum'),
                Theme :: getInstance()->getCommonImagesPath() . 'action_add.png',
                $this->get_url(
                    array(
                        self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                        self :: PARAM_ACTION => self :: ACTION_CREATE_SUBFORUM)),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL));
    }

    return $action_bar;
}

public function get_forum_actions($forum)
{
    $tool_bar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

    $parameters = array();
    $parameters[self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
    $parameters[self :: PARAM_SELECTED_] = $forum->get_id();
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
                Theme :: getInstance()->getCommonImagesPath() . 'action_delete.png',
                $this->get_url($parameters),
                ToolbarItem :: DISPLAY_ICON,
                true);
            $parameters[self :: PARAM_CURRENT_SESSION_PARENT_CLOI] = null;

            $tool_bar->add_item($delete);
            $parameters[self :: PARAM_ACTION] = self :: ACTION_EDIT_SUBFORUM;
            $tool_bar->add_item(
                new ToolbarItem(
                    Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_edit.png',
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
                                Theme :: getInstance()->getCommonImagesPath() . 'action_unlock.png',
                                $this->get_url($parameters),
                                ToolbarItem :: DISPLAY_ICON));
                    }
                    else
                    {
                        $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_LOCK;
                        $tool_bar->add_item(
                            new ToolbarItem(
                                Translation :: get('Lock'),
                                Theme :: getInstance()->getCommonImagesPath() . 'action_lock.png',
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
                        ContentObject :: get_content_object_type_namespace('forum'),
                        'action_mail'),
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
                        ContentObject :: get_content_object_type_namespace('forum'),
                        'action_unmail'),
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
