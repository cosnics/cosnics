<?php
namespace Chamilo\Core\Menu\Factory;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Cache\Simple\FilesystemCache;

/**
 * @package Chamilo\Core\Menu\Factory
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MenuCacheProviderFactory
{
    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    private $configurablePathBuilder;

    /**
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * @return \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    /**
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function setConfigurablePathBuilder(ConfigurablePathBuilder $configurablePathBuilder): void
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * @return \Symfony\Component\Cache\Simple\FilesystemCache
     */
    public function getItemCacheProvider()
    {
        return new FilesystemCache(
            'item', 0, $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Core\Menu')
        );
    }

    /**
     * @return \Symfony\Component\Cache\Simple\FilesystemCache
     */
    public function getRightsCacheProvider()
    {
        return new FilesystemCache(
            'rights', 0, $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Core\Menu')
        );
    }
}
