<?php
namespace Chamilo\Libraries\Cache\Doctrine;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Cache\IdentifiableCacheService;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 *
 * @package Chamilo\Libraries\Cache\Doctrine
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DoctrineCacheService extends IdentifiableCacheService
{

    /**
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    private $cacheProvider;

    /**
     *
     * @see \Chamilo\Libraries\Cache\IdentifiableCacheService::clear()
     */
    public function clear()
    {
        return $this->getCacheProvider()->flushAll();
    }

    /**
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     *
     * @return bool
     */
    public function clearForIdentifier($identifier)
    {
        return $this->getCacheProvider()->delete((string) $identifier);
    }

    /**
     *
     * @return string
     */
    protected function getCachePath()
    {
        $configurationConsulter = new ConfigurationConsulter(
            new FileConfigurationLoader(
                new FileConfigurationLocator(new PathBuilder(new ClassnameUtilities(new StringUtilities())))
            )
        );
        $configurablePathBuilder = new ConfigurablePathBuilder(
            $configurationConsulter->getSetting(array('Chamilo\Configuration', 'storage'))
        );

        return $configurablePathBuilder->getCachePath($this->getCachePathNamespace());
    }

    /**
     *
     * @return string
     */
    abstract function getCachePathNamespace();

    /**
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getCacheProvider()
    {
        if (!isset($this->cacheProvider))
        {
            $this->cacheProvider = $this->setupCacheProvider();
        }

        return $this->cacheProvider;
    }

    /**
     *
     * @param \Doctrine\Common\Cache\CacheProvider $cacheProvider
     */
    public function setCacheProvider($cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    protected function getConfigurablePathBuilder()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurablePathBuilder::class
        );
    }

    /**
     * @param \Chamilo\Libraries\Cache\ParameterBag|string $identifier
     *
     * @return false|mixed
     * @throws \Exception
     */
    public function getForIdentifier($identifier)
    {
        if (!$this->getCacheProvider()->contains((string) $identifier))
        {
            if (!$this->warmUpForIdentifier($identifier))
            {
                throw new Exception('CacheError');
            }
        }

        return $this->getCacheProvider()->fetch((string) $identifier);
    }

    /**
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    abstract function setupCacheProvider();
}