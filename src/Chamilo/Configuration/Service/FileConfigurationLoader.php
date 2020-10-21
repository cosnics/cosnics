<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class FileConfigurationLoader implements CacheableDataLoaderInterface
{

    /**
     * @var string[]
     */
    protected static $dataCache;

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    private $fileConfigurationLocator;

    /**
     *
     * @param FileConfigurationLocator $fileConfigurationLocator
     */
    public function __construct(FileConfigurationLocator $fileConfigurationLocator)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
    }

    public function clearData()
    {
        return true;
    }

    /**
     *
     * @return string[]
     * @throws \Exception
     */
    public function getData()
    {
        if (!isset(self::$dataCache))
        {
            if ($this->getFileConfigurationLocator()->isAvailable())
            {
                self::$dataCache = $this->getSettings();
            }
            else
            {
                self::$dataCache = $this->getDefaultSettings();
            }
        }

        return self::$dataCache;
    }

    /**
     *
     * @return string[]
     * @throws \Exception
     */
    protected function getDefaultSettings()
    {
        $fileContainer = new ContainerBuilder();
        $xmlFileLoader = new XmlFileLoader(
            $fileContainer, new FileLocator($this->getFileConfigurationLocator()->getDefaultFilePath())
        );
        $xmlFileLoader->load($this->getFileConfigurationLocator()->getDefaultFileName());

        return $this->getSettingsFromContainer($fileContainer);
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    public function getFileConfigurationLocator()
    {
        return $this->fileConfigurationLocator;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLocator $fileConfigurationLocator
     */
    public function setFileConfigurationLocator(FileConfigurationLocator $fileConfigurationLocator)
    {
        $this->fileConfigurationLocator = $fileConfigurationLocator;
    }

    /**
     *
     * @return string
     */
    public function getIdentifier()
    {
        return md5(__CLASS__);
    }

    /**
     *
     * @return string[]
     * @throws \Exception
     */
    protected function getSettings()
    {
        $fileContainer = new ContainerBuilder();
        $xmlFileLoader = new XmlFileLoader(
            $fileContainer, new FileLocator($this->getFileConfigurationLocator()->getFilePath())
        );
        $xmlFileLoader->load($this->getFileConfigurationLocator()->getFileName());

        return $this->getSettingsFromContainer($fileContainer);
    }

    /**
     *
     * @return string
     */
    protected function getSettingsContext()
    {
        return 'Chamilo\Configuration';
    }

    /**
     *
     * @param ContainerBuilder $fileContainer
     *
     * @return string[]
     */
    protected function getSettingsFromContainer(ContainerBuilder $fileContainer)
    {
        $settings = array(
            $this->getSettingsContext() => array(
                'general' => array(
                    'security_key' => $fileContainer->getParameter('chamilo.configuration.general.security_key'),
                    'hashing_algorithm' => $fileContainer->getParameter(
                        'chamilo.configuration.general.hashing_algorithm'
                    ),
                    'install_date' => $fileContainer->getParameter('chamilo.configuration.general.install_date'),
                    'language' => $fileContainer->getParameter('chamilo.configuration.general.language'),
                    'theme' => $fileContainer->getParameter('chamilo.configuration.general.theme')
                ),
                'database' => $fileContainer->getParameter('chamilo.configuration.database'),
                'debug' => array(
                    'show_errors' => $fileContainer->getParameter('chamilo.configuration.debug.show_errors'),
                    'enable_query_cache' => $fileContainer->getParameter(
                        'chamilo.configuration.debug.enable_query_cache'
                    )
                ),
                'storage' => $fileContainer->getParameter('chamilo.configuration.storage'),
                'session' => array(
                    'session_handler' => $fileContainer->getParameter('chamilo.configuration.session.session_handler')
                )
            )
        );

        if ($fileContainer->hasParameter('chamilo.configuration.error_handling'))
        {
            $settings[$this->getSettingsContext()]['error_handling'] = $fileContainer->getParameter(
                'chamilo.configuration.error_handling'
            );
        }

        return $settings;
    }
}
