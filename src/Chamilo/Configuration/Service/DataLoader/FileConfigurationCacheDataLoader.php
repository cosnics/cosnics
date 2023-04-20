<?php
namespace Chamilo\Configuration\Service\DataLoader;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Cache\Interfaces\CacheDataLoaderInterface;
use Chamilo\Libraries\Cache\Interfaces\CacheDataReaderInterface;
use Chamilo\Libraries\Cache\Traits\CacheDataLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @package Chamilo\Configuration\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class FileConfigurationCacheDataLoader implements CacheDataLoaderInterface, CacheDataReaderInterface
{
    use CacheDataLoaderTrait;

    private FileConfigurationLocator $fileConfigurationLocator;

    public function __construct(AdapterInterface $cacheAdapter, FileConfigurationLocator $fileConfigurationLocator)
    {
        $this->cacheAdapter = $cacheAdapter;
        $this->fileConfigurationLocator = $fileConfigurationLocator;
    }

    /**
     * @return string[][]
     * @throws \Exception
     */
    public function getDataForCache(): array
    {
        if ($this->getFileConfigurationLocator()->isAvailable())
        {
            return $this->getFileSettings();
        }
        else
        {
            return $this->getDefaultSettings();
        }
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    protected function getDefaultSettings(): array
    {
        $fileContainer = new ContainerBuilder();
        $xmlFileLoader = new XmlFileLoader(
            $fileContainer, new FileLocator($this->getFileConfigurationLocator()->getDefaultFilePath())
        );
        $xmlFileLoader->load($this->getFileConfigurationLocator()->getDefaultFileName());

        return $this->getSettingsFromContainer($fileContainer);
    }

    public function getFileConfigurationLocator(): FileConfigurationLocator
    {
        return $this->fileConfigurationLocator;
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    protected function getFileSettings(): array
    {
        $fileContainer = new ContainerBuilder();
        $xmlFileLoader = new XmlFileLoader(
            $fileContainer, new FileLocator($this->getFileConfigurationLocator()->getFilePath())
        );
        $xmlFileLoader->load($this->getFileConfigurationLocator()->getFileName());

        return $this->getSettingsFromContainer($fileContainer);
    }

    protected function getSettingsContext(): string
    {
        return 'Chamilo\Configuration';
    }

    /**
     * @return string[]
     */
    protected function getSettingsFromContainer(ContainerBuilder $fileContainer): array
    {
        $settings = [
            $this->getSettingsContext() => [
                'general' => [
                    'security_key' => $fileContainer->getParameter('chamilo.configuration.general.security_key'),
                    'hashing_algorithm' => $fileContainer->getParameter(
                        'chamilo.configuration.general.hashing_algorithm'
                    ),
                    'install_date' => $fileContainer->getParameter('chamilo.configuration.general.install_date'),
                    'language' => $fileContainer->getParameter('chamilo.configuration.general.language'),
                    'theme' => $fileContainer->getParameter('chamilo.configuration.general.theme')
                ],
                'database' => $fileContainer->getParameter('chamilo.configuration.database'),
                'debug' => [
                    'show_errors' => $fileContainer->getParameter('chamilo.configuration.debug.show_errors'),
                    'enable_query_cache' => $fileContainer->getParameter(
                        'chamilo.configuration.debug.enable_query_cache'
                    )
                ],
                'storage' => $fileContainer->getParameter('chamilo.configuration.storage'),
                'session' => [
                    'session_handler' => $fileContainer->getParameter('chamilo.configuration.session.session_handler')
                ]
            ]
        ];

        if ($fileContainer->hasParameter('chamilo.configuration.error_handling'))
        {
            $settings[$this->getSettingsContext()]['error_handling'] = $fileContainer->getParameter(
                'chamilo.configuration.error_handling'
            );
        }

        return $settings;
    }
}
