<?php
namespace Chamilo\Core\Repository\Publication\Factory;

use Doctrine\Common\Cache\ArrayCache;

/**
 * @package Chamilo\Core\Repository\Publication\Factory
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRepositoryCacheProviderFactory
{

    /**
     * @return \Doctrine\Common\Cache\ArrayCache
     */
    public function getPublicationTargetRepositoryCacheProvider()
    {
        return new ArrayCache();
    }
}
