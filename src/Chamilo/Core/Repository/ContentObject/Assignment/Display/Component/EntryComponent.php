<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Form\ScoreForm;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\ScoreFormProcessor;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Theme;
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
class EntryComponent extends Manager implements \Chamilo\Core\Repository\Feedback\FeedbackSupport
{

    /**
     *
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    private $entry;

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

    public function run()
    {
        $this->initializeEntry();
        $this->processSubmittedData();

        return $this->getTwig()->render(Manager::context() . ':EntryViewer.html.twig', $this->getTemplateProperties());
    }

    /**
     *
     * @return string[]
     */
    protected function getTemplateProperties()
    {
        $dateFormat = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
        $submittedDate = DatetimeUtilities::format_locale_date($dateFormat, $this->getEntry()->getSubmitted());

        $entityRenderer = $this->getDataProvider()->getEntityRendererForEntityTypeAndId(
            $this->getEntry()->getEntityType(),
            $this->getEntry()->getEntityId()
        );

        $configuration = new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this);
        $configuration->set(\Chamilo\Core\Repository\Feedback\Manager::CONFIGURATION_SHOW_FEEDBACK_HEADER, false);

        $feedbackManagerHtml = $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Feedback\Manager::context(), $configuration
        )->run();

        return [
            'HEADER' => $this->render_header(),
            'FOOTER' => $this->render_footer(),
            'BUTTON_TOOLBAR' => $this->getButtonToolbarRenderer()->render(),
            'CONTENT_OBJECT_RENDITION' => $this->renderContentObject(),
            'FEEDBACK_MANAGER' => $feedbackManagerHtml,
            'SUBMITTED_DATE' => $submittedDate, 'SUBMITTED_BY' => $entityRenderer->getEntityName(),
            'SCORE_FORM' => $this->getScoreForm()->render()
        ];
    }

    protected function processSubmittedData()
    {
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

            if (!$detailsProcessor->run())
            {
                return false;
            }
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
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry
     */
    protected function getEntry()
    {
        return $this->entry;
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
            ContentObjectRendition::VIEW_FULL,
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
        // TODO: Only course managers / teachers should be able to do this
        return true;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_create_feedback()
     */
    public function is_allowed_to_create_feedback()
    {
        // TODO: Only course managers / teachers should be able to do this
        return true;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_update_feedback()
     */
    public function is_allowed_to_update_feedback($feedback)
    {
        // TODO: Only course managers / teachers should be able to do this
        return true;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Feedback\FeedbackSupport::is_allowed_to_delete_feedback()
     */
    public function is_allowed_to_delete_feedback($feedback)
    {
        // TODO: Only course managers / teachers should be able to do this
        return true;
    }

    protected function getButtonToolbarRenderer()
    {
        $buttonToolBar = new ButtonToolBar();

        if (!isset($this->actionBar))
        {

            $buttonToolBar->addButtonGroup(
                new ButtonGroup(
                    array(
                        new Button(
                            Translation::get('Download'),
                            new FontAwesomeGlyph('download'),
                            $this->get_url(
                                [
                                    self::PARAM_ACTION => self::ACTION_DOWNLOAD,
                                    self::PARAM_ENTITY_TYPE => $this->getEntityType(),
                                    self::PARAM_ENTITY_ID => $this->entry->getEntityId(),
                                    self::PARAM_ENTRY_ID => $this->getEntry()->getId()
                                ]
                            )
                        )
                    )
                )
            );

            $buttonToolBar->addButtonGroup(
                new ButtonGroup(
                    array(
                        new Button(
                            Translation::get(
                                'BrowseEntities',
                                [
                                    'NAME' => strtolower(
                                        $this->getDataProvider()->getEntityNameByType($this->getEntityType())
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
                        ),
                        new Button(
                            Translation::get('BrowseEntries'),
                            new FontAwesomeGlyph('file-text'),
                            $this->get_url(
                                [
                                    self::PARAM_ACTION => self::ACTION_BROWSE,
                                    self::PARAM_ENTITY_TYPE => $this->getEntityType(),
                                    self::PARAM_ENTITY_ID => $this->entry->getEntityId()
                                ],
                                [self::PARAM_ENTRY_ID]
                            )
                        )
                    )
                )
            );
        }

        $this->actionBar = new ButtonToolBarRenderer($buttonToolBar);

        return $this->actionBar;
    }

    protected function initializeEntry()
    {
        $entryIdentifier = $this->getRequest()->query->get(self::PARAM_ENTRY_ID);

        if (!$entryIdentifier)
        {
            throw new NoObjectSelectedException(Translation::get('Entry'));
        }
        else
        {
            $this->set_parameter(self::PARAM_ENTRY_ID, $entryIdentifier);
        }

        $this->entry = $this->getDataProvider()->findEntryByIdentifier($entryIdentifier);
    }
}
