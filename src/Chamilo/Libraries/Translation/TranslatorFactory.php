<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Configuration\Package\Service\InternationalizationBundlesCacheService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Symfony\Component\Translation\Loader\IniFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Builds the symfony translator
 *
 * @package Chamilo\Libraries\Translation
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslatorFactory
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected InternationalizationBundlesCacheService $internationalizationBundlesCacheService;

    public function __construct(
        ConfigurablePathBuilder $configurablePathBuilder,
        InternationalizationBundlesCacheService $internationalizationBundlesCacheService
    )
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->internationalizationBundlesCacheService = $internationalizationBundlesCacheService;
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function addOptimizedTranslationResources(Translator $translator)
    {
        $packageNamespaces = $this->getInternationalizationBundlesCacheService()->getPackageNamespaces();

        $translationCachePath = $this->getTranslationCachePath();

        if (!is_dir($translationCachePath))
        {
            Filesystem::create_dir($translationCachePath);
        }

        $translationResourcesOptimizer = new TranslationResourcesOptimizer(
            ['xliff' => new XliffFileLoader(), 'ini' => new IniFileLoader()], new PackagesTranslationResourcesFinder(
            new PackagesFilesFinder(
                new PathBuilder(ClassnameUtilities::getInstance(), ChamiloRequest::createFromGlobals()),
                $packageNamespaces
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

    public function getInternationalizationBundlesCacheService(): InternationalizationBundlesCacheService
    {
        return $this->internationalizationBundlesCacheService;
    }

    public function getTranslationCachePath(): string
    {
        return $this->getConfigurablePathBuilder()->getCachePath(__NAMESPACE__);
    }
}