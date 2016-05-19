<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\CourseSettingsConnector;
use Chamilo\Application\Weblcms\CourseSettingsController;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Application\Weblcms\Storage\DataManager as WeblcmsDataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager as ForumDataManager;
use Chamilo\Core\Repository\ContentObject\Introduction\Storage\DataClass\Introduction;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\ResultSet\RecordResultSet;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_Table;

class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $introduction_text;

    private $size;

    public function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            throw new NotAllowedException();
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TOOL),
            new StaticConditionVariable('forum'));

        $subselect_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
            new StaticConditionVariable(Introduction :: class_name()));

        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            ContentObject :: get_table_name(),
            $subselect_condition);

        $condition = new AndCondition($conditions);

        $this->introduction_text = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve(
            ContentObjectPublication :: class_name(),
            new DataClassRetrieveParameters($condition));

        $this->size = 0;
        $this->allowed = $this->is_allowed(WeblcmsRights :: DELETE_RIGHT) ||
             $this->is_allowed(WeblcmsRights :: EDIT_RIGHT);
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $table = $this->get_table_html();

        $html = array();

        $html[] = $this->render_header();

        $intro_text_allowed = CourseSettingsController :: get_instance()->get_course_setting(
            $this->get_course(),
            CourseSettingsConnector :: ALLOW_INTRODUCTION_TEXT);

        if ($intro_text_allowed)
        {
            $html[] = $this->display_introduction_text($this->introduction_text);
        }

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $table;

        if ($this->size == 0)
        {
            $html[] = '<br><div style="text-align: center"><h3>' .
                 Translation :: get('NoPublications', null, Utilities :: COMMON_LIBRARIES) . '</h3></div>';
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_table_html()
    {
        $html = array();

        // Render forums published in the root
        $html[] = $this->renderTableForCategory();

        // Render forums published in categories
        $html[] = $this->renderTablesForCategories();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param ContentObjectPublicationCategory $category
     * @return string
     */
    public function renderTableForCategory(ContentObjectPublicationCategory $category = null)
    {
        $table = new HTML_Table(array('class' => 'table forum table-striped'));

        $header = $table->getHeader();
        $header->setHeaderContents(0, 0, '');
        $header->setCellAttributes(0, 0, array('class' => 'cell-stat'));

        if ($category instanceof ContentObjectPublicationCategory)
        {
            $categoryName = $category->get_name();
        }
        else
        {
            $categoryName = '';
        }

        $categoryTitle = array();

        $categoryTitle[] = '<h3>';
        $categoryTitle[] = $categoryName;

        if ($category instanceof ContentObjectPublicationCategory && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $rightsUrl = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Manager :: PARAM_CATEGORY => $category->get_id(),
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_EDIT_RIGHTS));

            $button = new Button(
                Translation :: get('ManageRights', null, Utilities :: COMMON_LIBRARIES),
                new BootstrapGlyph('lock'),
                $rightsUrl,
                Button :: DISPLAY_ICON,
                false,
                'btn-link');

            $buttonRenderer = new ButtonRenderer($button);

            $categoryTitle[] = $buttonRenderer->render();
        }

        $categoryTitle[] = '</h3>';

        $header->setHeaderContents(0, 1, implode(' ', $categoryTitle));

        $header->setHeaderContents(0, 2, Translation :: get("Topic", null, Forum :: package()));
        $header->setCellAttributes(0, 2, array('class' => 'cell-stat text-center hidden-xs hidden-sm'));
        $header->setHeaderContents(0, 3, Translation :: get("Posts", null, Forum :: package()));
        $header->setCellAttributes(0, 3, array('class' => 'cell-stat text-center hidden-xs hidden-sm'));
        $header->setHeaderContents(0, 4, Translation :: get("LastPostForum", null, Forum :: package()));
        $header->setCellAttributes(0, 4, array('class' => 'cell-stat-2x hidden-xs hidden-sm'));
        $header->setHeaderContents(0, 5, '');
        $header->setCellAttributes(0, 5, array('class' => 'cell-stat-2x'));

        $this->renderTableForumsForCategory($table, $category);

        return $table->toHtml();
    }

    /**
     *
     * @return string
     */
    public function renderTablesForCategories()
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_COURSE),
            new StaticConditionVariable($this->get_parent()->get_course_id()));

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_TOOL),
            new StaticConditionVariable($this->get_parent()->get_tool_id()));

        $order = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory :: class_name(),
                ContentObjectPublicationCategory :: PROPERTY_DISPLAY_ORDER));

        $categories = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieves(
            ContentObjectPublicationCategory :: class_name(),
            new DataClassRetrievesParameters(new AndCondition($conditions), null, null, $order));

        $html = array();

        while ($category = $categories->next_result())
        {
            $html[] = $this->renderTableForCategory($category);
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param ContentObjectPublicationCategory $category
     * @return \Chamilo\Libraries\Storage\ResultSet\RecordResultSet
     */
    public function getForumPublicationsForCategory(ContentObjectPublicationCategory $category = null)
    {
        if ($category instanceof ContentObjectPublicationCategory)
        {
            $categoryId = $category->get_id();
        }
        else
        {
            $categoryId = 0;
        }

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $user_id = array();
            $course_group_ids = array();
        }
        else
        {
            $user_id = $this->get_user_id();
            $course_groups = $this->get_course_groups();

            $course_group_ids = array();

            foreach ($course_groups as $course_group)
            {
                $course_group_ids[] = $course_group->get_id();
            }
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_COURSE_ID),
            new StaticConditionVariable($this->get_course_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_TOOL),
            new StaticConditionVariable('forum'));
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CATEGORY_ID),
            $categoryId);

        $subselect_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
            new StaticConditionVariable(Forum :: class_name()));

        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID),
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            ContentObject :: get_table_name(),
            $subselect_condition);

        $condition = new AndCondition($conditions);

        $location = $this->get_location($categoryId);
        $order[] = new OrderBy(
            new PropertyConditionVariable(
                ContentObjectPublication :: class_name(),
                ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX));
        if (! $this->get_course()->is_course_admin($this->get_user()))
        {
            $publications = WeblcmsDataManager :: retrieve_content_object_publications_with_view_right_granted_in_category_location(
                $location,
                $this->get_entities(),
                $condition,
                $order,
                0,
                - 1,
                $this->get_user_id());
        }
        else
        {
            $publications = \Chamilo\Application\Weblcms\Storage\DataManager :: retrieve_content_object_publications(
                $condition,
                $order);
        }

        return $publications;
    }

    /**
     *
     * @param string[] $publication
     * @param Forum $forum
     * @return string
     */
    public function renderLastPost($publication, Forum $forum)
    {
        $lastPost = \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager :: retrieve_forum_post(
            $forum->get_last_post());
        $html = array();

        if ($lastPost)
        {
            $link = $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_VIEW,
                    self :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                    \Chamilo\Core\Repository\Display\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Forum\Display\Manager :: ACTION_VIEW_TOPIC,
                    'pid' => $forum->get_id(),
                    \Chamilo\Core\Repository\Display\Manager :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forum->get_last_topic_changed_cloi(),
                    \Chamilo\Core\Repository\ContentObject\Forum\Display\Manager :: PARAM_LAST_POST => $lastPost->get_id()));

            $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                $lastPost->get_user_id());

            if (! $user)
            {
                $name = Translation :: get(
                    'UserNotFound',
                    null,
                    ContentObject :: get_content_object_type_namespace('forum'));
            }
            else
            {
                $name = $user->get_fullname();
            }

            $html[] = DatetimeUtilities :: format_locale_date(null, $lastPost->get_creation_date());
            $html[] = '<br />';
            $html[] = $name;

            if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT) || ! $forum->get_locked())
            {
                $html[] = '&nbsp;';
                $html[] = '<a href="' . $link . '">';
                $html[] = '<img title="' .
                     Translation :: get('ViewLastPost', null, 'Chamilo\Core\Repository\ContentObject\Forum') . '" src="' . Theme :: getInstance()->getImagePath(
                        'Chamilo\Application\Weblcms\Tool\Implementation\Forum',
                        'Forum/IconTopicLatest',
                        'gif') . '" />';
                $html[] = '</a>';
            }
        }
        else
        {
            $html[] = '-';
        }

        return implode('', $html);
    }

    /**
     *
     * @param string[] $publication
     * @param Forum $forum
     * @return string
     */
    public function renderTableForumTitle($publication, Forum $forum)
    {
        $canViewForum = ! $forum->get_locked() || $this->is_allowed(WeblcmsRights :: EDIT_RIGHT);
        $titleParts = array();

        if ($canViewForum)
        {
            $forumUrl = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_VIEW_FORUM,
                    \Chamilo\Core\Repository\Display\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Forum\Display\Manager :: ACTION_VIEW_FORUM,
                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID]));

            $titleParts[] = '<a href="' . $forumUrl . '">';
        }

        $titleParts[] = $forum->get_title();

        if ($canViewForum)
        {
            $titleParts[] = '</a>';
        }

        $titleParts[] = '<br />';

        $titleParts[] = '<small>';
        $titleParts[] = strip_tags($forum->get_description());
        $titleParts[] = '</small>';

        $title = implode('', $titleParts);

        if ($publication[ContentObjectPublication :: PROPERTY_HIDDEN])
        {
            $title = '<span style="color: grey;">' . $title . '</span>';
        }

        $title = '<h4>' . $title . '</h4>';

        return $title;
    }

    /**
     *
     * @param HTML_Table $table
     * @param ContentObjectPublicationCategory $category
     */
    public function renderTableForumsForCategory(HTML_Table $table, ContentObjectPublicationCategory $category = null)
    {
        $publications = $this->getForumPublicationsForCategory($category);

        if ($publications instanceof RecordResultSet && $publications->size() > 0)
        {
            $this->size += $publications->size();
            $row = 0;

            while ($publication = $publications->next_result())
            {
                if (! ($publication[ContentObjectPublication :: PROPERTY_HIDDEN] &&
                     ! $this->is_allowed(WeblcmsRights :: EDIT_RIGHT)))
                {
                    $forum = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                        Forum :: class_name(),
                        $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

                    $forumGlyphType = $forum->get_locked() ? 'lock' : 'file';
                    $forumGlyph = new BootstrapGlyph($forumGlyphType, array('text-muted'));

                    $table->setCellContents($row, 0, $forumGlyph->render());
                    $table->setCellAttributes($row, 0, array('class' => 'text-center'));
                    $table->setCellContents($row, 1, $this->renderTableForumTitle($publication, $forum));
                    $table->setCellContents($row, 2, '<a>' . $forum->get_total_topics() . '</a>');
                    $table->setCellAttributes($row, 2, array('class' => 'text-center hidden-xs hidden-sm'));
                    $table->setCellContents($row, 3, '<a>' . $forum->get_total_posts() . '</a>');
                    $table->setCellAttributes($row, 3, array('class' => 'text-center hidden-xs hidden-sm'));
                    $table->setCellContents($row, 4, $this->renderLastPost($publication, $forum));
                    $table->setCellAttributes($row, 4, array('class' => 'hidden-xs hidden-sm'));

                    $table->setCellContents(
                        $row,
                        5,
                        $this->getForumActions($publication, $publications->is_first(), $publications->is_last()));

                    $row ++;
                }
            }
        }
    }

    /**
     *
     * @param string[] $publication
     * @param boolean $first
     * @param boolean $last
     * @return string
     */
    public function getForumActions($publication, $first, $last)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            if ($publication[ContentObjectPublication :: PROPERTY_HIDDEN])
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Show', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Invisible'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_TOGGLE_VISIBILITY)),
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Hide', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Visible'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_TOGGLE_VISIBILITY)),
                        ToolbarItem :: DISPLAY_ICON));
            }

            if ($first)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('MoveUpNA', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/UpNa'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('MoveUp', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Up'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_MOVE,
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_MOVE_DIRECTION => \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_MOVE_DIRECTION_UP)),
                        ToolbarItem :: DISPLAY_ICON));
            }

            if ($last)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('MoveDownNA', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/DownNa'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('MoveDown', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Down'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_MOVE,
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_MOVE_DIRECTION => \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_MOVE_DIRECTION_DOWN)),
                        ToolbarItem :: DISPLAY_ICON));
            }

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Move'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_MOVE_TO_CATEGORY)),
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditContentObject', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_UPDATE_CONTENT_OBJECT)),
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('EditPublicationDetails', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getImagePath(
                        \Chamilo\Application\Weblcms\Manager :: context(),
                        'Action/EditPublication'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_UPDATE_PUBLICATION)),
                    ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('BuildComplexObject', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Build'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT)),
                    ToolbarItem :: DISPLAY_ICON));

            $forum = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                Forum :: class_name(),
                $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);

            if ($forum->get_locked())
            {
                $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_LOCK;
                $parameters[self :: PARAM_PUBLICATION_ID] = $publication[ContentObjectPublication :: PROPERTY_ID];
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Unlock'),
                        Theme :: getInstance()->getCommonImagePath('Action/Unlock'),
                        $this->get_url($parameters),
                        ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $parameters[self :: PARAM_ACTION] = self :: ACTION_CHANGE_LOCK;
                $parameters[self :: PARAM_PUBLICATION_ID] = $publication[ContentObjectPublication :: PROPERTY_ID];

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Lock'),
                        Theme :: getInstance()->getCommonImagePath('Action/Lock'),
                        $this->get_url($parameters),
                        ToolbarItem :: DISPLAY_ICON));
            }
        }
        else
        {
            $forum = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                Forum :: class_name(),
                $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID]);
        }

        if ($this->is_allowed(WeblcmsRights :: DELETE_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID],
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_DELETE)),
                    ToolbarItem :: DISPLAY_ICON,
                    true));
        }

        $forum_namespace = ContentObject :: get_content_object_type_namespace('Forum');

        if (! $forum->get_locked())
        {
            $subscribed = ForumDataManager :: retrieve_subscribe($forum->get_id(), $this->get_user_id());

            if (! $subscribed)
            {
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = self :: ACTION_FORUM_SUBSCRIBE;
                $parameters[self :: PARAM_FORUM_ID] = $forum->get_id();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $publication[ContentObjectPublication :: PROPERTY_ID];

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Subscribe', null, $forum_namespace),
                        Theme :: getInstance()->getImagePath($forum_namespace, 'Action/Mail'),
                        $this->get_url($parameters),
                        ToolbarItem :: DISPLAY_ICON,
                        true));
            }
            else
            {

                $parameters[self :: PARAM_SUBSCRIBE_ID] = $forum->get_id();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION] = self :: ACTION_FORUM_UNSUBSCRIBE;
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID] = $publication[ContentObjectPublication :: PROPERTY_ID];

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('UnSubscribe', null, $forum_namespace),
                        Theme :: getInstance()->getImagePath($forum_namespace, 'Action/Unmail'),
                        $this->get_url($parameters),
                        ToolbarItem :: DISPLAY_ICON,
                        true));
            }
        }
        return '<div style="float: right;">' . $toolbar->as_html() . '</div>';
    }

    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
            {
                // added tool dependent publish button
                $tool_dependent_publish = PlatformSetting :: get(
                    'tool_dependent_publish_button',
                    \Chamilo\Application\Weblcms\Manager :: context());

                if ($tool_dependent_publish == \Chamilo\Application\Weblcms\Tool\Manager :: PUBLISH_INDEPENDENT)
                {
                    $commonActions->addButton(
                        new Button(
                            Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getCommonImagePath('Action/Publish'),
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_PUBLISH)),
                            ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
                else
                {
                    $tool = Request :: get('tool');
                    $commonActions->addButton(
                        new Button(
                            Translation :: get(
                                'PublishToolDependent',
                                array(
                                    'TYPE' => Translation :: get(
                                        'TypeNameSingle',
                                        null,
                                        'application\weblcms\tool\\' . $tool)),
                                Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getCommonImagePath('Action/Publish'),
                            $this->get_url(
                                array(
                                    \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_PUBLISH)),
                            ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                }
            }

            if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        Translation :: get('ManageCategories', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Category'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES)),
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }

            if ($this->get_course()->is_course_admin($this->get_user()) || $this->get_user()->is_platform_admin())
            {
                $commonActions->addButton(
                    new Button(
                        Translation :: get('ManageRights', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Rights'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_EDIT_RIGHTS)),
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }

            $intro_text_allowed = CourseSettingsController :: get_instance()->get_course_setting(
                $this->get_course(),
                CourseSettingsConnector :: ALLOW_INTRODUCTION_TEXT);

            if (! $this->introduction_text && $intro_text_allowed && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        Translation :: get('PublishIntroductionText', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Introduce'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager :: ACTION_PUBLISH_INTRODUCTION)),
                        ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }

            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }
}
