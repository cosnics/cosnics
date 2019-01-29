<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism;

use Chamilo\Application\Plagiarism\Component\TurnitinEulaComponent;
use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Service\PlagiarismChecker;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismCheckerComponent extends Manager
{
    /**
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    function run()
    {
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
                [
                    self::PARAM_ACTION

                ]
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
                $this->getTranslator()->trans('PlagiarismCheckNotRequest', [], Manager::context()), false, [],
                [self::PARAM_ACTION]
            );
        }

        return null;
    }

    /**
     * @return PlagiarismChecker
     */
    protected function getPlagiarismChecker()
    {
        return $this->getService(PlagiarismChecker::class);
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Application\Application|\Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ExtensionComponent
     */
    protected function getExtensionComponent()
    {
        return $this->get_application();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface
     */
    protected function getEntryPlagiarismResultServiceBridge()
    {
        return $this->getBridgeManager()->getBridgeByInterface(EntryPlagiarismResultServiceBridgeInterface::class);
    }

    /**
     *
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(\Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::PARAM_ENTRY_ID);
    }

}