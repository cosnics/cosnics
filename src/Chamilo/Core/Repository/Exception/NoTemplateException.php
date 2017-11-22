<?php
namespace Chamilo\Core\Repository\Exception;

use Chamilo\Libraries\Translation\Translation;

class NoTemplateException extends \Exception
{

    public function __construct()
    {
        parent::__construct(Translation::get('NoTemplateAvailable'));
    }
}
