<?php

namespace Chamilo\Application\Plagiarism\Component;

use Chamilo\Application\Plagiarism\Domain\Turnitin\Exception\EulaNotAcceptedException;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Plagiarism\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Application\Plagiarism\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ViewReportComponent extends Manager
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

        $success = true;
        $message = '';

        try
        {
            $message = 'RefreshSuccess';
        }
        catch(EulaNotAcceptedException $exception)
        {

        }
        catch(\Exception $ex)
        {
            $message = 'RefreshFailed';
            $success = false;
        }

        $this->redirect($message, $success, [self::PARAM_ACTION => self::ACTION_BROWSE]);
        return;

    }


}