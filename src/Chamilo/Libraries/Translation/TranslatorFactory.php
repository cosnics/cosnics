<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Configuration\Package\Service\InternationalizationBundlesCacheService;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\Path;
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
    public function __construct(\Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder)
    {
        $this->configurablePathBuilder = $configurablePathBuilder;
    }

    /**
     * Builds and returns the Symfony Translator
     *
     * @param string $locale
     * @return \Symfony\Component\Translation\Translator
     */
    public function createTranslator($locale = null)
    {
        if (! $locale)
        {
            // TODO: Do we still need this if the default is already passed on via the DI definition?
            $classnameUtilities = ClassnameUtilities::getInstance();
            $pathBuilder = new PathBuilder($classnameUtilities);
            $fileConfigurationConsulter = new ConfigurationConsulter(
                new FileConfigurationLoader(new FileConfigurationLocator($pathBuilder)));

            $locale = $fileConfigurationConsulter->getSetting(array('Chamilo\Configuration', 'general', 'language'));
        }

        $translator = new Translator($locale);

        $translator->addLoader('optimized', new OptimizedTranslationsPhpFileLoader());
        $this->addOptimizedTranslationResources($translator);

        $translator->setFallbackLocales(array('en', 'nl'));

        return $translator;
    }

    /**
     * Adds the optimized translation resources to the translator
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    protected function addOptimizedTranslationResources(Translator $translator)
    {
        $translationCachePath = $this->configurablePathBuilder->getCachePath(__NAMESPACE__);

        if (! is_dir($translationCachePath))
        {
            Filesystem::create_dir($translationCachePath);
        }

        $internationalizationBundlesCacheService = new InternationalizationBundlesCacheService();
        $packageNamespaces = $internationalizationBundlesCacheService->getAllPackages();

        $translationResourcesOptimizer = new TranslationResourcesOptimizer(
            array('xliff' => new XliffFileLoader(), 'ini' => new IniFileLoader()),
            new PackagesTranslationResourcesFinder(
                new PackagesFilesFinder(new PathBuilder(ClassnameUtilities::getInstance()), $packageNamespaces)),
            $translationCachePath);

        $resources = $translationResourcesOptimizer->getOptimizedTranslationResources();

        foreach ($resources as $locale => $resource)
        {
            $translator->addResource('optimized', $resource, $locale);
        }
    }
}