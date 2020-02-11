<?php
namespace Chamilo\Core\Menu\Factory;

use Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache;
use Chamilo\Libraries\File\ConfigurablePathBuilder;

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
     * @return \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    public function getItemCacheProvider()
    {
        return new FilesystemCache(
            $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Core\Menu\Item')
        );
    }

    /**
     * @return \Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache
     */
    public function getRightsCacheProvider()
    {
        return new FilesystemCache(
            $this->getConfigurablePathBuilder()->getCachePath('Chamilo\Core\Menu\Rights')
        );
    }
}
