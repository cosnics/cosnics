<?php
namespace Chamilo\Libraries\Package;

/**
 * @package Chamilo\Libraries\Package
 */
class Installer extends \Chamilo\Configuration\Package\Action\Installer
{
    public const CONTEXT = 'Chamilo\Libraries';

    public static function getAdditionalPackages($packagesList = []): array
    {
        $packagesList[] = 'Chamilo\Libraries\Calendar';
        $packagesList[] = 'Chamilo\Libraries\Rights';

        return parent::getAdditionalPackages($packagesList);
    }
}
