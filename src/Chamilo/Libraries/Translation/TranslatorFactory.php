<?php
namespace Chamilo\Libraries\Translation;

use Chamilo\Configuration\Package\Finder\BasicBundles;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PackagesContentFinder\PackagesFilesFinder;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\Translation\Loader\IniFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;

/**
 * Builds the symfony translator
 *
 * @package common\libraries
 * @author Sven Vanpoucke - Hogeschool Gent
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
            $isoCode = Translation :: getInstance()->getLanguageIsocode();
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
        $translationCachePath = Path :: getInstance()->getCachePath() . 'translation';
        if (! is_dir($translationCachePath))
        {
            Filesystem :: create_dir($translationCachePath);
        }

        $packageBundles = new BasicBundles(PackageList :: ROOT);
        $packageNamespaces = $packageBundles->getPackageNamespaces();

        $translationResourcesOptimizer = new TranslationResourcesOptimizer(
            array('xliff' => new XliffFileLoader(), 'ini' => new IniFileLoader()),
            new PackagesTranslationResourcesFinder(new PackagesFilesFinder(Path :: getInstance(), $packageNamespaces)),
            $translationCachePath);

        $resources = $translationResourcesOptimizer->getOptimizedTranslationResources();

        foreach ($resources as $locale => $resource)
        {
            $translator->addResource('optimized', $resource, $locale);
        }
    }
}