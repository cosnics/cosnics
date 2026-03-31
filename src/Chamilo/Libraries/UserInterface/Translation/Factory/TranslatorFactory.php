<?php
namespace Chamilo\Libraries\UserInterface\Translation\Factory;

use Chamilo\Core\Admin\Service\Finder\TranslationBundlesGenerator;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\UserInterface\Translation\Service\OptimizedTranslationsPhpFileLoader;
use Chamilo\Libraries\UserInterface\Translation\Service\PackagesTranslationResourcesFinder;
use Chamilo\Libraries\UserInterface\Translation\Service\TranslationResourcesOptimizer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Translation\Factory
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslatorFactory
{
    public function __construct(
        protected Filesystem $filesystem, protected ConfigurablePathBuilder $configurablePathBuilder,
        protected SystemPathBuilder $systemPathBuilder,
        protected TranslationBundlesGenerator $internationalizationBundlesGenerator
    )
    {
    }

    protected function addOptimizedTranslationResources(Translator $translator): void
    {
        $packageNamespaces = $this->internationalizationBundlesGenerator->getPackageNamespaces();

        $translationCachePath = $this->getTranslationCachePath();

        if (!is_dir($translationCachePath)) {
            $this->filesystem->mkdir($translationCachePath);
        }

        $translationResourcesOptimizer = new TranslationResourcesOptimizer(
            new YamlFileLoader(), new PackagesTranslationResourcesFinder(
            new PackagesFilesFinder(
                $this->systemPathBuilder, $packageNamespaces
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

    public function getTranslationCachePath(): string
    {
        return $this->configurablePathBuilder->getCachePath(__NAMESPACE__);
    }
}