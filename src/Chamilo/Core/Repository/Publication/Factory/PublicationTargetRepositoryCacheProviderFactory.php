<?php
namespace Chamilo\Core\Repository\Publication\Factory;

use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * @package Chamilo\Core\Repository\Publication\Factory
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PublicationTargetRepositoryCacheProviderFactory
{

    /**
     * @return \Symfony\Component\Cache\Adapter\ArrayAdapter
     */
    public function getPublicationTargetRepositoryCacheProvider(): ArrayAdapter
    {
        return new ArrayAdapter();
    }
}
