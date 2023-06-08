<?php
namespace Chamilo\Core\Home\Storage\DataClass;

use Chamilo\Core\Home\Manager;

/**
 * @package Chamilo\Core\Home\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class Block extends Element
{
    public const CONFIGURATION_BLOCK_TYPE = 'block_type';
    public const CONFIGURATION_CONTEXT = 'context';
    public const CONFIGURATION_VISIBILITY = 'visibility';

    public const CONTEXT = Manager::CONTEXT;

    public function getBlockType(): string
    {
        return $this->getSetting(self::CONFIGURATION_BLOCK_TYPE);
    }

    /**
     * @param string[] $configurationVariables
     *
     * @return string[]
     */
    public static function getConfigurationVariables($configurationVariables = []): array
    {
        return parent::getConfigurationVariables(
            [self::CONFIGURATION_VISIBILITY, self::CONFIGURATION_CONTEXT, self::CONFIGURATION_BLOCK_TYPE]
        );
    }

    public function getContext(): string
    {
        return $this->getSetting(self::CONFIGURATION_CONTEXT);
    }

    public function getVisibility(): bool
    {
        return (bool) $this->getSetting(self::CONFIGURATION_VISIBILITY);
    }

    public function isVisible(): bool
    {
        return $this->getVisibility();
    }

    public function setBlockType(string $blockType): void
    {
        $this->setSetting(self::CONFIGURATION_BLOCK_TYPE, $blockType);
    }

    public function setContext(string $context): void
    {
        $this->setSetting(self::CONFIGURATION_CONTEXT, $context);
    }

    public function setVisibility(bool $visibility): void
    {
        $this->setSetting(self::CONFIGURATION_VISIBILITY, $visibility);
    }
}
