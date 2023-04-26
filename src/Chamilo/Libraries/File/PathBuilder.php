<?php
namespace Chamilo\Libraries\File;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package    Chamilo\Libraries\File
 * @author     Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author     Sven Vanpoucke <sven.vanpoucke@hogent.be>
 * @author     Magali Gillard <magali.gillard@ehb.be>
 * @author     Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use SystemPathBuilder or WebPathBuilder now
 */
class PathBuilder
{
    protected static ?PathBuilder $instance = null;

    protected SystemPathBuilder $systemPathBuilder;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(SystemPathBuilder $systemPathBuilder, WebPathBuilder $webPathBuilder)
    {
        $this->systemPathBuilder = $systemPathBuilder;
        $this->webPathBuilder = $webPathBuilder;
    }

    /**
     * @deprecated Use SystemPathBuilder::getBasePath or WebPathBuilder::getBasePath
     */
    public function getBasePath(bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getBasePath() : $this->getSystemPathBuilder()->getBasePath();
    }

    /**
     * @deprecated Use SystemPathBuilder::getConfigurationPath or WebPathBuilder::getConfigurationPath
     */
    public function getConfigurationPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getConfigurationPath($namespace) :
            $this->getSystemPathBuilder()->getConfigurationPath($namespace);
    }

    /**
     * @deprecated Use SystemPathBuilder::getCssPath or WebPathBuilder::getCssPath
     */
    public function getCssPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getCssPath($namespace) :
            $this->getSystemPathBuilder()->getCssPath($namespace);
    }

    /**
     * @deprecated Use SystemPathBuilder::getI18nPath or WebPathBuilder::getI18nPath
     */
    public function getI18nPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getI18nPath($namespace) :
            $this->getSystemPathBuilder()->getI18nPath($namespace);
    }

    /**
     * @deprecated Use SystemPathBuilder::getImagesPath or WebPathBuilder::getImagesPath
     */
    public function getImagesPath(string $namespace = StringUtilities::LIBRARIES, bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getImagesPath($namespace) :
            $this->getSystemPathBuilder()->getImagesPath($namespace);
    }

    public static function getInstance(): ?PathBuilder
    {
        if (is_null(static::$instance))
        {
            $classnameUtilities = ClassnameUtilities::getInstance();

            static::$instance = new static(
                new SystemPathBuilder($classnameUtilities),
                new WebPathBuilder($classnameUtilities, ChamiloRequest::createFromGlobals())
            );
        }

        return static::$instance;
    }

    /**
     * @deprecated Use SystemPathBuilder::getJavascriptPath or WebPathBuilder::getJavascriptPath
     */
    public function getJavascriptPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getJavascriptPath($namespace) :
            $this->getSystemPathBuilder()->getJavascriptPath($namespace);
    }

    /**
     * @deprecated Use SystemPathBuilder::getPluginPath or WebPathBuilder::getPluginPath
     */
    public function getPluginPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getPluginPath($namespace) :
            $this->getSystemPathBuilder()->getPluginPath($namespace);
    }

    /**
     * @deprecated Use SystemPathBuilder::getPublicStoragePath or WebPathBuilder::getPublicStoragePath
     */
    public function getPublicStoragePath(string $namespace = null, bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getResourcesPath($namespace) :
            $this->getSystemPathBuilder()->getResourcesPath($namespace);
    }

    /**
     * @deprecated Use SystemPathBuilder::getResourcesPath or WebPathBuilder::getResourcesPath
     */
    public function getResourcesPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getResourcesPath($namespace) :
            $this->getSystemPathBuilder()->getResourcesPath($namespace);
    }

    /**
     * @deprecated Use SystemPathBuilder::getStoragePath
     */
    public function getStoragePath(?string $namespace = null): string
    {
        return $this->getSystemPathBuilder()->getStoragePath($namespace);
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    /**
     * @deprecated Use SystemPathBuilder::getTemplatesPath or WebPathBuilder::getTemplatesPath
     */
    public function getTemplatesPath(string $namespace = 'Chamilo\Configuration', bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->getTemplatesPath($namespace) :
            $this->getSystemPathBuilder()->getTemplatesPath($namespace);
    }

    /**
     * @throws \Exception
     * @deprecated Use SystemPathBuilder::getVendorPath
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

    /**
     * @deprecated Use WebPathBuilder::isWebUri
     */
    public function isWebUri(string $uri): bool
    {
        return $this->getWebPathBuilder()->isWebUri($uri);
    }

    /**
     * @deprecated Use SystemPathBuilder::namespaceToFullPath or WebPathBuilder::namespaceToFullPath
     */
    public function namespaceToFullPath(?string $namespace = null, bool $web = false): string
    {
        return $web ? $this->getWebPathBuilder()->namespaceToFullPath($namespace) :
            $this->getSystemPathBuilder()->namespaceToFullPath($namespace);
    }
}
