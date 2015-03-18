<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Platform\Translation;

class ClassNotExistException extends \Exception
{

    public function __construct($class)
    {
        parent :: __construct(Translation :: get('ClassNotExist', array('CLASS' => $class)));
    }
}
