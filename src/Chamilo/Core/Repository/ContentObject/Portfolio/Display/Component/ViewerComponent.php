<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioBookmarkSupport;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\PortfolioComplexRights;
use Chamilo\Core\Repository\ContentObject\Portfolio\Storage\DataClass\Portfolio;
use Chamilo\Core\Repository\Feedback\FeedbackNotificationSupport;
use Chamilo\Core\Repository\Feedback\FeedbackSupport;
use Chamilo\Core\Repository\Feedback\Generator\ActionsGenerator;
use Chamilo\Core\Repository\Feedback\Manager;
use Chamilo\Core\Repository\Feedback\Storage\DataClass\Notification;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Viewer\ActionSelector;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\OrderProperty;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Default viewer component that handles the visualization of the portfolio item or folder
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends ItemComponent implements FeedbackSupport, FeedbackNotificationSupport
{

    private $buttonToolBar;

    /**
     * Executes this component
     */
    public function build()
    {
        if (!$this->get_parent()->is_allowed_to_view_content_object($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        $content = [];
        $content[] = ContentObjectRenditionImplementation::launch(
            $this->get_current_content_object(), ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_FULL,
            $this
        );

        if ($this->get_current_node()->is_root())
        {
            $content[] = $this->render_last_actions();
            $content[] = '<a name="view_feedback"></a>';
            $content[] = $this->renderFeedback();
        }
        else
        {
            $content[] = $this->renderFeedback();
            $content[] = '<a name="view_feedback"></a>';
            $content[] = $this->render_statistics($this->get_current_content_object());
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = implode(PHP_EOL, $content);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        return $this->get_parent()->count_portfolio_feedbacks($this->get_current_node());
    }

    /**
     * Formats the given date in a human-readable format.
     *
     * @param $date int A UNIX timestamp.
     *
     * @return string The formatted date.
     */
    public function format_date($date)
    {
        $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

        return DatetimeUtilities::format_locale_date($date_format, $date);
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::getAdditionalParameters()
     */
    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_STEP;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function getButtonToolBar()
    {
        if (!isset($this->buttonToolBar))
        {
            $this->buttonToolBar = new ButtonToolBar();

            $contentItems = new ButtonGroup();
            $this->buttonToolBar->addItem($contentItems);

            if ($this->canEditComplexContentObjectPathNode($this->get_current_node()))
            {
                if ($this->get_current_node()->get_content_object() instanceof Portfolio)
                {

                    $parameters = $this->get_parameters();
                    $parameters[self::PARAM_ACTION] = self::ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM;
                    $parameters[self::PARAM_STEP] = $this->get_current_step();

                    $contentItems->addButton(
                        $this->getPublicationButton(
                            Translation::get('CreatorComponent'), new FontAwesomeGlyph('plus'),
                            $this->get_root_content_object()->get_allowed_types(), $parameters, [], 'btn-primary'
                        )
                    );
                }
            }

            $canEditNodeOrContentObject = $this->canEditComplexContentObjectPathNode($this->get_current_node());

            $canMoveNode = !$this->get_current_node()->is_root() &&
                $this->canEditComplexContentObjectPathNode($this->get_current_node()->get_parent());

            if ($canEditNodeOrContentObject || $canMoveNode)
            {
                if ($canEditNodeOrContentObject && $canMoveNode)
                {
                    if ($this->get_current_node()->is_root())
                    {
                        $editTitle = Translation::get('ChangeIntroduction');
                        $editImage = new FontAwesomeGlyph('compass');
                    }
                    else
                    {
                        $variable = $this->get_current_content_object() instanceof Portfolio ? 'UpdateFolder' :
                            'UpdaterComponent';

                        $editTitle = Translation::get($variable);
                        $editImage = new FontAwesomeGlyph('pencil-alt');
                    }

                    $editButton = new SplitDropdownButton(
                        $editTitle, $editImage, $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self::PARAM_STEP => $this->get_current_step()
                        )
                    )
                    );

                    $variable =
                        $this->get_current_content_object() instanceof Portfolio ? 'MoveFolder' : 'MoverComponent';

                    if ($this->get_current_node()->has_siblings())
                    {
                        $editButton->addSubButton(
                            new SubButton(
                                Translation::get('MoveToOtherFolder'), new FontAwesomeGlyph('folder-open'),
                                $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_MOVE,
                                        self::PARAM_STEP => $this->get_current_step()
                                    )
                                )
                            )
                        );

                        if (!$this->get_current_node()->is_first_child())
                        {
                            $editButton->addSubButton(
                                new SubButton(
                                    Translation::get('MoveUp'), new FontAwesomeGlyph('chevron-up'), $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_SORT,
                                        self::PARAM_SORT => self::SORT_UP,
                                        self::PARAM_STEP => $this->get_current_step()
                                    )
                                )
                                )
                            );
                        }

                        if (!$this->get_current_node()->is_last_child())
                        {
                            $editButton->addSubButton(
                                new SubButton(
                                    Translation::get('MoveDown'), new FontAwesomeGlyph('chevron-down'), $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_SORT,
                                        self::PARAM_SORT => self::SORT_DOWN,
                                        self::PARAM_STEP => $this->get_current_step()
                                    )
                                )
                                )
                            );
                        }
                    }
                    else
                    {
                        $editButton->addSubButton(
                            new SubButton(
                                Translation::get('MoveToOtherFolder'), new FontAwesomeGlyph('folder-open'),
                                $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_MOVE,
                                        self::PARAM_STEP => $this->get_current_step()
                                    )
                                )
                            )
                        );
                    }

                    $contentItems->addButton($editButton);
                }
                elseif (!$canEditNodeOrContentObject && $canMoveNode)
                {
                    $variable =
                        $this->get_current_content_object() instanceof Portfolio ? 'MoveFolder' : 'MoverComponent';

                    if ($this->get_current_node()->has_siblings())
                    {
                        $moveButton = new DropdownButton(Translation::get($variable), new FontAwesomeGlyph('sort'));

                        $moveButton->addSubButton(
                            new SubButton(
                                Translation::get('MoveToOtherFolder'), new FontAwesomeGlyph('folder-open'),
                                $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_MOVE,
                                        self::PARAM_STEP => $this->get_current_step()
                                    )
                                )
                            )
                        );

                        if (!$this->get_current_node()->is_first_child())
                        {
                            $moveButton->addSubButton(
                                new SubButton(
                                    Translation::get('MoveUp'), new FontAwesomeGlyph('chevron-up'), $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_SORT,
                                        self::PARAM_SORT => self::SORT_UP,
                                        self::PARAM_STEP => $this->get_current_step()
                                    )
                                )
                                )
                            );
                        }

                        if (!$this->get_current_node()->is_last_child())
                        {
                            $moveButton->addSubButton(
                                new SubButton(
                                    Translation::get('MoveDown'), new FontAwesomeGlyph('chevron-down'), $this->get_url(
                                    array(
                                        self::PARAM_ACTION => self::ACTION_SORT,
                                        self::PARAM_SORT => self::SORT_DOWN,
                                        self::PARAM_STEP => $this->get_current_step()
                                    )
                                )
                                )
                            );
                        }

                        $contentItems->addButton($moveButton);
                    }
                    else
                    {
                        $contentItems->addButton(
                            new Button(
                                Translation::get($variable), new FontAwesomeGlyph(
                                'window-restore', array('fa-lg', 'fa-flip-horizontal'), null, 'fas'
                            ), $this->get_url(
                                array(
                                    self::PARAM_ACTION => self::ACTION_MOVE,
                                    self::PARAM_STEP => $this->get_current_step()
                                )
                            )
                            )
                        );
                    }
                }
                else
                {
                    if ($this->get_current_node()->is_root())
                    {
                        $editTitle = Translation::get('ChangeIntroduction');
                        $editImage = new FontAwesomeGlyph('compass');
                    }
                    else
                    {
                        $variable = $this->get_current_content_object() instanceof Portfolio ? 'UpdateFolder' :
                            'UpdaterComponent';

                        $editTitle = Translation::get($variable);
                        $editImage = new FontAwesomeGlyph('pencil-alt');
                    }

                    $contentItems->addButton(
                        new Button(
                            $editTitle, $editImage, $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                                self::PARAM_STEP => $this->get_current_step()
                            )
                        ), Button::DISPLAY_ICON_AND_LABEL
                        )
                    );
                }
            }

            if ($this->get_application()->is_allowed_to_view_feedback($this->get_current_node()))
            {
                $this->buttonToolBar->addItem(
                    new Button(Translation::get('ViewFeedback'), new FontAwesomeGlyph('inbox'), '#view_feedback')
                );
            }

            if (!$this->get_current_node()->is_root() &&
                $this->canEditComplexContentObjectPathNode($this->get_current_node()->get_parent()))
            {
                $variable =
                    $this->get_current_content_object() instanceof Portfolio ? 'DeleteFolder' : 'DeleterComponent';

                $contentItems->addButton(
                    new Button(
                        Translation::get($variable), new FontAwesomeGlyph('times'), $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self::PARAM_STEP => $this->get_current_step()
                        )
                    ), Button::DISPLAY_ICON_AND_LABEL, true
                    )
                );
            }

            if ($this->get_parent() instanceof PortfolioComplexRights &&
                $this->get_parent()->is_allowed_to_set_content_object_rights())
            {
                if (!$this->get_parent()->get_portfolio_virtual_user() instanceof User)
                {
                    $variable =
                        $this->get_current_content_object() instanceof Portfolio ? 'RightsFolder' : 'RightsComponent';

                    $rightsButton = new SplitDropdownButton(
                        Translation::get($variable), new FontAwesomeGlyph('lock'), $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_RIGHTS,
                            self::PARAM_STEP => $this->get_current_step()
                        )
                    )
                    );

                    $rightsButton->addSubButton(
                        new SubButton(
                            Translation::get('UserComponent'), new FontAwesomeGlyph('user'), $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_USER,
                                self::PARAM_STEP => $this->get_current_step()
                            )
                        )
                        )
                    );
                }
                else
                {

                    $rightsButton = new Button(
                        Translation::get('ReturnToYourPortfolio'),
                        new FontAwesomeGlyph('user', array('fa-lg'), null, 'fas'), $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_USER,
                            self::PARAM_STEP => $this->get_current_step()
                        )
                    )
                    );
                }

                $this->buttonToolBar->addItem($rightsButton);
            }

            $this->buttonToolBar->addItem(new ButtonGroup($this->get_parent()->get_portfolio_additional_actions()));
            $this->buttonToolBar->addItem($this->getExtraButton());
        }

        return $this->buttonToolBar;
    }

    public function getExtraButton()
    {
        $extraButton = new DropdownButton(Translation::get('Extra'), new FontAwesomeGlyph('cog'));

        $extraButton->addSubButton(
            new SubButton(
                Translation::get('ActivityComponent'), new FontAwesomeGlyph('mouse-pointer'), $this->get_url(
                array(self::PARAM_ACTION => self::ACTION_ACTIVITY, self::PARAM_STEP => $this->get_current_step())
            )
            )
        );

        $areBookmarksAllowed =
            $this->get_parent() instanceof PortfolioBookmarkSupport && !$this->get_parent()->is_own_portfolio();

        if ($areBookmarksAllowed)
        {
            $extraButton->addSubButton(
                new SubButton(
                    Translation::get('BookmarkerComponent'),
                    new FontAwesomeGlyph('bookmark', array('fa-lg'), null, 'fas'), $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_BOOKMARK,
                        self::PARAM_STEP => $this->get_current_step()
                    )
                )
                )
            );
        }

        $isManagingAllowed = $this->canEditComplexContentObjectPathNode($this->get_current_node());

        if ($isManagingAllowed)
        {
            if ($this->get_current_content_object() instanceof Portfolio &&
                count($this->get_current_node()->get_children()) > 1)
            {
                $extraButton->addSubButton(
                    new SubButton(
                        Translation::get('ManagerComponent'), new FontAwesomeGlyph('cog'), $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_MANAGE,
                            self::PARAM_STEP => $this->get_current_step()
                        )
                    )
                    )
                );
            }
        }

        return $extraButton;
    }

    protected function getFeedbackButtonToolbar()
    {
        $buttonToolbar = new ButtonToolBar();

        $isAllowedToViewFeedback = $this->get_parent()->is_allowed_to_view_feedback($this->get_current_node());
        $isAllowedToCreateFeedback = $this->get_parent()->is_allowed_to_create_feedback($this->get_current_node());

        if ($isAllowedToViewFeedback || $isAllowedToCreateFeedback)
        {
            $baseParameters = array(
                self::PARAM_ACTION => self::ACTION_FEEDBACK,
                self::PARAM_STEP => $this->get_current_step()
            );

            if ($isAllowedToViewFeedback)
            {
                $feedbackCount = $this->get_parent()->count_portfolio_feedbacks($this->get_current_node());
                $portfolioNotification =
                    $this->get_parent()->retrieve_portfolio_notification($this->get_current_node());
                $hasNotification = $portfolioNotification instanceof Notification;
            }
            else
            {
                $feedbackCount = 0;
                $hasNotification = false;
            }

            $actionsGenerator = new ActionsGenerator(
                $this, $baseParameters, $isAllowedToViewFeedback, $feedbackCount, $hasNotification
            );

            $buttonToolbar->addItems($actionsGenerator->run());
        }

        return $buttonToolbar;
    }

    /**
     *
     * @param string $label
     * @param unknown $glyph
     * @param unknown $allowedContentObjectTypes
     * @param string[] $parameters
     * @param array $extraActions
     * @param unknown $classes
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
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        $feedback = $this->get_parent()->get_portfolio_feedback();
        $feedback->set_complex_content_object_id($this->get_current_complex_content_object_item()->get_id());

        return $feedback;
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::get_notification()
     */
    public function get_notification()
    {
        $notification = $this->get_parent()->get_portfolio_notification();
        $notification->set_complex_content_object_id($this->get_current_complex_content_object_item()->get_id());

        return $notification;
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        return $this->get_parent()->is_allowed_to_create_feedback($this->get_current_node());
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return $this->get_parent()->is_allowed_to_delete_feedback($feedback);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::is_allowed_to_update_feedback()
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return $this->get_parent()->is_allowed_to_update_feedback($feedback);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback()
    {
        return $this->get_parent()->is_allowed_to_view_feedback($this->get_current_node());
    }

    public function renderFeedback()
    {
        if ($this->get_parent()->is_allowed_to_view_feedback($this->get_current_node()) ||
            $this->get_parent()->is_allowed_to_create_feedback($this->get_current_node()))
        {
            return $this->getApplicationFactory()->getApplication(
                Manager::context(), new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            )->run();
        }
    }

    public function render_header($pageTitle = '')
    {
        $buttonToolBarRenderer = new ButtonToolBarRenderer($this->getButtonToolBar());

        $html = [];

        $html[] = parent::render_header($pageTitle);
        $html[] = $buttonToolBarRenderer->render();

        return implode(PHP_EOL, $html);
    }

    /**
     * Render the last actions undertaken by the user in the portfolio
     *
     * @return string
     */
    public function render_last_actions()
    {
        $html = [];

        $last_activities = DataManager::retrieve_activities(
            $this->get_current_content_object(), null, 0, 1, new OrderBy(
                array(new OrderProperty(new PropertyConditionVariable(Activity::class, Activity::PROPERTY_DATE)))
            )
        );

        $last_activity = $last_activities->current();

        if ($last_activity)
        {
            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-body">';

            $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

            $html[] = Translation::get(
                'LastEditedOn',
                array('DATE' => DatetimeUtilities::format_locale_date($date_format, $last_activity->get_date()))
            );

            $html[] = '<br />';

            $html[] = Translation::get(
                'LastAction',
                array('ACTION' => $last_activity->get_type_string(), 'CONTENT' => $last_activity->get_content())
            );

            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Render the basic statistics for the portfolio item / folder
     *
     * @param ContentObject $content_object
     *
     * @return string
     */
    public function render_statistics($content_object)
    {
        $html = [];

        $html[] = '<div class="portfolio-statistics">';

        if ($this->get_user_id() == $content_object->get_owner_id())
        {
            $html[] = Translation::get(
                'CreatedOn', array('DATE' => $this->format_date($content_object->get_creation_date()))
            );
        }
        else
        {
            $html[] = Translation::get(
                'CreatedOnBy', array(
                    'DATE' => $this->format_date($content_object->get_creation_date()),
                    'USER' => $content_object->get_owner()->get_fullname()
                )
            );
        }

        if ($content_object->get_creation_date() != $content_object->get_modification_date())
        {
            $html[] = '<br />';
            $html[] = Translation::get(
                'LastModifiedOn', array('DATE' => $this->format_date($content_object->get_modification_date()))
            );
        }

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedback_id)
    {
        return $this->get_parent()->retrieve_portfolio_feedback($feedback_id);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks($count, $offset)
    {
        return $this->get_parent()->retrieve_portfolio_feedbacks($this->get_current_node(), $count, $offset);
    }

    /**
     *
     * @see \core\repository\content_object\portfolio\feedback\FeedbackSupport::retrieve_notification()
     */
    public function retrieve_notification()
    {
        return $this->get_parent()->retrieve_portfolio_notification($this->get_current_node());
    }

    /**
     * Retrieves all the notifications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection<Notification>
     */
    public function retrieve_notifications()
    {
        return $this->get_application()->retrievePortfolioNotifications($this->get_current_node());
    }
}
