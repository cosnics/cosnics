<?php
namespace Chamilo\Libraries\DependencyInjection\Service;

use Chamilo\Core\Admin\Service\Finder\PackageBundlesGenerator;
use Chamilo\Core\Admin\Service\PackageBundlesCacheService;
use Chamilo\Core\Admin\Service\PackageFactory;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ContainerExtensionFinderInterface;
use Chamilo\Libraries\DependencyInjection\Architecture\Interface\ICompilerPassExtension;
use Chamilo\Libraries\Filesystem\Service\PackagesContentFinder\PackagesClassFinder;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Utilities\ClassnameUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @package Chamilo\Libraries\DependencyInjection\Service
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyInjectionContainerBuilder
{
    private static ?ContainerInterface $container = null;

    private static ?DependencyInjectionContainerBuilder $instance = null;

    protected Filesystem $filesystem;

    protected ChamiloRequest $request;

    protected SystemPathBuilder $systemPathBuilder;

    protected WebPathBuilder $webPathBuilder;

    private ?ContainerBuilder $builder;

    private ?string $cacheClass;

    private ?string $cacheFile;

    private ClassnameUtilities $classnameUtilities;

    private ?ContainerExtensionFinderInterface $containerExtensionFinder;

    private StringUtilities $stringUtilities;

    public function __construct(
        ?ContainerBuilder $builder = null, ?ContainerExtensionFinderInterface $containerExtensionFinder = null,
        ?string $cacheFile = null, string $cacheClass = 'ChamiloContainer'
    )
    {
        $this->setBuilder($builder);

        if (is_null($cacheFile)) {
            $cacheFile = $this->getDefaultCacheFilePath();
        }

        $this->cacheFile = $cacheFile;
        $this->cacheClass = $cacheClass;
        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    protected function cacheContainer(ContainerBuilder $container, string $cacheFile): void
    {
        if (!is_dir(dirname($cacheFile))) {
            $this->getFilesystem()->mkdir(dirname($cacheFile));
        }

        $dumper = new PhpDumper($container);
        file_put_contents($cacheFile, $dumper->dump(['class' => $this->cacheClass]));
    }

    public function clearContainerInstance(): void
    {
        self::$container = null;
    }

    /**
     * Creates and returns the default dependency injection container for Chamilo
     */
    public function createContainer(): ContainerInterface
    {
        if (self::$container instanceof ContainerInterface) {
            return self::$container;
        }

        if (file_exists($this->cacheFile)) {
            require_once $this->cacheFile;
            $container = new $this->cacheClass();
        }
        else {
            $container = $this->builder ?: new ContainerBuilder();
            $this->loadContainerExtensions($container);
            $container->compile();

            $this->cacheContainer($container, $this->cacheFile);
        }

        self::$container = $container;

        return $container;
    }

    protected function getClassnameUtilities(): ClassnameUtilities
    {
        if (!isset($this->classnameUtilities)) {
            $this->classnameUtilities = new ClassnameUtilities($this->getStringUtilities());
        }

        return $this->classnameUtilities;
    }

    public function getContainerExtensionFinder(): ContainerExtensionFinderInterface
    {
        if (!isset($this->containerExtensionFinder)) {
            $packageNamespaces = $this->getPackageNamespaces();

            $this->containerExtensionFinder = new PackagesContainerExtensionFinder(
                new PackagesClassFinder($this->getSystemPathBuilder(), $packageNamespaces)
            );
        }

        return $this->containerExtensionFinder;
    }

    public function setContainerExtensionFinder(?ContainerExtensionFinderInterface $containerExtensionFinder = null
    ): void
    {
        $this->containerExtensionFinder = $containerExtensionFinder;
    }

    protected function getDefaultCacheFilePath(): string
    {
        return realpath(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
            ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'symfony' . DIRECTORY_SEPARATOR .
            'DependencyInjection.php';
    }

    protected function getDefaultLogsPath(): string
    {
        return realpath(
                __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR
            ) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
    }

    protected function getFilesystem(): Filesystem
    {
        if (!isset($this->filesystem)) {
            $this->filesystem = new Filesystem();
        }

        return $this->filesystem;
    }

    public static function getInstance(): DependencyInjectionContainerBuilder
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    protected function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        $packageFactory = new PackageFactory($this->getSystemPathBuilder(), $this->getFilesystem());

        $packageBundlesGenerator = new PackageBundlesGenerator(
            $this->getSystemPathBuilder(), $this->getClassnameUtilities(), $packageFactory
        );

        return new PackageBundlesCacheService(new ArrayAdapter(), $packageBundlesGenerator);
    }

    /**
     * @return string[]
     */
    protected function getPackageNamespaces(): array
    {
        return $this->getPackageNamespacesFromFilesystem();
    }

    /**
     * @return string[]
     */
    protected function getPackageNamespacesFromFilesystem(): array
    {
        return array_keys($this->getPackageBundlesCacheService()->getPackages());
    }

    protected function getRequest(): ChamiloRequest
    {
        if (!isset($this->request)) {
            $this->request = ChamiloRequest::createFromGlobals();
        }

        return $this->request;
    }

    protected function getStringUtilities(): StringUtilities
    {
        if (!isset($this->stringUtilities)) {
            $this->stringUtilities = new StringUtilities();
        }

        return $this->stringUtilities;
    }

    protected function getSystemPathBuilder(): SystemPathBuilder
    {
        if (!isset($this->systemPathBuilder)) {
            $this->systemPathBuilder = new SystemPathBuilder(
                new ClassnameUtilities($this->getStringUtilities())
            );
        }

        return $this->systemPathBuilder;
    }

    protected function getWebPathBuilder(): WebPathBuilder
    {
        if (!isset($this->webPathBuilder)) {
            $this->webPathBuilder = new WebPathBuilder(
                new ClassnameUtilities($this->getStringUtilities()), $this->getRequest()
            );
        }

        return $this->webPathBuilder;
    }

    /**
     * Loads the extensions for the container
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function loadContainerExtensions(ContainerBuilder $container): void
    {
        $extensionClasses = $this->getContainerExtensionFinder()->findContainerExtensions();
        $extensions = [];

        foreach ($extensionClasses as $extensionClass) {
            /** @var \Symfony\Component\DependencyInjection\Extension\ExtensionInterface $extension */
            $extension = new $extensionClass();

            $container->registerExtension($extension);
            $container->loadFromExtension($extension->getAlias());

            $extensions[] = $extension;
        }

        foreach ($extensions as $extension) {
            if ($extension instanceof ICompilerPassExtension) {
                /** @var ICompilerPassExtension $extension */
                $extension->registerCompilerPasses($container);
            }
        }
    }

    public function rebuildContainer(
        ?ContainerBuilder $builder = null, ?ContainerExtensionFinderInterface $containerExtensionFinder = null,
        ?string $cacheFile = null, string $cacheClass = 'ChamiloContainer'
    ): DependencyInjectionContainerBuilder
    {
        $this->removeContainerCache();
        $this->clearContainerInstance();

        self::$instance = $newContainer = new self($builder, $containerExtensionFinder, $cacheFile, $cacheClass);
        $newContainer->createContainer();

        return $newContainer;
    }

    public function removeContainerCache(): void
    {
        if (file_exists($this->cacheFile)) {
            $this->getFilesystem()->remove($this->cacheFile);

            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($this->cacheFile);
            }
        }
    }

    public function setBuilder(?ContainerBuilder $builder = null): void
    {
        $this->builder = $builder;
    }
}