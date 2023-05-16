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

    /**
     * @return string
     */
    public function getBlockType()
    {
        return $this->getSetting(self::CONFIGURATION_BLOCK_TYPE);
    }

    /**
     * @param string[] $configurationVariables
     *
     * @return string[]
     */
    public static function getConfigurationVariables($configurationVariables = [])
    {
        return parent::getConfigurationVariables(
            [self::CONFIGURATION_VISIBILITY, self::CONFIGURATION_CONTEXT, self::CONFIGURATION_BLOCK_TYPE]
        );
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->getSetting(self::CONFIGURATION_CONTEXT);
    }

    /**
     * @return bool
     */
    public function getVisibility()
    {
        return $this->getSetting(self::CONFIGURATION_VISIBILITY);
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->getVisibility();
    }

    /**
     * @param string $blockType
     */
    public function setBlockType($blockType)
    {
        $this->setSetting(self::CONFIGURATION_BLOCK_TYPE, $blockType);
    }

    /**
     * @param string $context
     */
    public function setContext($context)
    {
        $this->setSetting(self::CONFIGURATION_CONTEXT, $context);
    }

    /**
     * @param bool $visibility
     */
    public function setVisibility($visibility)
    {
        $this->setSetting(self::CONFIGURATION_VISIBILITY, $visibility);
    }
}
