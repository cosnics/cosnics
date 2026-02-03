<?php
namespace Chamilo\Libraries\UserInterface\Translation\Service;

use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Loads optimized translations into a message catalogue.
 * This loader does not take the given domain into account and
 * loads every translation from the given resource into the message catalogue.
 *
 * @package Chamilo\Libraries\UserInterface\Translation\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class OptimizedTranslationsPhpFileLoader implements LoaderInterface
{
    /**
     * @param mixed $resource
     * @param string $locale
     * @param string $domain
     *
     * @return \Symfony\Component\Translation\MessageCatalogue
     */
    public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        if (!stream_is_local($resource)) {
            throw new InvalidResourceException(sprintf('This is not a local file "%s".', $resource));
        }

        if (!file_exists($resource)) {
            throw new NotFoundResourceException(sprintf('File "%s" not found.', $resource));
        }

        $messages = require($resource);

        return new MessageCatalogue($locale, $messages);
    }
}