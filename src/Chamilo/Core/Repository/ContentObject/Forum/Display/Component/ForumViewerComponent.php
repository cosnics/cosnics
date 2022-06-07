<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Forum\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\ComplexForum;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ComplexForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumPost;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use HTML_Table;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Forum\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ForumViewerComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum
     */
    protected $forum;

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
     * @var \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\ComplexForum[]
     */
    private $subforums;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ComplexForumTopic[]
     */
    private $topics;

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::run()
     */
    public function run()
    {
        $this->setBreadcrumbs();

        if (!$this->getForum() instanceof Forum)
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->render_header();

        $html[] = $this->renderDescription();

        if (!$this->isLocked())
        {
            $html[] = $this->getButtonToolbarRenderer()->render();
        }

        $html[] = $this->renderTopics();
        $html[] = $this->renderSubforums();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();

            $publishParameters = $this->get_parameters();
            $publishParameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] =
                $this->get_complex_content_object_item_id();
            $publishParameters[self::PARAM_ACTION] = self::ACTION_CREATE_TOPIC;

            $commonActions->addButton(
                $this->getPublicationButton(
                    Translation::get('NewTopic'), new FontAwesomeGlyph('plus'), array(ForumTopic::class),
                    $publishParameters, [], 'btn-primary'
                )
            );

            if ($this->get_user()->is_platform_admin() || $this->get_user_id() == $this->forum->get_owner_id() ||
                $this->isForumManager($this->get_user()))
            {
                $publishParameters = $this->get_parameters();
                $publishParameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] =
                    $this->get_complex_content_object_item_id();
                $publishParameters[self::PARAM_ACTION] = self::ACTION_CREATE_SUBFORUM;

                $commonActions->addButton(
                    $this->getPublicationButton(
                        Translation::get('NewSubForum'), new FontAwesomeGlyph('plus'), array(Forum::class),
                        $publishParameters
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
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
     * @return \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\ComplexForum[]
     */
    public function getSubforums()
    {
        if (!isset($this->subforums))
        {
            $this->prepareTopicsAndSubforums();
        }

        return $this->subforums;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ComplexForumTopic[]
     */
    public function getTopics()
    {
        if (!isset($this->topics))
        {
            $this->prepareTopicsAndSubforums();
        }

        return $this->topics;
    }

    /**
     *
     * @return boolean
     */
    public function isLocked()
    {
        if (!isset($this->isLocked))
        {
            $this->isLocked = $this->getForum()->is_locked();
        }

        return $this->isLocked;
    }

    private function prepareTopicsAndSubforums()
    {
        $orderBy = OrderBy::generate(ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_ADD_DATE);

        $parameters = new DataClassRetrievesParameters(
            new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
                ), new StaticConditionVariable($this->getForum()->get_id())
            ), null, null, $orderBy
        );

        $children = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexContentObjectItem::class, $parameters
        );

        $this->topics = [];
        $this->subforums = [];

        foreach ($children as $child)
        {
            $contentObject = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class, $child->get_ref()
            );
            $child->set_ref($contentObject);

            if ($contentObject->getType() == ForumTopic::class)
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

    /**
     *
     * @param ComplexForumTopic $topic
     *
     * @return string
     */
    public function renderAuthor(ComplexForumTopic $topic)
    {
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class, (int) $topic->get_user_id());
        $name = "";

        if (!$user)
        {
            return Translation::get('UserNotFound');
        }
        else
        {
            return $user->get_fullname();
        }
    }

    public function renderDescription()
    {
        $forum = $this->getForum();
        $html = [];

        if ($forum->has_description())
        {
            $rendition_implementation = ContentObjectRenditionImplementation::factory(
                $forum, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
            );

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-body">';
            $html[] = $rendition_implementation->render();

            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param ForumPost $lastPost
     * @param boolean $isViewable
     * @param string $viewUrl
     *
     * @return string
     */
    public function renderLastPost(ForumPost $lastPost = null, $isViewable, $viewUrl)
    {
        if ($lastPost instanceof ForumPost)
        {
            $html[] = DatetimeUtilities::getInstance()->formatLocaleDate(null, $lastPost->get_creation_date());

            $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class, $lastPost->get_user_id()
            );

            if ($user instanceof User)
            {
                $html[] = '<br />';
                $html[] = '<span class="text-primary">' . $user->get_fullname() . '</span>';
            }

            if ($isViewable)
            {
                $fileFontAwesomeGlyph = new FontAwesomeGlyph(
                    'file', [], Translation::get('ViewLastPost', null, Forum::package())
                );

                $html[] = '&nbsp;';
                $html[] = '<a href="' . $viewUrl . '">';
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
     * @param ComplexForum $forum
     *
     * @return string
     */
    public function renderSubforumActions(ComplexForum $forum)
    {
        $buttonToolBar = new ButtonToolBar();

        if (!$this->isLocked())
        {
            $hasEditRights =
                $this->get_user()->get_id() == $forum->get_user_id() || $this->get_user()->is_platform_admin() ||
                $this->isForumManager($this->get_user());

            if ($hasEditRights)
            {
                if (!$this->get_complex_content_object_item())
                {
                    $parent_cloi = null;
                }
                else
                {
                    $parent_cloi = Request::get('cloi');
                }

                $parameters = [];
                $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $parameters[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
                $parameters[self::PARAM_ACTION] = self::ACTION_EDIT_SUBFORUM;

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('Edit', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                        $this->get_url($parameters), Button::DISPLAY_ICON, false, 'btn-link'
                    )
                );

                if ($this->get_user()->is_platform_admin() || $this->isForumManager($this->get_user()))
                {
                    if (!$this->isLocked())
                    {
                        if ($forum->get_ref()->get_locked())
                        {
                            $parameters = [];
                            $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] =
                                $this->get_complex_content_object_item_id();
                            $parameters[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
                            $parameters[self::PARAM_ACTION] = self::ACTION_CHANGE_LOCK;

                            $buttonToolBar->addItem(
                                new Button(
                                    Translation::get('Unlock'), new FontAwesomeGlyph('unlock'),
                                    $this->get_url($parameters), Button::DISPLAY_ICON, false, 'btn-link'
                                )
                            );
                        }
                        else
                        {
                            $parameters = [];
                            $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] =
                                $this->get_complex_content_object_item_id();
                            $parameters[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
                            $parameters[self::PARAM_ACTION] = self::ACTION_CHANGE_LOCK;

                            $buttonToolBar->addItem(
                                new Button(
                                    Translation::get('Lock'), new FontAwesomeGlyph('lock'), $this->get_url($parameters),
                                    Button::DISPLAY_ICON, false, 'btn-link'
                                )
                            );
                        }
                    }
                }
            }

            $subscribed = \Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager::retrieve_subscribe(
                $forum->get_ref()->get_id(), $this->get_user_id()
            );

            if (!$subscribed)
            {
                $parameters = [];
                $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $parameters[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
                $parameters[self::PARAM_ACTION] = self::ACTION_FORUM_SUBSCRIBE;

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('Subscribe'), new FontAwesomeGlyph('envelope'), $this->get_url($parameters),
                        Button::DISPLAY_ICON, true, 'btn-link'
                    )
                );
            }
            else
            {
                $parameters = [];
                $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $parameters[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
                $parameters[self::PARAM_ACTION] = self::ACTION_FORUM_UNSUBSCRIBE;
                $parameters[self::PARAM_SUBSCRIBE_ID] = $subscribed->get_id();

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('UnSubscribe'), new FontAwesomeGlyph('envelope', [], null, 'far'),
                        $this->get_url($parameters), Button::DISPLAY_ICON, true, 'btn-link'
                    )
                );
            }

            if ($hasEditRights)
            {
                if (!$this->get_complex_content_object_item())
                {
                    $parent_cloi = null;
                }
                else
                {
                    $parent_cloi = Request::get('cloi');
                }

                $parameters = [];
                $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $parameters[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $forum->get_id();
                $parameters[self::PARAM_ACTION] = self::ACTION_DELETE_SUBFORUM;
                $parameters[self::PARAM_CURRENT_SESSION_PARENT_CLOI] = $parent_cloi;

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->get_url($parameters), Button::DISPLAY_ICON, true, 'btn-link'
                    )
                );
            }
        }

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    /**
     *
     * @param ComplexForum $subforum
     *
     * @return string
     */
    public function renderSubforumGlyph(ComplexForum $subforum)
    {
        $isLocked = $this->isLocked() || $subforum->get_ref()->get_locked();

        $subforumGlyphType = $isLocked ? 'lock' : 'folder-open';
        $subforumGlyph = new FontAwesomeGlyph($subforumGlyphType, array('text-muted'), Translation::get('NoNewPosts'));

        return $subforumGlyph->render();
    }

    /**
     *
     * @param ComplexForum $subforum
     *
     * @return string
     */
    public function renderSubforumLastPost(ComplexForum $subforum)
    {
        $lastPost = DataManager::retrieve_by_id(ForumPost::class, $subforum->get_ref()->get_last_post());

        $isLocked = ($subforum->get_ref()->is_locked() &&
            (!$this->isForumManager($this->get_user()) || !$this->get_user()->is_platform_admin() ||
                !($this->get_user_id() == $subforum->get_ref()->get_owner_id())));

        if ($lastPost instanceof ForumPost)
        {
            $viewUrl = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_VIEW_TOPIC,
                    'pid' => $subforum->get_ref(),
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $subforum->get_ref()->get_last_topic_changed_cloi(),
                    self::PARAM_LAST_POST => $lastPost->get_id()
                )
            );
        }
        else
        {
            $lastPost = null;
            $viewUrl = null;
        }

        return $this->renderLastPost($lastPost, !$isLocked, $viewUrl);
    }

    /**
     *
     * @param ComplexForum $subforum
     *
     * @return string
     */
    public function renderSubforumTitle(ComplexForum $subforum)
    {
        $isNotAllowedToView = ($subforum->get_ref()->is_locked() && (!$this->get_user()->is_platform_admin() ||
                !($this->get_user_id() == $subforum->get_ref()->get_owner_id()) || !$this->isForumManager(
                    $this->get_user()
                )));

        $title = $subforum->get_ref()->get_title();

        if (!$isNotAllowedToView)
        {
            $viewUrl = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $subforum->get_id()
                )
            );

            $titleParts = [];

            $titleParts[] = '<a href="' . $viewUrl . '">';
            $titleParts[] = $title;
            $titleParts[] = '</a>';

            $description = $subforum->get_ref()->get_description();
            $descriptionString = StringUtilities::getInstance()->createString(strip_tags($description));

            if (!$descriptionString->isBlank())
            {
                $titleParts[] = '<br />';
                $titleParts[] = '<small>';
                $titleParts[] = $descriptionString->safeTruncate(200, '&hellip;');
                $titleParts[] = '</small>';
            }

            $title = implode('', $titleParts);
        }

        $title = '<h4>' . $title . '</h4>';

        return $title;
    }

    /**
     *
     * @return string
     */
    public function renderSubforums()
    {
        $subforums = $this->getSubforums();

        if (count($subforums) > 0)
        {
            $table = new HTML_Table(array('class' => 'table forum table-striped'));

            $header = $table->getHeader();

            $header->setHeaderContents(0, 0, '');
            $header->setCellAttributes(0, 0, array('class' => 'cell-stat'));
            $header->setHeaderContents(0, 1, '<h3>' . Translation::get('Subforums') . '</h3>');

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

            $row = 0;

            foreach ($subforums as $subforum)
            {
                $table->setCellContents($row, 0, $this->renderSubforumGlyph($subforum));
                $table->setCellAttributes($row, 0, array('class' => 'text-center forum-row-icon'));
                $table->setCellContents($row, 1, $this->renderSubforumTitle($subforum));
                $table->setCellContents($row, 2, $subforum->get_ref()->get_total_topics());
                $table->setCellAttributes($row, 2, array('class' => 'text-primary text-center hidden-xs hidden-sm'));
                $table->setCellContents($row, 3, $subforum->get_ref()->get_total_posts());
                $table->setCellAttributes($row, 3, array('class' => 'text-primary text-center hidden-xs hidden-sm'));
                $table->setCellContents($row, 4, $this->renderSubforumLastPost($subforum));
                $table->setCellAttributes($row, 4, array('class' => 'hidden-xs hidden-sm'));
                $table->setCellContents($row, 5, $this->renderSubforumActions($subforum));
                $table->setCellAttributes($row, 5, array('class' => 'text-center'));

                $row ++;
            }

            return $table->toHtml();
        }
    }

    /**
     *
     * @param ComplexForumTopic $topic
     *
     * @return string
     */
    public function renderTopicActions(ComplexForumTopic $topic)
    {
        $buttonToolBar = new ButtonToolBar();

        $parameters = [];

        $parameters[self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $parameters[self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $topic->get_id();

        if (!$this->isLocked())
        {
            if ($this->get_user()->get_id() == $topic->get_user_id() || $this->get_user()->is_platform_admin() ||
                $this->isForumManager(
                    $this->get_user()
                ))
            {
                $parameters[self::PARAM_ACTION] = self::ACTION_DELETE_TOPIC;

                if (!$this->get_complex_content_object_item())
                {
                    $parent_cloi = null;
                }
                else
                {
                    $parent_cloi = Request::get('cloi');
                }

                $parameters[self::PARAM_CURRENT_SESSION_PARENT_CLOI] = $parent_cloi;

                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                        $this->get_url($parameters), Button::DISPLAY_ICON, true, 'btn-link'
                    )
                );

                $parameters[self::PARAM_CURRENT_SESSION_PARENT_CLOI] = null;

                if ($topic->get_forum_type() == 1 && $this->get_user()->is_platform_admin())
                {
                    $parameters[self::PARAM_ACTION] = self::ACTION_MAKE_STICKY;

                    $buttonToolBar->addItem(
                        new Button(
                            Translation::get('UnSticky'), new FontAwesomeGlyph('star'), $this->get_url($parameters),
                            Button::DISPLAY_ICON, false, 'btn-link'
                        )
                    );
                }
                else
                {
                    if ($topic->get_forum_type() == 2 &&
                        ($this->get_user()->is_platform_admin() || $this->isForumManager(
                                $this->get_user()
                            )))
                    {
                        $parameters[self::PARAM_ACTION] = self::ACTION_MAKE_IMPORTANT;

                        $buttonToolBar->addItem(
                            new Button(
                                Translation::get('UnImportant'), new FontAwesomeGlyph('exclamation-circle'),
                                $this->get_url($parameters), Button::DISPLAY_ICON, false, 'btn-link'
                            )
                        );
                    }
                    else
                    {
                        if ($this->get_user()->is_platform_admin() || $this->isForumManager($this->get_user()))
                        {
                            $parameters[self::PARAM_ACTION] = self::ACTION_MAKE_STICKY;
                            $buttonToolBar->addItem(
                                new Button(
                                    Translation::get('MakeSticky'), new FontAwesomeGlyph('star', [], null, 'far'),
                                    $this->get_url($parameters), Button::DISPLAY_ICON, false, 'btn-link'
                                )
                            );

                            $parameters[self::PARAM_ACTION] = self::ACTION_MAKE_IMPORTANT;
                            $buttonToolBar->addItem(
                                new Button(
                                    Translation::get('MakeImportant'), new FontAwesomeGlyph('circle', [], null, 'far'),
                                    $this->get_url($parameters), Button::DISPLAY_ICON, false, 'btn-link'
                                )
                            );
                        }
                    }
                }
                if ($this->get_user()->is_platform_admin() || $this->isForumManager($this->get_user()))
                {
                    if (!$this->isLocked())
                    {
                        if ($topic->get_ref()->get_locked())
                        {
                            $parameters[self::PARAM_ACTION] = self::ACTION_CHANGE_LOCK;
                            $buttonToolBar->addItem(
                                new Button(
                                    Translation::get('Unlock'), new FontAwesomeGlyph('unlock'),
                                    $this->get_url($parameters), Button::DISPLAY_ICON, false, 'btn-link'
                                )
                            );
                        }
                        else
                        {
                            $parameters[self::PARAM_ACTION] = self::ACTION_CHANGE_LOCK;
                            $buttonToolBar->addItem(
                                new Button(
                                    Translation::get('Lock'), new FontAwesomeGlyph('lock'), $this->get_url($parameters),
                                    Button::DISPLAY_ICON, false, 'btn-link'
                                )
                            );
                        }
                    }
                }
            }

            $subscribed = DataManager::retrieve_subscribe($topic->get_ref()->get_id(), $this->get_user_id());

            if (!$subscribed)
            {
                $parameters[self::PARAM_ACTION] = self::ACTION_TOPIC_SUBSCRIBE;
                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('Subscribe', null, ForumTopic::package()), new FontAwesomeGlyph('envelope'),
                        $this->get_url($parameters), Button::DISPLAY_ICON, true, 'btn-link'
                    )
                );
            }
            else
            {
                $parameters[self::PARAM_ACTION] = self::ACTION_TOPIC_UNSUBSCRIBE;
                $parameters[self::PARAM_SUBSCRIBE_ID] = $topic->get_ref()->get_id();
                $buttonToolBar->addItem(
                    new Button(
                        Translation::get('UnSubscribe', null, ForumTopic::package()),
                        new FontAwesomeGlyph('envelope', [], null, 'far'), $this->get_url($parameters),
                        Button::DISPLAY_ICON, true, 'btn-link'
                    )
                );
            }
        }

        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolBarRenderer->render();
    }

    /**
     *
     * @param ComplexForumTopic $topic
     *
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function renderTopicGlyph(ComplexForumTopic $topic)
    {
        $forumGlyph = new FontAwesomeGlyph('file', array('text-muted'), Translation::get('NoNewPosts'));

        switch ($topic->get_forum_type())
        {
            case 1 :
                $forumGlyph = new FontAwesomeGlyph('star', array('text-danger'), Translation::get('Sticky'));
                break;
            case 2 :
                $forumGlyph = new FontAwesomeGlyph(
                    'exclamation-circle', array('text-danger'), Translation::get('Important')
                );
                break;
        }

        if ($this->isLocked() || $topic->get_ref()->get_locked())
        {
            $forumGlyph = new FontAwesomeGlyph('lock', [], Translation::get('Locked'));
        }

        return $forumGlyph->render();
    }

    /**
     *
     * @param ComplexForumTopic $topic
     *
     * @return string
     */
    public function renderTopicLastPost(ComplexForumTopic $topic)
    {
        $lastPost = DataManager::retrieve_by_id(ForumPost::class, $topic->get_ref()->get_last_post());

        $isLocked = $topic->get_ref()->is_locked() &&
            (!$this->get_user()->is_platform_admin() || !($this->get_user_id() == $topic->get_ref()->get_owner_id()));

        if ($lastPost instanceof ForumPost)
        {
            $viewUrl = $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_VIEW_TOPIC,
                    'pid' => $this->pid,
                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $topic->get_id(),
                    self::PARAM_LAST_POST => $lastPost->get_id()
                )
            );
        }
        else
        {
            $lastPost = null;
            $viewUrl = null;
        }

        return $this->renderLastPost($lastPost, !$isLocked, $viewUrl);
    }

    /**
     *
     * @return string
     */
    public function renderTopics()
    {
        $table = new HTML_Table(array('class' => 'table forum table-striped'));

        $header = $table->getHeader();

        $header->setHeaderContents(0, 0, '');
        $header->setCellAttributes(0, 0, array('class' => 'cell-stat'));
        $header->setHeaderContents(0, 1, '<h3>' . Translation::get('Topics') . '</h3>');

        $bootstrapGlyph = new FontAwesomeGlyph('user', [], Translation::get("Author", null, Forum::package()));
        $header->setHeaderContents(0, 2, $bootstrapGlyph->render());
        $header->setCellAttributes(0, 2, array('class' => 'cell-stat-2x text-center'));

        $bootstrapGlyph = new FontAwesomeGlyph('comment', [], Translation::get("Replies", null, Forum::package()));
        $header->setHeaderContents(0, 3, $bootstrapGlyph->render());
        $header->setCellAttributes(0, 3, array('class' => 'cell-stat text-center'));

        $bootstrapGlyph = new FontAwesomeGlyph('eye', [], Translation::get("Views", null, Forum::package()));
        $header->setHeaderContents(0, 4, $bootstrapGlyph->render());

        $header->setCellAttributes(0, 4, array('class' => 'cell-stat text-center hidden-xs hidden-sm'));
        $header->setHeaderContents(0, 5, Translation::get("LastPostForum", null, Forum::package()));
        $header->setCellAttributes(0, 5, array('class' => 'cell-stat-2x hidden-xs hidden-sm'));
        $header->setHeaderContents(0, 6, '');
        $header->setCellAttributes(0, 6, array('class' => 'cell-stat-2x'));

        $row = 0;

        if (count($this->getTopics()) > 0)
        {
            foreach ($this->getTopics() as $topic)
            {
                $title = '<h4><a href="' . $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_VIEW_TOPIC,
                            self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $topic->get_id()
                        )
                    ) . '">' . $topic->get_ref()->get_title() . '</a></h4>';

                $count = $topic->get_ref()->get_total_posts();

                $table->setCellContents($row, 0, $this->renderTopicGlyph($topic));
                $table->setCellAttributes($row, 0, array('class' => 'text-center'));
                $table->setCellContents($row, 1, $title);

                $table->setCellContents($row, 2, $this->renderAuthor($topic));
                $table->setCellAttributes($row, 2, array('class' => 'text-primary text-center'));
                $table->setCellContents($row, 3, ($count > 0) ? $count - 1 : $count);
                $table->setCellAttributes($row, 3, array('class' => 'text-primary text-center'));
                $table->setCellContents($row, 4, $this->forum_count_topic_views($topic->get_id()));
                $table->setCellAttributes($row, 4, array('class' => 'text-primary text-center hidden-xs hidden-sm'));
                $table->setCellContents($row, 5, $this->renderTopicLastPost($topic));
                $table->setCellAttributes($row, 5, array('class' => 'hidden-xs hidden-sm'));
                $table->setCellContents($row, 6, $this->renderTopicActions($topic));
                $table->setCellAttributes($row, 6, array('class' => 'text-center'));

                $row ++;
            }
        }
        else
        {
            $table->setCellContents($row, 1, Translation::get('NoTopics'));
        }

        return $table->toHtml();
    }

    public function setBreadcrumbs()
    {
        $trail = BreadcrumbTrail::getInstance();

        $trail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                        self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => null
                    )
                ), $this->get_root_content_object()->get_title()
            )
        );

        if ($this->get_complex_content_object_item())
        {
            $forums_with_key_cloi = [];
            $forums_with_key_cloi = $this->retrieve_children_from_root_to_cloi(
                $this->get_root_content_object()->get_id(), $this->get_complex_content_object_item()->get_id()
            );

            if ($forums_with_key_cloi)
            {
                foreach ($forums_with_key_cloi as $key => $value)
                {

                    $trail->add(
                        new Breadcrumb(
                            $this->get_url(
                                array(
                                    self::PARAM_ACTION => self::ACTION_VIEW_FORUM,
                                    self::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $key
                                )
                            ), $value->get_title()
                        )
                    );
                }
            }
            else
            {
                throw new Exception('The forum you requested has not been found');
            }
        }
    }

    /**
     *
     * @param ForumTopic[] $topics
     *
     * @return ForumTopic[]
     */
    private function sortTopics($topics)
    {
        $sorted_array = [];

        foreach ($topics as $key => $value)
        {
            $type = ($value->get_forum_type()) ? $value->get_forum_type() : 100;
            $sorted_array[$type][] = $value;
        }

        ksort($sorted_array);

        $array = [];

        foreach ($sorted_array as $key => $value)
        {
            foreach ($value as $key2 => $value2)
            {
                $array[] = $value2;
            }
        }

        return $array;
    }
}
