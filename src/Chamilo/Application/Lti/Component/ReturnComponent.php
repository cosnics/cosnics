<?php
namespace Chamilo\Application\Lti\Component;

use Chamilo\Application\Lti\Manager;

/**
 * @package Chamilo\Application\Lti\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ReturnComponent extends Manager
{
    const PARAM_ERROR_MESSAGE = 'lti_errormsg';
    const PARAM_ERROR_LOG = 'lti_errorlog';

    /**
     *
     * @return string
     */
    function run()
    {
        $ltiErrorLog = $this->getRequest()->getFromUrl(self::PARAM_ERROR_LOG);
        if(!empty($ltiErrorLog))
        {
            $this->getExceptionLogger()->logException(new \Exception($ltiErrorLog));
        }

        $ltiErrorMessage = $this->getRequest()->getFromUrl(self::PARAM_ERROR_MESSAGE);
        if(!empty($ltiErrorMessage))
        {
            return '<div class="alert alert-error">' . $ltiErrorMessage . '</div>';
        }

        return null;
    }
}