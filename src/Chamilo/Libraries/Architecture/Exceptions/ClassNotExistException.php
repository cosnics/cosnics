<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class ClassNotExistException extends UserException
{

    /**
     *
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct(Translation::get('ClassNotExist', array('CLASS' => $class), StringUtilities::LIBRARIES));
    }
}
