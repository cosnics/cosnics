<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ExtensionComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CheckPlagiarismComponent extends Manager
{
    /**
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    function run()
    {
        if(!$this->getAssignmentServiceBridge()->canEditAssignment())
        {
            throw new NotAllowedException();
        }

        $entry = $this->getExtensionComponent()->getEntry();
        if (!$entry instanceof Entry)
        {
            throw new NoObjectSelectedException(
                $this->getTranslator()->trans('Entry', [], 'Chamilo\Core\Repository\ContentObject\Assignment')
            );
        }

        try
        {
            $this->getPlagiarismChecker()->checkEntryForPlagiarism(
                $this->getExtensionComponent()->getAssignment(), $entry, $this->getEntryPlagiarismResultServiceBridge()
            );

            $this->redirect(
                $this->getTranslator()->trans('PlagiarismCheckRequested', [], Manager::context()), false,
                [\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY],
                [self::PARAM_ACTION, ExtensionComponent::PARAM_EXTENSION]
            );
        }
        catch (EulaNotAcceptedException $eulaNotAcceptedException)
        {
            $redirectUrl = $this->get_url();

            return $this->getPlagiarismChecker()->getRedirectToEULAPageResponse($redirectUrl);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex, ExceptionLoggerInterface::EXCEPTION_LEVEL_FATAL_ERROR);

            $this->redirect(
                $this->getTranslator()->trans('PlagiarismCheckRequestFailed', [], Manager::context()), true,
                [\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::ACTION_ENTRY],
                [self::PARAM_ACTION, ExtensionComponent::PARAM_EXTENSION]
            );
        }

        return null;
    }

    /**
     *
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID,
            ExtensionComponent::PARAM_EXTENSION
        );
    }

}