<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Manager;

/**
 * @package Chamilo\Core\Home\Storage\DataClass-
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Column extends Element
{
    public const CONFIGURATION_WIDTH = 'width';

    public const CONTEXT = Manager::CONTEXT;

    /**
     * @param string[] $configurationVariables
     *
     * @return string[]
     */
    public static function getConfigurationVariables($configurationVariables = []): array
    {
        return parent::getConfigurationVariables([self::CONFIGURATION_WIDTH]);
    }

    public function getWidth(): int
    {
        return (int) $this->getSetting(self::CONFIGURATION_WIDTH);
    }

    public function setWidth(int $width): void
    {
        $this->setSetting(self::CONFIGURATION_WIDTH, $width);
    }
}
