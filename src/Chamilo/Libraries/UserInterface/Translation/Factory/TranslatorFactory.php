<?php
namespace Chamilo\Libraries\UserInterface\Translation\Factory;

use Chamilo\Core\Admin\Service\InternationalizationBundlesCacheService;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\UserInterface\Translation\Service\OptimizedTranslationsPhpFileLoader;
use Chamilo\Libraries\UserInterface\Translation\Service\PackagesTranslationResourcesFinder;
use Chamilo\Libraries\UserInterface\Translation\Service\TranslationResourcesOptimizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\IniFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Translation\Factory
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslatorFactory
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected Filesystem $filesystem;

    protected InternationalizationBundlesCacheService $internationalizationBundlesCacheService;

    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(
        Filesystem $filesystem, ConfigurablePathBuilder $configurablePathBuilder,
        InternationalizationBundlesCacheService $internationalizationBundlesCacheService,
        SystemPathBuilder $systemPathBuilder
    )
    {
        $this->filesystem = $filesystem;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->internationalizationBundlesCacheService = $internationalizationBundlesCacheService;
        $this->systemPathBuilder = $systemPathBuilder;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function addOptimizedTranslationResources(Translator $translator): void
    {
        $packageNamespaces = $this->getInternationalizationBundlesCacheService()->getPackageNamespaces();

        $translationCachePath = $this->getTranslationCachePath();

        if (!is_dir($translationCachePath)) {
            $this->getFilesystem()->mkdir($translationCachePath);
        }

        $translationResourcesOptimizer = new TranslationResourcesOptimizer(
            new IniFileLoader(), new PackagesTranslationResourcesFinder(
            new PackagesFilesFinder(
                $this->getSystemPathBuilder(), $packageNamespaces
            )
        ), $translationCachePath
        );

        $resources = $translationResourcesOptimizer->getOptimizedTranslationResources();

        foreach ($resources as $locale => $resource) {
            $translator->addResource('optimized', $resource, $locale);
        }
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function createTranslator(?string $locale = null, array $fallbackLanguages = []): Translator
    {
        $translator = new Translator($locale);

        $translator->addLoader('optimized', new OptimizedTranslationsPhpFileLoader());
        $this->addOptimizedTranslationResources($translator);

        $translator->setFallbackLocales($fallbackLanguages);

        return $translator;
    }

    public function getConfigurablePathBuilder(): ConfigurablePathBuilder
    {
        return $this->configurablePathBuilder;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function getInternationalizationBundlesCacheService(): InternationalizationBundlesCacheService
    {
        return $this->internationalizationBundlesCacheService;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    public function getTranslationCachePath(): string
    {
        return $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__);
    }
}