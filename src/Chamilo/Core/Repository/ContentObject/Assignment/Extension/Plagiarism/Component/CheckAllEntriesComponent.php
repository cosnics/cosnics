<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Component\ExtensionComponent;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Bridge\Interfaces\EntryPlagiarismResultServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\EntriesPlagiarismChecker;
use Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism\Service\PlagiarismChecker;
use Chamilo\Libraries\Architecture\ErrorHandler\ExceptionLogger\ExceptionLoggerInterface;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Extension\Plagiarism
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CheckAllEntriesComponent extends Manager
{

    /**
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Application\Plagiarism\Domain\Exception\PlagiarismException
     */
    function run()
    {
        $this->validateAccess();

        try
        {
            $this->getEntriesPlagiarismChecker()->checkAllEntriesForPlagiarism(
                $this->getUser(), $this->getAssignmentServiceBridge(), $this->getEntryPlagiarismResultServiceBridge()
            );

            $this->redirect(
                $this->getTranslator()->trans('EntriesChecked', [], Manager::context()), false,
                [self::PARAM_ACTION => self::ACTION_BROWSE]
            );
        }
        catch (EulaNotAcceptedException $eulaNotAcceptedException)
        {
            $redirectUrl = $this->get_url();

            return $this->getPlagiarismChecker()->getRedirectToEULAPageResponse($redirectUrl);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            $this->redirect(
                $this->getTranslator()->trans('EntriesNotChecked', [], Manager::context()), true,
                [self::PARAM_ACTION => self::ACTION_BROWSE]
            );
        }

        return null;
    }

}