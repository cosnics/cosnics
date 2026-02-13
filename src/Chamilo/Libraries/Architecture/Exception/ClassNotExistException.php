<?php
namespace Chamilo\Libraries\Architecture\Exception;

use Chamilo\Libraries\Service\Utilities\StringUtilities;

/**
 * @package Chamilo\Libraries\Architecture\Exception
 */
class ClassNotExistException extends UserException
{
    public function __construct(string $class)
    {
        parent::__construct(
            $this->getTranslator()->trans('ClassNotExist', ['%Class%' => $class], StringUtilities::LIBRARIES)
        );
    }
}
