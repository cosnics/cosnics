<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class represents a parameter not defined exception.
 * Throw this if you expected an URL parameter that is not
 * there
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class NotAllowedException extends UserException
{

    /**
     *
     * @param boolean $show_login_form
     */
    public function __construct($showLoginForm = false)
    {
        Session::register('request_uri', $_SERVER['REQUEST_URI']);

        $html = array();

        $html[] = Translation::get('NotAllowed', null, Utilities::COMMON_LIBRARIES);

        // if ($showLoginForm)
        // {
        // $html[] = $this->getLoginForm()->toHtml();
        // }

        parent::__construct(implode(PHP_EOL, $html));
    }

}
