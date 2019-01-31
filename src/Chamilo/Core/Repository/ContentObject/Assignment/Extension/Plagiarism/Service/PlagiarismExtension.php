<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\EntryComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ExtensionComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\Extensions\ExtensionInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Storage\DataClass\EntryPlagiarismResult;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;

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
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker $plagiarismChecker,
        \Chamilo\Libraries\Architecture\Bridge\BridgeManager $bridgeManager, \Twig_Environment $twig
    )
    {
        $this->plagiarismChecker = $plagiarismChecker;
        $this->bridgeManager = $bridgeManager;
        $this->twig = $twig;
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
                'IN_PROGRESS' => $hasResult ? $submissionStatus->isInProgress() : false,
                'SUCCESS' => $hasResult ? $submissionStatus->isReportGenerated() : false,
                'FAILED' => $hasResult ? $submissionStatus->isFailed() : false,
                'CAN_RETRY' => $hasResult ? $submissionStatus->canRetry() : false
            ]
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
     */
    protected function getEntryPlagiarismResultServiceBridge()
    {
        return $this->bridgeManager->getBridgeByInterface(EntryPlagiarismResultServiceBridgeInterface::class);
    }
}