<?php
namespace Chamilo\Application\Calendar\Package;

use Chamilo\Libraries\Architecture\Application\WebApplicationRemover;

class Remover extends WebApplicationRemover
{
    public const CONTEXT = Installer::CONTEXT;

    public function getAdditionalPackages($packagesList = []): array
    {
        $packagesList[] = 'Chamilo\Application\Calendar\Extension\Personal';

        return parent::getAdditionalPackages($packagesList);
    }
}
