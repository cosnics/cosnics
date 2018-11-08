<?php
namespace Chamilo\Core\Repository\Publication\Factory;

use Symfony\Component\Cache\Simple\ArrayCache;

/**
 * @package Chamilo\Core\Repository\Publication\Factory
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRepositoryCacheProviderFactory
{

    /**
     * @return \Symfony\Component\Cache\Simple\ArrayCache
     */
    public function getPublicationTargetRepositoryCacheProvider()
    {
        return new ArrayCache();
    }
}
