<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Architecture\Exceptions
 */
class ClassNotExistException extends UserException
{

    public function __construct(string $class)
    {
        parent::__construct(
            $this->getTranslator()->trans('ClassNotExist', ['CLASS' => $class], StringUtilities::LIBRARIES)
        );
    }
}
