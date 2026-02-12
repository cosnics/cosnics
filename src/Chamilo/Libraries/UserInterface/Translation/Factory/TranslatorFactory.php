<?php
namespace Chamilo\Libraries\UserInterface\Translation\Factory;

use Chamilo\Core\Admin\Service\Finder\InternationalizationBundlesGenerator;
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

    protected InternationalizationBundlesGenerator $internationalizationBundlesGenerator;

    protected SystemPathBuilder $systemPathBuilder;

    public function __construct(
        Filesystem $filesystem, ConfigurablePathBuilder $configurablePathBuilder, SystemPathBuilder $systemPathBuilder,
        InternationalizationBundlesGenerator $internationalizationBundlesGenerator
    )
    {
        $this->filesystem = $filesystem;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->internationalizationBundlesGenerator = $internationalizationBundlesGenerator;
    }

    protected function addOptimizedTranslationResources(Translator $translator): void
    {
        $packageNamespaces = $this->getInternationalizationBundlesGenerator()->getPackageNamespaces();

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

    public function getInternationalizationBundlesGenerator(): InternationalizationBundlesGenerator
    {
        return $this->internationalizationBundlesGenerator;
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