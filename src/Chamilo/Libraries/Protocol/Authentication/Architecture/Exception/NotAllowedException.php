<?php
namespace Chamilo\Libraries\Protocol\Authentication\Architecture\Exception;

use Chamilo\Libraries\Architecture\Exception\UserException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 *
 * @package Chamilo\Libraries\Protocol\Authentication\Architecture\Exception
 */
class NotAllowedException extends UserException
{
    public function __construct()
    {
        $this->getSession()->set('request_uri', $_SERVER['REQUEST_URI']);

        parent::__construct($this->getTranslator()->trans('NotAllowed', [], StringUtilities::LIBRARIES));
    }
}
