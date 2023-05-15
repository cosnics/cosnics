<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Integration\Chamilo\Core\Repository\Package;

use Chamilo\Configuration\Package\NotAllowed;

class Remover extends \Chamilo\Configuration\Package\Action\Remover implements NotAllowed
{
    public const CONTEXT = Installer::CONTEXT;
}
