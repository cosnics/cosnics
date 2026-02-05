<?php
namespace Chamilo\Core\Admin\Service\DataLoader;

use Chamilo\Core\Admin\Service\FileConfigurationLocator;
use Chamilo\Libraries\Storage\Architecture\Interface\CacheDataPreLoaderInterface;
use Chamilo\Libraries\Storage\Architecture\Trait\SimpleCacheAdapterHandlerTrait;
use Chamilo\Libraries\Storage\Architecture\Trait\SimpleCacheDataPreLoaderTrait;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @package Chamilo\Core\Admin\Service\DataLoader
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class FileConfigurationCacheDataPreLoader implements CacheDataPreLoaderInterface
{
    use SimpleCacheAdapterHandlerTrait;
    use SimpleCacheDataPreLoaderTrait;

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
        if ($this->getFileConfigurationLocator()->isAvailable()) {
            return $this->getFileSettings();
        }
        else {
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
        $xmlFileLoader = new YamlFileLoader(
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
        $xmlFileLoader = new YamlFileLoader(
            $fileContainer, new FileLocator($this->getFileConfigurationLocator()->getFilePath())
        );
        $xmlFileLoader->load($this->getFileConfigurationLocator()->getFileName());

        return $this->getSettingsFromContainer($fileContainer);
    }

    protected function getSettingsContext(): string
    {
        return 'Chamilo\Core\Admin';
    }

    /**
     * @return string[]
     */
    protected function getSettingsFromContainer(ContainerBuilder $fileContainer): array
    {
        $settings = [
            $this->getSettingsContext() => [
                'general' => [
                    'securityKey' => $fileContainer->getParameter('chamilo.configuration.general.securityKey'),
                    'hashingAlgorithm' => $fileContainer->getParameter(
                        'chamilo.configuration.general.hashingAlgorithm'
                    ),
                    'installDate' => $fileContainer->getParameter('chamilo.configuration.general.installDate'),
                    'language' => $fileContainer->getParameter('chamilo.configuration.general.language'),
                    'theme' => $fileContainer->getParameter('chamilo.configuration.general.theme')
                ],
                'database' => $fileContainer->getParameter('chamilo.configuration.database'),
                'debug' => [
                    'showErrors' => $fileContainer->getParameter('chamilo.configuration.debug.showErrors'),
                    'enableQueryCache' => $fileContainer->getParameter(
                        'chamilo.configuration.debug.enableQueryCache'
                    )
                ],
                'storage' => $fileContainer->getParameter('chamilo.configuration.storage')
            ]
        ];

        if ($fileContainer->hasParameter('chamilo.configuration.errorHandling')) {
            $settings[$this->getSettingsContext()]['errorHandling'] = $fileContainer->getParameter(
                'chamilo.configuration.errorHandling'
            );
        }

        return $settings;
    }
}
