<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\Translation\Loader\IniFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Chamilo\Configuration\Configuration;

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
     * Builds and returns the Symfony Translator
     *
     * @param string $locale
     *
     * @return \Symfony\Component\Translation\Translator
     */
    public function createTranslator($locale = null)
    {
        if (! $locale)
        {
            $isoCode = Translation::getInstance()->getLanguageIsocode();
            $locale = $isoCode . '_' . strtoupper($isoCode);
        }

        $translator = new Translator($locale);

        $translator->addLoader('optimized', new OptimizedTranslationsPhpFileLoader());
        $this->addOptimizedTranslationResources($translator);

        $translator->setFallbackLocales(array('en_EN', 'nl_NL'));

        return $translator;
    }

    /**
     * Adds the optimized translation resources to the translator
     *
     * @param Translator $translator
     */
    protected function addOptimizedTranslationResources(Translator $translator)
    {
        $translationCachePath = Path::getInstance()->getCachePath(__NAMESPACE__);

        if (! is_dir($translationCachePath))
        {
            Filesystem::create_dir($translationCachePath);
        }

        $packageNamespaces = Configuration::get_instance()->get_registration_contexts();

        $translationResourcesOptimizer = new TranslationResourcesOptimizer(
            array('xliff' => new XliffFileLoader(), 'ini' => new IniFileLoader()),
            new PackagesTranslationResourcesFinder(new PackagesFilesFinder(Path::getInstance(), $packageNamespaces)),
            $translationCachePath);

        $resources = $translationResourcesOptimizer->getOptimizedTranslationResources();

        foreach ($resources as $locale => $resource)
        {
            $translator->addResource('optimized', $resource, $locale);
        }
    }
}