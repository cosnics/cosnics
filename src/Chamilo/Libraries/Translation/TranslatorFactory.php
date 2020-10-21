<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Configuration\Package\Service\InternationalizationBundlesCacheService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\PathBuilder;
use Symfony\Component\Translation\Loader\IniFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Builds the symfony translator
 *
 * @package Chamilo\Libraries\Translation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslatorFactory
{
    /**
     * @var \Chamilo\Libraries\File\ConfigurablePathBuilder
     */
    protected $configurablePathBuilder;

    /**
     * TranslatorFactory constructor.
     *
     * @param \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
     */
    public function __construct(ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * Adds the optimized translation resources to the translator
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    protected function addOptimizedTranslationResources(Translator $translator)
    {
        $translationCachePath = $this->configurablePathBuilder->getCachePath(__NAMESPACE__);

        if (!is_dir($translationCachePath))
        {
            Filesystem::create_dir($translationCachePath);
        }

        $internationalizationBundlesCacheService = new InternationalizationBundlesCacheService();
        $packageNamespaces = $internationalizationBundlesCacheService->getAllPackages();

        $translationResourcesOptimizer = new TranslationResourcesOptimizer(
            array('xliff' => new XliffFileLoader(), 'ini' => new IniFileLoader()),
            new PackagesTranslationResourcesFinder(
                new PackagesFilesFinder(new PathBuilder(ClassnameUtilities::getInstance()), $packageNamespaces)
            ), $translationCachePath
        );

        $resources = $translationResourcesOptimizer->getOptimizedTranslationResources();

        foreach ($resources as $locale => $resource)
        {
            $translator->addResource('optimized', $resource, $locale);
        }
    }

    /**
     * Builds and returns the Symfony Translator
     *
     * @param string $locale
     *
     * @return \Symfony\Component\Translation\Translator
     */
    public function createTranslator($locale = null)
    {
        $translator = new Translator($locale);

        $translator->addLoader('optimized', new OptimizedTranslationsPhpFileLoader());
        $this->addOptimizedTranslationResources($translator);

        $translator->setFallbackLocales(array('en', 'nl'));

        return $translator;
    }
}