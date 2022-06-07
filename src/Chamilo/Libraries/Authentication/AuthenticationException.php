<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Authentication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
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
     *
     * @param string $message
     * @param integer $code
     * @param string $previous
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        Session::register('request_uri', $_SERVER['REQUEST_URI']);

        $this->errorMessage = $message;

        $redirect = new Redirect();
        $currentUrl = $redirect->getCurrentUrl();

        $html = [];
        $html[] = $message;
        $html[] = '<p style="margin-top: 10px;"><a href="' . $currentUrl . '" class="btn btn-success">';
        $html[] = Translation::getInstance()->getTranslation('LoginTryAgain', null, StringUtilities::LIBRARIES);
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