<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ExtensionComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ViewerComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions\ExtensionInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismExtension implements ExtensionInterface
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker
     */
    protected $plagiarismChecker;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\Turnitin\EulaService
     */
    protected $eulaService;

    /**
     * @var ExceptionLoggerInterface
     */
    protected $exceptionLogger;

    /**
     * @var \Chamilo\Libraries\Architecture\Bridge\BridgeManager
     */
    protected $bridgeManager;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * PlagiarismExtension constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker $plagiarismChecker
     * @param \Chamilo\Libraries\Architecture\Bridge\BridgeManager $bridgeManager
     * @param \Twig_Environment $twig
     * @param \Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface $exceptionLogger
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker $plagiarismChecker,
        \Chamilo\Libraries\Architecture\Bridge\BridgeManager $bridgeManager, \Twig_Environment $twig,
        ExceptionLoggerInterface $exceptionLogger
    )
    {
        $this->plagiarismChecker = $plagiarismChecker;
        $this->bridgeManager = $bridgeManager;
        $this->twig = $twig;
        $this->exceptionLogger = $exceptionLogger;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent $entryComponent
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     */
    public function extendEntryViewerTitle(
        EntryComponent $entryComponent, Assignment $assignment, Entry $entry, User $user
    )
    {
        // TODO: Implement extendEntryViewerTitle() method.
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent $entryComponent
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return string
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function extendEntryViewerParts(
        EntryComponent $entryComponent, Assignment $assignment, Entry $entry, User $user
    )
    {
        if(!$this->getAssignmentServiceBridge()->canEditAssignment())
        {
            return null;
        }

        $createUrl = $entryComponent->get_url(
            [
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_EXTENSION,
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID => $entry->getId(),
                ExtensionComponent::PARAM_EXTENSION => Manager::context(),
                Manager::PARAM_ACTION => Manager::ACTION_CHECK_PLAGIARISM
            ]
        );

        $viewUrl = $entryComponent->get_url(
            [
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_EXTENSION,
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID => $entry->getId(),
                ExtensionComponent::PARAM_EXTENSION => Manager::context(),
                Manager::PARAM_ACTION => Manager::ACTION_VIEW_PLAGIARISM_RESULT
            ]
        );

        $canCheckPlagiarism = $this->plagiarismChecker->canCheckForPlagiarism($entry);
        $result = $this->getEntryPlagiarismResultServiceBridge()->findEntryPlagiarismResultByEntry($entry);
        $hasResult = ($result instanceof EntryPlagiarismResult);

        $submissionStatus = $hasResult ? $result->getSubmissionStatus() : null;

        return $this->twig->render(
            Manager::context() . ':AssignmentPlagiarismPart.html.twig',
            [
                'CHECK_PLAGIARISM_URL' => $createUrl,
                'VIEW_URL' => $viewUrl,
                'CAN_CHECK_PLAGIARISM' => $canCheckPlagiarism,
                'HAS_RESULT' => $hasResult,
                'RESULT' => $result,
                'ERROR_TRANSLATION_VARIABLE' => $hasResult ? $submissionStatus->getErrorTranslationVariable() : '',
                'IN_PROGRESS' => $hasResult ? $submissionStatus->isInProgress() : false,
                'SUCCESS' => $hasResult ? $submissionStatus->isReportGenerated() : false,
                'FAILED' => $hasResult ? $submissionStatus->isFailed() : false,
                'CAN_RETRY' => $hasResult ? $submissionStatus->canRetry() : false,
                'MAINTENANCE_MODE' => $this->plagiarismChecker->isInMaintenanceMode()
            ]
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment $assignment
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry $entry
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @throws \Exception
     */
    public function entryCreated(Assignment $assignment, Entry $entry, User $user)
    {
        if($this->plagiarismChecker->isInMaintenanceMode())
        {
            return;
        }

        if($this->getEntryPlagiarismResultServiceBridge()->checkForPlagiarismAfterSubmission())
        {
            if(!$this->plagiarismChecker->canCheckForPlagiarism($entry))
            {
                return;
            }

            try
            {
                $this->plagiarismChecker->checkEntryForPlagiarism(
                    $assignment, $entry, $this->getEntryPlagiarismResultServiceBridge()
                );
            }
            catch (EulaNotAcceptedException $eulaNotAcceptedException)
            {
                $assignmentUser = new User();
                $assignmentUser->setId($assignment->get_owner_id());

                $this->eulaService->acceptEULA($assignmentUser);

                $this->plagiarismChecker->checkEntryForPlagiarism(
                    $assignment, $entry, $this->getEntryPlagiarismResultServiceBridge()
                );
            }
        }
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ViewerComponent $viewerComponent
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar $buttonToolBar
     */
    public function buildButtonToolbarForEntityBrowser(ViewerComponent $viewerComponent, ButtonToolBar $buttonToolBar)
    {
        if($this->plagiarismChecker->isInMaintenanceMode())
        {
            return;
        }

        if(!$this->getAssignmentServiceBridge()->canEditAssignment())
        {
            return;
        }

        $browserUrl = $viewerComponent->get_url(
            [
                \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_EXTENSION,
                ExtensionComponent::PARAM_EXTENSION => Manager::context(),
                Manager::PARAM_ACTION => Manager::ACTION_BROWSE
            ]
        );

        $buttonToolBar->addButtonGroup(
            new ButtonGroup(
                array(
                    new Button(
                        Translation::get('Plagiarism'),
                        new FontAwesomeGlyph('files-o'),
                        $browserUrl
                    )
                )
            )
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
     */
    protected function getEntryPlagiarismResultServiceBridge()
    {
        return $this->bridgeManager->getBridgeByInterface(EntryPlagiarismResultServiceBridgeInterface::class);
    }

    /**
     * @return AssignmentServiceBridgeInterface
     */
    protected function getAssignmentServiceBridge()
    {
        return $this->bridgeManager->getBridgeByInterface(AssignmentServiceBridgeInterface::class);
    }
}