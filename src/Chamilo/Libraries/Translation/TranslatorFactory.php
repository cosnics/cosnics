<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Configuration\Service\InternationalizationBundlesCacheService;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\IniFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Translation
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

        if (!is_dir($translationCachePath))
        {
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

        foreach ($resources as $locale => $resource)
        {
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