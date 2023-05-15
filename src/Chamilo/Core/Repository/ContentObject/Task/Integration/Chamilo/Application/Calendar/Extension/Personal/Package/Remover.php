<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Integration\Chamilo\Application\Calendar\Extension\Personal\Package;

use Chamilo\Configuration\Package\NotAllowed;

class Remover extends \Chamilo\Configuration\Package\Action\Remover implements NotAllowed
{
    public const CONTEXT = Installer::CONTEXT;
}
