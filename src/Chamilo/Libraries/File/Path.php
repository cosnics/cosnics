<?php
namespace Chamilo\Libraries\File;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Configuration\Service\DataLoader\FileConfigurationCacheDataPreLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Exception;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * @package    Chamilo\Libraries\File
 * @author     Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author     Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author     Magali Gillard <magali.gillard@ehb.be>
 * @author     Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use the SystemPathBuilder and WebPathBuilder now
 */
class Path
{
    protected static ?Path $instance = null;

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected SystemPathBuilder $systemPathBuilder;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        SystemPathBuilder $systemPathBuilder, WebPathBuilder $webPathBuilder,
        ConfigurablePathBuilder $configurablePathBuilder
    )
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->webPathBuilder = $webPathBuilder;
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * @return string
     * @deprecated Use the ConfigurablePathBuilder service now
     */
    public function getArchivePath(): string
    {
        return $this->getConfigurablePathBuilder()->getArchivePath();
    }

    public function getBasePath(bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getBasePath() : $this->getSystemPathBuilder()->getBasePath();
    }

    /**
     * @deprecated Use the ConfigurablePath service now
     */
    public function getCachePath(?string $namespace = null): string
    {
        return $this->getConfigurablePathBuilder()->getCachePath($namespace);
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getConfigurationPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getConfigurationPath($namespace) :
            $this->getSystemPathBuilder()->getConfigurationPath($namespace);
    }

    /**
     * @param string $namespace
     * @param bool $web
     *
     * @return string
     */
    public function getI18nPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getI18nPath($namespace) :
            $this->getSystemPathBuilder()->getI18nPath($namespace);
    }

    public static function getInstance(): Path
    {
        if (is_null(static::$instance))
        {
            $classnameUtilities = ClassnameUtilities::getInstance();

            $systemPathBuilder = new SystemPathBuilder($classnameUtilities);

            $fileConfigurationLocator = new FileConfigurationLocator($systemPathBuilder);

            $fileConfigurationConsulter = new ConfigurationConsulter(
                new FileConfigurationCacheDataPreLoader(new ArrayAdapter(), $fileConfigurationLocator)
            );

            $configurablePathBuilder = new ConfigurablePathBuilder(
                $fileConfigurationConsulter->getSetting(['Chamilo\Configuration', 'storage'])
            );

            static::$instance = new static(
                new SystemPathBuilder($classnameUtilities),
                new WebPathBuilder($classnameUtilities, ChamiloRequest::createFromGlobals()), $configurablePathBuilder
            );
        }

        return static::$instance;
    }

    public function getJavascriptPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getJavascriptPath($namespace) :
            $this->getSystemPathBuilder()->getJavascriptPath($namespace);
    }

    /**
     * @deprecated Use the ConfigurablePathBuilder service now
     */
    public function getLogPath(): string
    {
        return $this->getConfigurablePathBuilder()->getLogPath();
    }

    public function getPluginPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getPluginPath($namespace) :
            $this->getSystemPathBuilder()->getPluginPath($namespace);
    }

    /**
     * @deprecated Use the ConfigurablePathBuilder service now
     */
    public function getProfilePicturePath(): string
    {
        return $this->getConfigurablePathBuilder()->getProfilePicturePath();
    }

    public function getPublicStoragePath(?string $namespace = null, bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getResourcesPath($namespace) :
            $this->getSystemPathBuilder()->getResourcesPath($namespace);
    }

    /**
     * @deprecated Use the ConfigurablePathBuilder service now
     */
    public function getRepositoryPath(): string
    {
        return $this->getConfigurablePathBuilder()->getRepositoryPath();
    }

    public function getResourcesPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getResourcesPath($namespace) :
            $this->getSystemPathBuilder()->getResourcesPath($namespace);
    }

    public function getStoragePath(?string $namespace = null): string
    {
        return $this->getSystemPathBuilder()->getStoragePath($namespace);
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    /**
     * @deprecated Use the ConfigurablePathBuilder service now
     */
    public function getTemporaryPath(?string $namespace = null): string
    {
        return $this->getConfigurablePathBuilder()->getTemporaryPath($namespace);
    }

    /**
     * @throws \Exception
     */
    public function getVendorPath(bool $web = false): string
    {
        if ($web)
        {
            throw new Exception('Storage is not directly accessible');
        }

        return $this->getSystemPathBuilder()->getVendorPath();
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    public function isWebUri(string $uri): bool
    {
        return $this->getWebPathBuilder()->isWebUri($uri);
    }

    public function namespaceToFullPath(?string $namespace = null, bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->namespaceToFullPath($namespace) :
            $this->getSystemPathBuilder()->namespaceToFullPath($namespace);
    }
}
