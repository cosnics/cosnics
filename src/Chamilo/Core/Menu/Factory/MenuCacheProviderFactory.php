<?php
namespace Chamilo\Core\Menu\Factory;

use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Core\Menu\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MenuCacheProviderFactory
{
    private ConfigurablePathBuilder $configurablePathBuilder;

    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getItemCacheProvider(): FilesystemAdapter
    {
        return new FilesystemAdapter(
            md5('Chamilo\Core\Menu\Item'), 0, $this->getConfigurablePathBuilder()->getConfiguredCachePath()
        );
    }

    public function getRightsCacheProvider(): FilesystemAdapter
    {
        return new FilesystemAdapter(
            md5('Chamilo\Core\Menu\Rights'), 0, $this->getConfigurablePathBuilder()->getConfiguredCachePath()
        );
    }

    public function setConfigurablePathBuilder(ConfigurablePathBuilder $configurablePathBuilder): void
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }
}
