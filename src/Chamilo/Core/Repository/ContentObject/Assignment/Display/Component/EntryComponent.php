<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreForm;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\EntryNavigator;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\ScoreFormProcessor;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryComponent extends Manager implements \Chamilo\Core\Repository\Feedback\FeedbackSupport, TableSupport
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    private $score;

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreForm
     */
    private $scoreForm;

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * @var EntryNavigator
     */
    protected $entryNavigator;

    /**
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->initializeEntry();
        $this->checkAccessRights();
        $this->processSubmittedData();

        return $this->getTwig()->render(Manager::context() . ':EntryViewer.html.twig', $this->getTemplateProperties());
    }

    /**
     *
     */
    protected function initializeEntry()
    {
        try
        {
            parent::initializeEntry();
        }
        catch (\Exception $ex)
        {
            $this->entry =
                $this->getDataProvider()->findLastEntryForEntity($this->getEntityType(), $this->getEntityIdentifier());
        }

        if (!$this->entry instanceof Entry)
        {
            $breadcrumbTrail = BreadcrumbTrail::getInstance();
            $breadcrumbTrail->get_last()->set_name(
                Translation::getInstance()->getTranslation('ViewerComponent', null, Manager::context())
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if ($this->getEntry() &&
            $this->getRightsService()->canUserViewEntry($this->getUser(), $this->getAssignment(), $this->getEntry()))
        {
            return;
        }

        if ($this->getRightsService()->canUserViewEntity(
            $this->getUser(), $this->getAssignment(), $this->getEntityType(), $this->getEntityIdentifier()
        ))
        {
            return;
        }

        throw new NotAllowedException();
    }

    /**
     *
     * @return string[]
     */
    protected function getTemplateProperties()
    {
        /** @var \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment */
        $assignment = $this->get_root_content_object();

        $baseParameters = [
            'HAS_ENTRY' => false,
            'IS_USER_PART_OF_ENTITY' => $this->getDataProvider()->isUserPartOfEntity(
                $this->getUser(), $this->getEntityType(), $this->getEntityIdentifier()
            ),
            'CHANGE_ENTITY_URL' => $this->get_url([self::PARAM_ENTITY_ID => '__ENTITY_ID__']),
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'BUTTON_TOOLBAR' => $this->getButtonToolbarRenderer()->render(),
            'NAVIGATOR_BUTTON_TOOLBAR' => $this->getNavigatorButtonToolbarRenderer()->render(),
            'ASSIGNMENT_TITLE' => $this->get_root_content_object()->get_title(),
            'ASSIGNMENT_RENDITION' => $this->renderAssignment(),
        ];

        $baseParameters = $this->getAvailableEntitiesParameters($baseParameters);

        if (!$this->getEntry() instanceof Entry)
        {
            return $baseParameters;
        }

        $dateFormat = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
        $submittedDate = DatetimeUtilities::format_locale_date($dateFormat, $this->getEntry()->getSubmitted());

        $configuration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);
        $configuration->set(\Chamilo\Core\Repository\Feedback\Manager::CONFIGURATION_SHOW_FEEDBACK_HEADER, false);

        $feedbackManagerHtml = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Feedback\Manager::context(), $configuration
        )->run();

        $contentObjects = $assignment->getAutomaticFeedbackObjects();

        $score = $this->getScore() instanceof Score ? $this->getScore()->getScore() : 0;

        $extendParameters = [
            'HAS_ENTRY' => true,
            'CONTENT_OBJECT_TITLE' => $this->getEntry()->getContentObject()->get_title(),
            'CONTENT_OBJECT_RENDITION' => $this->renderContentObject(),
            'FEEDBACK_MANAGER' => $feedbackManagerHtml,
            'SUBMITTED_DATE' => $submittedDate,
            'SUBMITTED_BY' => $this->getUserService()->getUserFullNameById($this->getEntry()->getUserId()),
            'SCORE_FORM' => $this->getScoreForm()->render(),
            'SCORE' => $score,
            'CAN_EDIT_ASSIGNMENT' => $this->getDataProvider()->canEditAssignment(),
            'ENTRY_TABLE' => $this->renderEntryTable(),
            'ENTRY_COUNT' => $this->getDataProvider()->countEntriesForEntityTypeAndId(
                $this->getEntityType(), $this->getEntityIdentifier()
            ),
            'SHOW_AUTOMATIC_FEEDBACK' => $assignment->isAutomaticFeedbackVisible(),
            'AUTOMATIC_FEEDBACK_TEXT' => $assignment->get_automatic_feedback_text(),
            'AUTOMATIC_FEEDBACK_CONTENT_OBJECTS' => $contentObjects,
            'ATTACHMENT_VIEWER_URL' => $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_VIEW_ATTACHMENT,
                    self::PARAM_ATTACHMENT_ID => '__ATTACHMENT_ID__'
                ]
            )
        ];

        return array_merge($baseParameters, $extendParameters);
    }

    /**
     * @return bool|void
     * @throws \Exception
     *
     * @throws \HTML_QuickForm_Error
     */
    protected function processSubmittedData()
    {
        if (!$this->getDataProvider()->canEditAssignment() || !$this->getEntry() instanceof Entry)
        {
            return;
        }

        $scoreForm = $this->getScoreForm();

        if ($scoreForm->validate())
        {
            $detailsProcessor = new ScoreFormProcessor(
                $this->getDataProvider(),
                $this->getUser(),
                $this->getEntry(),
                $this->getScore(),
                $scoreForm->exportValues()
            );

            $this->score = $detailsProcessor->run();
        }

        return true;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score
     */
    protected function getScore()
    {
        if (!isset($this->score))
        {
            $this->score = $this->getDataProvider()->findScoreByEntry($this->getEntry());
        }

        return $this->score;
    }

    /**
     *
     * @return string
     */
    protected function renderContentObject()
    {
        $contentObject = $this->getEntry()->getContentObject();

        $display = ContentObjectRenditionImplementation::factory(
            $contentObject,
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_DESCRIPTION,
            $this
        );

        return $display->render();
    }

    /**
     *
     * @return string
     */
    protected function renderAssignment()
    {
        $contentObject = $this->get_root_content_object();

        $display = ContentObjectRenditionImplementation::factory(
            $contentObject,
            ContentObjectRendition::FORMAT_HTML,
            ContentObjectRendition::VIEW_DESCRIPTION,
            $this
        );

        return $display->render();
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreForm
     */
    protected function getScoreForm()
    {
        if (!isset($this->scoreForm))
        {
            $this->scoreForm = new ScoreForm(
                $this->getScore(),
                $this->getDataProvider(),
                $this->get_url(array(self::PARAM_ENTRY_ID => $this->getEntry()->getId())),
                $this->getTwig()
            );
        }

        return $this->scoreForm;
    }

    /**
     *
     * @return string
     */
    protected function renderEntryTable()
    {
        $table = $this->getDataProvider()->getEntryTableForEntityTypeAndId(
            $this,
            $this->getEntityType(),
            $this->getEntityIdentifier()
        );

        if (!empty($table))
        {
            return $table->render();
        }

        return '';
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedbacks()
     */
    public function retrieve_feedbacks($count, $offset)
    {
        return $this->getDataProvider()->findFeedbackByEntry($this->getEntry());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::count_feedbacks()
     */
    public function count_feedbacks()
    {
        return $this->getDataProvider()->countFeedbackByEntry($this->getEntry());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::retrieve_feedback()
     */
    public function retrieve_feedback($feedbackIdentifier)
    {
        return $this->getDataProvider()->findFeedbackByIdentifier($feedbackIdentifier);
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::get_feedback()
     */
    public function get_feedback()
    {
        $feedback = $this->getDataProvider()->initializeFeedback();
        $feedback->setEntryId($this->getEntry()->getId());

        return $feedback;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_view_feedback()
     */
    public function is_allowed_to_view_feedback()
    {
        return true;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        return true;
    }

    /**
     * @param \Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback $feedback
     *
     * @return bool
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->getUser()->getId();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        return $feedback->get_user_id() == $this->getUser()->getId();
    }

    protected function getButtonToolbarRenderer()
    {
        $buttonToolBar = new ButtonToolBar();

        if (!isset($this->actionBar))
        {

            $buttonGroup = new ButtonGroup();

            if (
                $this->getRightsService()->canUserCreateEntry(
                    $this->getUser(), $this->getAssignment(), $this->getEntityType(), $this->getEntityIdentifier()
                ) && $this->getAssignment()->canSubmit()
            )
            {
                $buttonGroup->addButton(
                    new Button(
                        Translation::get('AddNewEntry'),
                        new FontAwesomeGlyph('plus'),
                        $this->get_url(
                            [
                                self::PARAM_ACTION => self::ACTION_CREATE,
                                self::PARAM_ENTITY_TYPE => $this->getEntityType(),
                                self::PARAM_ENTITY_ID => $this->getEntityIdentifier()
                            ]
                        ),
                        Button::DISPLAY_ICON_AND_LABEL,
                        false,
                        'btn-success'
                    )
                );
            }

            $buttonToolBar->addButtonGroup($buttonGroup);

            $buttonGroup = new ButtonGroup();

            if ($this->getEntry() instanceof Entry)
            {
                $splitDropdownButton = new SplitDropdownButton(
                    Translation::get('DownloadAll'),
                    new FontAwesomeGlyph('download'),
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_DOWNLOAD,
                            self::PARAM_ENTITY_TYPE => $this->getEntityType(),
                            self::PARAM_ENTITY_ID => $this->getEntityIdentifier()
                        ],
                        [self::PARAM_ENTRY_ID]
                    )
                );

                $splitDropdownButton->addSubButton(
                    new SubButton(
                        Translation::get('DownloadCurrent'),
                        new FontAwesomeGlyph('download'),
                        $this->get_url(
                            [
                                self::PARAM_ACTION => self::ACTION_DOWNLOAD,
                                self::PARAM_ENTITY_TYPE => $this->getEntityType(),
                                self::PARAM_ENTITY_ID => $this->getEntityIdentifier(),
                                self::PARAM_ENTRY_ID => $this->getEntry()->getId()
                            ]

                        )
                    )
                );

                $buttonGroup->addButton($splitDropdownButton);
            }

            $buttonToolBar->addButtonGroup($buttonGroup);

            if ($this->getDataProvider()->canEditAssignment() || $this->getAssignment()->get_visibility_submissions())
            {
                $buttonToolBar->addButtonGroup(
                    new ButtonGroup(
                        array(
                            new Button(
                                Translation::get(
                                    'BrowseEntities',
                                    [
                                        'NAME' => strtolower(
                                            $this->getDataProvider()->getPluralEntityNameByType($this->getEntityType())
                                        )
                                    ]
                                ),
                                new FontAwesomeGlyph('user'),
                                $this->get_url(
                                    [
                                        self::PARAM_ACTION => self::ACTION_VIEW
                                    ],
                                    [self::PARAM_ENTRY_ID]
                                )
                            )
                        )
                    )
                );
            }
        }

        $this->actionBar = new ButtonToolBarRenderer($buttonToolBar);

        return $this->actionBar;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    protected function getNavigatorButtonToolbarRenderer()
    {
        $buttonToolBar = new ButtonToolBar();

        if (!$this->getDataProvider()->canEditAssignment() || empty($this->getEntry()))
        {
            return new ButtonToolBarRenderer($buttonToolBar);
        }

        $currentEntityPosition = $this->getEntryNavigator()->getCurrentEntityPosition(
            $this->getDataProvider(), $this->getEntry(), $this->getEntityType(), $this->getEntityIdentifier()
        );

        $currentEntryPosition = $this->getEntryNavigator()->getCurrentEntryPosition(
            $this->getDataProvider(), $this->getEntry(), $this->getEntityType(), $this->getEntityIdentifier()
        );

        $translator = Translation::getInstance();
        $entityName = $this->getDataProvider()->getEntityNameByType($this->getEntityType());

        $entitiesCount = $this->getDataProvider()->countEntitiesWithEntriesByEntityType($this->getEntityType());
        $entriesCount = $this->getDataProvider()->countEntriesForEntityTypeAndId(
            $this->getEntityType(), $this->getEntityIdentifier()
        );

        if ($entitiesCount > 1)
        {
            $submittersNavigatorActions = new ButtonGroup();

            $submittersNavigatorActions->addButton(
                new Button(
                    $translator->getTranslation('PreviousEntity', ['ENTITY_NAME' => strtolower($entityName)]),
                    new FontAwesomeGlyph('backward'),
                    $this->getPreviousEntityUrl(),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $submittersNavigatorActions->addButton(
                new Button(
                    '<span class="badge" style="color: white; background-color: #5bc0de;">'
                    . $currentEntityPosition . ' / ' . $entitiesCount . '</span>',
                    null,
                    '#',
                    ToolbarItem::DISPLAY_LABEL
                )
            );

            $submittersNavigatorActions->addButton(
                new Button(
                    $translator->getTranslation('NextEntity', ['ENTITY_NAME' => strtolower($entityName)]),
                    new FontAwesomeGlyph('forward'),
                    $this->getNextEntityUrl(),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolBar->addButtonGroup($submittersNavigatorActions);
        }

        if ($entriesCount > 1)
        {
            $submissionsNavigatorActions = new ButtonGroup();

            $submissionsNavigatorActions->addButton(
                new Button(
                    $translator->getTranslation('EarlierEntry'),
                    new FontAwesomeGlyph('backward'),
                    $this->getPreviousEntryUrl(),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $submissionsNavigatorActions->addButton(
                new Button(
                    '<span class="badge" style="color: white; background-color: #28a745;">'
                    . $currentEntryPosition . ' / ' . $entriesCount . '</span>',
                    null,
                    '#',
                    ToolbarItem::DISPLAY_LABEL
                )
            );

            $submissionsNavigatorActions->addButton(
                new Button(
                    $translator->getTranslation('LaterEntry'),
                    new FontAwesomeGlyph('forward'),
                    $this->getNextEntryUrl(),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolBar->addButtonGroup($submissionsNavigatorActions);
        }

        return new ButtonToolBarRenderer($buttonToolBar);
    }

    /**
     * @return string
     */
    protected function getPreviousEntryUrl()
    {
        $previousEntry = $this->getEntryNavigator()->getPreviousEntry(
            $this->getDataProvider(), $this->getEntry(), $this->getEntityType(), $this->getEntityIdentifier()
        );

        if (!$previousEntry instanceof Entry)
        {
            return null;
        }

        return $this->get_url(array(self::PARAM_ENTRY_ID => $previousEntry->getId()));
    }

    /**
     * @return string
     */
    protected function getNextEntryUrl()
    {
        $nextEntry = $this->getEntryNavigator()->getNextEntry(
            $this->getDataProvider(), $this->getEntry(), $this->getEntityType(), $this->getEntityIdentifier()
        );

        if (!$nextEntry instanceof Entry)
        {
            return null;
        }

        return $this->get_url(array(self::PARAM_ENTRY_ID => $nextEntry->getId()));
    }

    /**
     * @return string
     */
    protected function getPreviousEntityUrl()
    {
        $previousEntity = $this->getEntryNavigator()->getPreviousEntity(
            $this->getDataProvider(), $this->getEntry(), $this->getEntityType(), $this->getEntityIdentifier()
        );

        if (empty($previousEntity[Entry::PROPERTY_ENTITY_ID]))
        {
            return null;
        }

        return $this->get_url(
            array(self::PARAM_ENTITY_ID => $previousEntity[Entry::PROPERTY_ENTITY_ID]), array(self::PARAM_ENTRY_ID)
        );
    }

    /**
     * @return string
     */
    protected function getNextEntityUrl()
    {
        $nextEntity = $this->getEntryNavigator()->getNextEntity(
            $this->getDataProvider(), $this->getEntry(), $this->getEntityType(), $this->getEntityIdentifier()
        );

        if (empty($nextEntity[Entry::PROPERTY_ENTITY_ID]))
        {
            return null;
        }

        return $this->get_url(
            array(self::PARAM_ENTITY_ID => $nextEntity[Entry::PROPERTY_ENTITY_ID]), array(self::PARAM_ENTRY_ID)
        );
    }

    /**
     * Returns the condition
     *
     * @param string $tableClassname
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition
     */
    public function get_table_condition($tableClassname)
    {
        // TODO: Implement get_table_condition() method.
    }

    public function get_additional_parameters()
    {
        return array(self::PARAM_ENTRY_ID, self::PARAM_ENTITY_ID, self::PARAM_ENTITY_TYPE);
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\EntryNavigator
     */
    protected function getEntryNavigator()
    {
        if (!isset($this->entryNavigator))
        {
            $this->entryNavigator = new EntryNavigator();
        }

        return $this->entryNavigator;
    }

    /**
     * @return \Chamilo\Core\User\Service\UserService
     */
    protected function getUserService()
    {
        return $this->getService('chamilo.core.user.service.user_service');
    }
}
