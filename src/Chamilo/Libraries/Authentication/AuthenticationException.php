<?php
namespace Chamilo\Libraries\Authentication;

use Chamilo\Libraries\Platform\Session\Session;

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
     * Constructor
     *
     * @param null $message
     * @param null $code
     * @param null $previous
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        Session :: register('request_uri', $_SERVER['REQUEST_URI']);

        $this->errorMessage = $message;

        $html = array();
        $html[] = $message;
        $html[] = $this->getLoginForm()->toHtml();

        \Exception :: __construct(implode(PHP_EOL, $html), $code, $previous);
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