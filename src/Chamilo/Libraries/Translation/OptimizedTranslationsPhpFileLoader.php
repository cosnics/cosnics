<?php
namespace Chamilo\Libraries\Translation;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Loads optimized translations into a message catalog.
 * This loader does not take the given domain into account and
 * loads every translation from the given resource into the message catalogue.
 *
 * @package Chamilo\Libraries\Translation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OptimizedTranslationsPhpFileLoader implements LoaderInterface
{

    /**
     * Loads a locale.
     *
     * @param mixed $resource A resource
     * @param string $locale A locale
     * @param string $domain The domain
     *
     * @return \Symfony\Component\Translation\MessageCatalogue A MessageCatalogue instance
     * @throws \Symfony\Component\Translation\Exception\NotFoundResourceException when the resource cannot be found
     * @throws \Symfony\Component\Translation\Exception\InvalidResourceException when the resource cannot be loaded
     */
    public function load($resource, $locale, $domain = 'messages')
    {
        if (!stream_is_local($resource))
        {
            throw new InvalidResourceException(sprintf('This is not a local file "%s".', $resource));
        }

        if (!file_exists($resource))
        {
            throw new NotFoundResourceException(sprintf('File "%s" not found.', $resource));
        }

        $messages = require($resource);

        return new MessageCatalogue($locale, $messages);
    }
}