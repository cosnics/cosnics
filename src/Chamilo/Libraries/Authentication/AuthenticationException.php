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

    protected string $errorMessage;

    public function __construct(?string $message = null, ?int $code = null, ?string $previous = null)
    {
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

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}