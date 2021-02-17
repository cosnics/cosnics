<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Security;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Authentication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class AuthenticationException extends \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
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
        $security = new Security();
        $message = $security->removeXSS($message);

        Session::register('request_uri', $_SERVER['REQUEST_URI']);

        $this->errorMessage = $message;

        $redirect = new Redirect();
        $currentUrl = $redirect->getCurrentUrl();

        $html = array();
        $html[] = $message;
        $html[] = '<p style="margin-top: 10px;"><a href="' . $currentUrl . '" class="btn btn-success">';
        $html[] = Translation::getInstance()->getTranslation('LoginTryAgain', null, Utilities::COMMON_LIBRARIES);
        $html[] = '</a></p>';

        \Exception::__construct(implode(PHP_EOL, $html), $code, $previous);
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
