<?php
namespace Chamilo\Core\Repository\Exception;

use Chamilo\Libraries\Platform\Translation;

class NoTemplateException extends \Exception
{

    public function __construct()
    {
        parent :: __construct(Translation :: get('NoTemplateAvailable'));
    }
}
