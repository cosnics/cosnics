<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Libraries\Authentication
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class AuthenticationException extends NotAllowedException
{

    /**
     * Caches the error message to avoid doubling the login form
     *
     * @var string
     */
    protected $errorMessage;

    /**
     * @param string $message
     * @param int $code
     * @param string $previous
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        $this->initializeContainer();
        $this->getSession()->set('request_uri', $_SERVER['REQUEST_URI']);

        $this->errorMessage = $message;

        $currentUrl = $this->getRequest()->getUri();

        $html = [];

        $html[] = $message;
        $html[] = '<p style="margin-top: 10px;"><a href="' . $currentUrl . '" class="btn btn-success">';
        $html[] = $this->getTranslator()->trans('LoginTryAgain', [], StringUtilities::LIBRARIES);
        $html[] = '</a></p>';

        Exception::__construct(implode(PHP_EOL, $html), $code, $previous);
    }

    /**
     * Returns the error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}