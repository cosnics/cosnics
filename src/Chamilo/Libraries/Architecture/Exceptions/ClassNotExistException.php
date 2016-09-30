<?php
namespace Chamilo\Libraries\Architecture\Exceptions;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ClassNotExistException extends UserException
{

    public function __construct($class)
    {
        parent :: __construct(
            Translation :: get('ClassNotExist', array('CLASS' => $class), Utilities :: COMMON_LIBRARIES));
    }
}
