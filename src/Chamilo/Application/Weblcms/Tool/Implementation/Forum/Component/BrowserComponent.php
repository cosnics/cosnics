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
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButtonDivider;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrieveParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\SubselectCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
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
        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT))
        {
            throw new NotAllowedException();
        }

        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($this->get_course_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable('forum')
        );

        $subselect_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable(Introduction::class)
        );

        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
            ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), $subselect_condition
        );

        $condition = new AndCondition($conditions);

        $this->introduction_text = WeblcmsDataManager::retrieve(
            ContentObjectPublication::class, new DataClassRetrieveParameters($condition)
        );

        $this->size = 0;
        $this->allowed = $this->is_allowed(WeblcmsRights::DELETE_RIGHT) || $this->is_allowed(WeblcmsRights::EDIT_RIGHT);

        $intro_text_allowed = CourseSettingsController::getInstance()->get_course_setting(
            $this->get_course(), CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
        );

        $html = [];

        $html[] = $this->render_header();

        if ($intro_text_allowed)
        {
            $html[] = $this->display_introduction_text($this->introduction_text);
        }

        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->renderForum();

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $publishActions = new ButtonGroup();

            $publishParameters = $this->get_parameters();
            $publishParameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                \Chamilo\Application\Weblcms\Tool\Manager::ACTION_PUBLISH;

            if ($this->is_allowed(WeblcmsRights::ADD_RIGHT))
            {
                $publishActions->addButton(
                    $this->getPublicationButton(
                        Translation::get('Publish', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('plus'),
                        $this->get_allowed_types(), $publishParameters, [], 'btn-primary'
                    )
                );
            }

            $intro_text_allowed = CourseSettingsController::getInstance()->get_course_setting(
                $this->get_course(), CourseSettingsConnector::ALLOW_INTRODUCTION_TEXT
            );

            $publishParameters = $this->get_parameters();
            $publishParameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] =
                \Chamilo\Application\Weblcms\Tool\Manager::ACTION_PUBLISH_INTRODUCTION;

            if (!$this->introduction_text && $intro_text_allowed && $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $publishActions->addButton(
                    $this->getPublicationButton(
                        Translation::get('PublishIntroductionText', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('book'), array(Introduction::class), $publishParameters
                    )
                );
            }

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('ManageCategories', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('folder'), $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_MANAGE_CATEGORIES
                        )
                    ), Button::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            if ($this->get_course()->is_course_admin($this->get_user()) || $this->get_user()->is_platform_admin())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('ManageRights', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('lock'), $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS
                        )
                    ), Button::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($publishActions);
            $buttonToolbar->addButtonGroup($commonActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     *
     * @param string[] $publication
     * @param boolean $first
     * @param boolean $last
     *
     * @return string
     */
    public function getForumActions($publication, $first, $last)
    {
        $forum = DataManager::retrieve_by_id(
            Forum::class, $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
        );

        $buttonToolBar = new ButtonToolBar();

        $dropdownButton = new DropdownButton(
            Translation::get('Actions'), new FontAwesomeGlyph('cog'), Button::DISPLAY_ICON, 'btn-link'
        );
        $dropdownButton->setDropdownClasses('dropdown-menu-right');

        if (!$forum->get_locked())
        {
            $subscribed = ForumDataManager::retrieve_subscribe($forum->get_id(), $this->get_user_id());

            if (!$subscribed)
            {
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = self::ACTION_FORUM_SUBSCRIBE;
                $parameters[self::PARAM_FORUM_ID] = $forum->get_id();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] =
                    $publication[ContentObjectPublication::PROPERTY_ID];

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('Subscribe', null, Forum::package()), new FontAwesomeGlyph('envelope'),
                        $this->get_url($parameters), Button::DISPLAY_ICON, true, 'btn-link'
                    )
                );
            }
            else
            {

                $parameters[self::PARAM_SUBSCRIBE_ID] = $forum->get_id();
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION] = self::ACTION_FORUM_UNSUBSCRIBE;
                $parameters[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID] =
                    $publication[ContentObjectPublication::PROPERTY_ID];

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('UnSubscribe', null, Forum::package()),
                        new FontAwesomeGlyph('envelope', [], null, 'far'), $this->get_url($parameters),
                        Button::DISPLAY_ICON, true, 'btn-link'
                    )
                );
            }
        }

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $buttonToolBar->addItem(
                new Button(
                    Translation::get('EditContentObject', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('pencil-alt'), $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_CONTENT_OBJECT
                    )
                ), Button::DISPLAY_ICON, false, 'btn-link'
                )
            );

            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('EditPublicationDetails', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('cog'), $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_UPDATE_PUBLICATION
                    )
                ), SubButton::DISPLAY_LABEL, false, 'btn-link'
                )
            );

            $dropdownButton->addSubButton(new SubButtonDivider());

            if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('Show', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('eye-slash'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY
                            )
                        ), SubButton::DISPLAY_LABEL, false, 'btn-link'
                    )
                );
            }
            else
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('Hide', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('eye'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_TOGGLE_VISIBILITY
                            )
                        ), SubButton::DISPLAY_LABEL, false, 'btn-link'
                    )
                );
            }

            if ($forum->get_locked())
            {
                $parameters[self::PARAM_ACTION] = self::ACTION_CHANGE_LOCK;
                $parameters[self::PARAM_PUBLICATION_ID] = $publication[ContentObjectPublication::PROPERTY_ID];

                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('Unlock'), new FontAwesomeGlyph('unlock'), $this->get_url($parameters),
                        SubButton::DISPLAY_LABEL, false, 'btn-link'
                    )
                );
            }
            else
            {
                $parameters[self::PARAM_ACTION] = self::ACTION_CHANGE_LOCK;
                $parameters[self::PARAM_PUBLICATION_ID] = $publication[ContentObjectPublication::PROPERTY_ID];

                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('Lock'), new FontAwesomeGlyph('lock'), $this->get_url($parameters),
                        SubButton::DISPLAY_LABEL, false, 'btn-link'
                    )
                );
            }

            $dropdownButton->addSubButton(new SubButtonDivider());

            $dropdownButton->addSubButton(
                new SubButton(
                    Translation::get('Move', null, Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('window-restore', array('fa-flip-horizontal'), null, 'fas'), $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE_TO_CATEGORY
                    )
                ), SubButton::DISPLAY_LABEL, false, 'btn-link'
                )
            );

            if (!$first)
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('MoveUp', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('chevron-up'), $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION => \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION_UP
                        )
                    ), SubButton::DISPLAY_LABEL, false, 'btn-link'
                    )
                );
            }

            if (!$last)
            {
                $dropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('MoveDown', null, Utilities::COMMON_LIBRARIES),
                        new FontAwesomeGlyph('chevron-down'), $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_MOVE,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION => \Chamilo\Application\Weblcms\Tool\Manager::PARAM_MOVE_DIRECTION_DOWN
                        )
                    ), SubButton::DISPLAY_LABEL, false, 'btn-link'
                    )
                );
            }
        }

        if ($this->is_allowed(WeblcmsRights::DELETE_RIGHT))
        {
            $buttonToolBar->addItem(
                new Button(
                    Translation::get('Delete', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_DELETE
                        )
                    ), Button::DISPLAY_ICON, true, 'btn-link'
                )
            );
        }

        $buttonToolBar->addItem($dropdownButton);

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    /**
     *
     * @param ContentObjectPublicationCategory $category
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator<\Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication>
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

        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_COURSE_ID
            ), new StaticConditionVariable($this->get_course_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_TOOL
            ), new StaticConditionVariable('forum')
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CATEGORY_ID
            ), $categoryId
        );

        $subselect_condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
            new StaticConditionVariable(Forum::class)
        );

        $conditions[] = new SubselectCondition(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID
            ), new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), $subselect_condition
        );

        $condition = new AndCondition($conditions);

        $order = [];
        $order[] = new OrderProperty(
            new PropertyConditionVariable(
                ContentObjectPublication::class, ContentObjectPublication::PROPERTY_DISPLAY_ORDER_INDEX
            )
        );

        if (!$this->get_course()->is_course_admin($this->get_user()))
        {
            $publications =
                WeblcmsDataManager::retrieve_content_object_publications_with_view_right_granted_in_category_location(
                    $this->get_location($categoryId), $this->get_entities(), $condition, new OrderBy($order), 0, - 1,
                    $this->get_user_id()
                );
        }
        else
        {
            $publications = WeblcmsDataManager::retrieve_content_object_publications(
                $condition, new OrderBy($order)
            );
        }

        return $publications;
    }

    /**
     *
     * @param string $label
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\InlineGlyph $glyph
     * @param string[] $allowedContentObjectTypes
     * @param string[] $parameters
     * @param array $extraActions
     * @param string $classes
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton
     */
    public function getPublicationButton(
        $label, $glyph, $allowedContentObjectTypes, $parameters, $extraActions = [], $classes = null
    )
    {
        $actionSelector = new ActionSelector(
            $this, $this->getUser()->getId(), $allowedContentObjectTypes, $parameters, $extraActions, $classes
        );

        return $actionSelector->getActionButton($label, $glyph);
    }

    /**
     *
     * @return string
     */
    public function renderForum()
    {
        $html = [];

        // Render forums published in the root
        $html[] = $this->renderTableForCategory();

        // Render forums published in categories
        $html[] = $this->renderTablesForCategories();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $publication
     * @param Forum $forum
     *
     * @return string
     */
    public function renderLastPost($publication, Forum $forum)
    {
        $lastPost = \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager::retrieve_forum_post(
            $forum->get_last_post()
        );
        $html = [];

        if ($lastPost)
        {
            $link = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_VIEW,
                    self::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                    \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Forum\Display\Manager::ACTION_VIEW_TOPIC,
                    'pid' => $forum->get_id(),
                    \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forum->get_last_topic_changed_cloi(
                    ),
                    \Chamilo\Core\Repository\ContentObject\Forum\Display\Manager::PARAM_LAST_POST => $lastPost->get_id()
                )
            );

            $html[] = DatetimeUtilities::format_locale_date(null, $lastPost->get_creation_date());

            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class, $lastPost->get_user_id()
            );

            $muteClass = $publication[ContentObjectPublication::PROPERTY_HIDDEN] ? ' text-muted-invisible' : '';

            if ($user instanceof User)
            {
                $html[] = '<br />';
                $html[] = '<span class="text-primary' . $muteClass . '">' . $user->get_fullname() . '</span>';
            }

            if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT) || !$forum->get_locked())
            {
                $fileFontAwesomeGlyph = new FontAwesomeGlyph(
                    'file', [], Translation::get('ViewLastPost', null, Forum::package())
                );

                $html[] = '&nbsp;';
                $html[] = '<a class="' . $muteClass . '" href="' . $link . '">';
                $html[] = '<small>';
                $html[] = $fileFontAwesomeGlyph->render();
                $html[] = '</small>';
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
     * @param ContentObjectPublicationCategory $category
     *
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

        $categoryTitle = [];

        $categoryTitle[] = '<h3>';
        $categoryTitle[] = $categoryName;

        if ($category instanceof ContentObjectPublicationCategory && $this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $rightsUrl = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Manager::PARAM_CATEGORY => $category->get_id(),
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Manager::ACTION_EDIT_RIGHTS
                )
            );

            $button = new Button(
                Translation::get('ManageRights', null, Utilities::COMMON_LIBRARIES), new FontAwesomeGlyph('lock'),
                $rightsUrl, Button::DISPLAY_ICON, false, 'btn-link'
            );

            $buttonRenderer = new ButtonRenderer($button);

            $categoryTitle[] = $buttonRenderer->render();
        }

        $categoryTitle[] = '</h3>';

        $header->setHeaderContents(0, 1, implode(' ', $categoryTitle));

        $bootstrapGlyph = new FontAwesomeGlyph('comments', [], Translation::get("Topics", null, Forum::package()));
        $header->setHeaderContents(0, 2, $bootstrapGlyph->render());
        $header->setCellAttributes(0, 2, array('class' => 'cell-stat text-center hidden-xs hidden-sm'));

        $bootstrapGlyph = new FontAwesomeGlyph('comment', [], Translation::get("Posts", null, Forum::package()));
        $header->setHeaderContents(0, 3, $bootstrapGlyph->render());
        $header->setCellAttributes(0, 3, array('class' => 'cell-stat text-center hidden-xs hidden-sm'));

        $header->setHeaderContents(0, 4, Translation::get("LastPostForum", null, Forum::package()));
        $header->setCellAttributes(0, 4, array('class' => 'cell-stat-2x hidden-xs hidden-sm'));
        $header->setHeaderContents(0, 5, '');
        $header->setCellAttributes(0, 5, array('class' => 'cell-stat-2x'));

        $this->renderTableForumsForCategory($table, $category);

        return $table->toHtml();
    }

    /**
     *
     * @param string[] $publication
     * @param Forum $forum
     *
     * @return string
     */
    public function renderTableForumTitle($publication, Forum $forum)
    {
        $canViewForum = !$forum->get_locked() || $this->is_allowed(WeblcmsRights::EDIT_RIGHT);
        $titleParts = [];

        if ($canViewForum)
        {
            $forumUrl = $this->get_url(
                array(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                    \Chamilo\Core\Repository\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Forum\Display\Manager::ACTION_VIEW_FORUM,
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                )
            );

            if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
            {
                $class = ' class="text-muted"';
            }

            $titleParts[] = '<a href="' . $forumUrl . '"' . $class . '>';
        }

        $titleParts[] = $forum->get_title();

        if ($canViewForum)
        {
            $titleParts[] = '</a>';
        }

        $description = $forum->get_description();
        $descriptionString = StringUtilities::getInstance()->createString(strip_tags($description));

        if (!$descriptionString->isBlank())
        {
            $titleParts[] = '<br />';
            $titleParts[] = '<small>';
            $titleParts[] = $descriptionString->safeTruncate(200, '&hellip;');
            $titleParts[] = '</small>';
        }

        $title = implode('', $titleParts);

        if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
        {
            $title = '<span class="text-muted">' . $title . '</span>';
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

        if ($publications->count() > 0)
        {
            $this->size += $publications->count();
            $row = 0;

            foreach ($publications as $publication)
            {
                if (!($publication[ContentObjectPublication::PROPERTY_HIDDEN] &&
                    !$this->is_allowed(WeblcmsRights::EDIT_RIGHT)))
                {
                    $forum = DataManager::retrieve_by_id(
                        Forum::class, $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
                    );

                    $forumGlyphType = $forum->get_locked() ? 'lock' : 'file';

                    $classes = array('text-muted');
                    $muteClass = '';

                    if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
                    {
                        $muteClass = ' text-muted-invisible';
                    }

                    $forumGlyph = new FontAwesomeGlyph($forumGlyphType, $classes);

                    $table->setCellContents($row, 0, $forumGlyph->render());
                    $table->setCellAttributes($row, 0, array('class' => 'text-center forum-row-icon'));
                    $table->setCellContents($row, 1, $this->renderTableForumTitle($publication, $forum));
                    $table->setCellContents($row, 2, $forum->get_total_topics());
                    $table->setCellAttributes(
                        $row, 2, array('class' => 'text-primary text-center hidden-xs hidden-sm' . $muteClass)
                    );
                    $table->setCellContents($row, 3, $forum->get_total_posts());
                    $table->setCellAttributes(
                        $row, 3, array('class' => 'text-primary text-center hidden-xs hidden-sm' . $muteClass)
                    );
                    $table->setCellContents($row, 4, $this->renderLastPost($publication, $forum));
                    $table->setCellAttributes($row, 4, array('class' => 'hidden-xs hidden-sm' . $muteClass));

                    $table->setCellContents(
                        $row, 5, $this->getForumActions(
                        $publication, $publications->isCurrentEntryFirst(), $publications->isCurrentEntryLast()
                    )
                    );
                    $table->setCellAttributes($row, 5, array('class' => 'text-center'));

                    $row ++;
                }
            }
        }
        else
        {
            $table->setCellContents(0, 1, Translation::get('NoPublications'));
        }
    }

    /**
     *
     * @return string
     */
    public function renderTablesForCategories()
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_COURSE
            ), new StaticConditionVariable($this->get_parent()->get_course_id())
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_TOOL
            ), new StaticConditionVariable($this->get_parent()->get_tool_id())
        );

        $order = array(
            new OrderProperty(
                new PropertyConditionVariable(
                    ContentObjectPublicationCategory::class, ContentObjectPublicationCategory::PROPERTY_DISPLAY_ORDER
                )
            )
        );

        $categories = WeblcmsDataManager::retrieves(
            ContentObjectPublicationCategory::class,
            new DataClassRetrievesParameters(new AndCondition($conditions), null, null, $order)
        );

        $html = [];

        foreach ($categories as $category)
        {
            $html[] = $this->renderTableForCategory($category);
        }

        return implode(PHP_EOL, $html);
    }
}
