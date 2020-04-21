<?php
namespace Chamilo\Libraries\Translation;

use InvalidArgumentException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Optimizes the translation resources
 *
 * @package Chamilo\Libraries\Translation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TranslationResourcesOptimizer
{

    /**
     * The translation loaders
     *
     * @var \Symfony\Component\Translation\Loader\LoaderInterface[]
     */
    private $translationLoaders;

    /**
     * The translation resources finder
     *
     * @var \Chamilo\Libraries\Translation\TranslationResourcesFinderInterface
     */
    private $translationResourcesFinder;

    /**
     * The optimized translations cache path
     *
     * @var string
     */
    private $optimizedTranslationsCachePath;

    /**
     * Constructor
     *
     * @param \Symfony\Component\Translation\Loader\LoaderInterface[] $translationLoaders
     * @param \Chamilo\Libraries\Translation\TranslationResourcesFinderInterface $translationResourcesFinder
     * @param string $optimizedTranslationsCachePath
     */
    public function __construct(array $translationLoaders,
        TranslationResourcesFinderInterface $translationResourcesFinder, $optimizedTranslationsCachePath = '')
    {
        $this->setTranslationLoaders($translationLoaders);
        $this->setTranslationResourcesFinder($translationResourcesFinder);
        $this->setOptimizedTranslationsCachePath($optimizedTranslationsCachePath);
    }

    /**
     *
     * @param \Symfony\Component\Translation\Loader\LoaderInterface[] $translationLoaders
     * @throws \InvalidArgumentException
     */
    public function setTranslationLoaders($translationLoaders)
    {
        if (empty($translationLoaders))
        {
            throw new InvalidArgumentException('You must provide at least one valid translation loader');
        }

        foreach ($translationLoaders as $translationLoader)
        {
            if (! $translationLoader instanceof LoaderInterface)
            {
                throw new InvalidArgumentException(
                    'The translation loader "' . get_class($translationLoader) .
                         '" must be an instance of \Symfony\Component\Translation\Loader\LoaderInterface');
            }
        }

        $this->translationLoaders = $translationLoaders;
    }

    /**
     *
     * @param \Chamilo\Libraries\Translation\TranslationResourcesFinderInterface $translationResourcesFinder
     * @throws \InvalidArgumentException
     */
    public function setTranslationResourcesFinder($translationResourcesFinder)
    {
        $this->translationResourcesFinder = $translationResourcesFinder;
    }

    /**
     *
     * @param string $optimizedTranslationsCachePath
     * @throws \InvalidArgumentException
     */
    public function setOptimizedTranslationsCachePath($optimizedTranslationsCachePath)
    {
        if (empty($optimizedTranslationsCachePath))
        {
            throw new InvalidArgumentException('You must provide a valid cache path');
        }

        $this->optimizedTranslationsCachePath = $optimizedTranslationsCachePath;
    }

    /**
     * Optimizes the translation resources and returns the paths to the optimized translation resources
     */
    public function getOptimizedTranslationResources()
    {
        $cachePath = $this->optimizedTranslationsCachePath;
        $optimizedTranslationsCache = $cachePath . '/locale.php';

        if (! file_exists($optimizedTranslationsCache))
        {
            return $this->optimizeResources($cachePath, $optimizedTranslationsCache);
        }
        else
        {
            return $this->retrieveOptimizedResources($cachePath, $optimizedTranslationsCache);
        }
    }

    /**
     * Optimizes the resources and returns the paths to the optimized resources
     *
     * @param string $cachePath
     * @param string $optimizedTranslationsCache
     * @return string[]
     */
    protected function optimizeResources($cachePath, $optimizedTranslationsCache)
    {
        $resources = array();

        $foundResources = $this->translationResourcesFinder->findTranslationResources();
        foreach ($foundResources as $locale => $localeFoundResources)
        {
            $messageCatalogue = new MessageCatalogue($locale);

            foreach ($localeFoundResources as $type => $localeTypeFoundResources)
            {
                $translationLoader = $this->determineLoaderByType($type);

                foreach ($localeTypeFoundResources as $domain => $resource)
                {
                    $messageCatalogue->addCatalogue($translationLoader->load($resource, $locale, $domain));
                }
            }

            $resourcePath = $cachePath . '/' . $locale . '.php';
            file_put_contents($resourcePath, "<?php\n\nreturn " . var_export($messageCatalogue->all(), true) . ";\n");

            $resources[$locale] = $resourcePath;
        }

        file_put_contents(
            $optimizedTranslationsCache,
            "<?php\n\nreturn " . var_export(array_keys($resources), true) . ";\n");

        return $resources;
    }

    /**
     * Retrieves the optimized resources from the cache
     *
     * @param string $cachePath
     * @param string $optimizedTranslationsCache
     * @return string[]
     */
    protected function retrieveOptimizedResources($cachePath, $optimizedTranslationsCache)
    {
        $resources = array();

        $locales = require ($optimizedTranslationsCache);
        foreach ($locales as $locale)
        {
            $resources[$locale] = $cachePath . '/' . $locale . '.php';
        }

        return $resources;
    }

    /**
     * Determines a loader by a given type
     *
     * @param string $type
     * @throws \InvalidArgumentException
     * @return \Symfony\Component\Translation\Loader\LoaderInterface
     */
    protected function determineLoaderByType($type)
    {
        if (! array_key_exists($type, $this->translationLoaders))
        {
            throw new InvalidArgumentException(
                'The given type "' . $type . '" is not supported by the current loaders. ' .
                     'Please add the loader for this type or choose between "' .
                     implode(', ', array_keys($this->translationLoaders)));
        }

        return $this->translationLoaders[$type];
    }
}