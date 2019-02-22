<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RefreshComponent extends Manager
{
    /**
     * @return \Chamilo\Libraries\Format\Response\Response|string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        try
        {
            $this->getContentObjectPlagiarismChecker()->refreshContentObjectPlagiarismChecks(
                $this->get_course(), $this->getUser()
            );

            $message = 'RefreshSuccess';
            $success = true;
        }
        catch (EulaNotAcceptedException $exception)
        {
            $redirectUrl = $this->get_url();

            return $this->getContentObjectPlagiarismChecker()->getRedirectToEULAPageResponse($redirectUrl);
        }
        catch (\Exception $ex)
        {
            $this->getExceptionLogger()->logException($ex);
            $message = 'RefreshFailed';
            $success = false;
        }

        $this->redirect($message, !$success, [self::PARAM_ACTION => self::ACTION_BROWSE]);

        return null;
    }

}