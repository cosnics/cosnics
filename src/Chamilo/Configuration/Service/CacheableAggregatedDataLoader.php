<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class CacheableAggregatedDataLoader extends AggregatedDataLoader implements CacheableDataLoaderInterface
{

    /**
     *
     * @param \Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface[] $dataLoaders
     */
    public function __construct($dataLoaders)
    {
        parent::__construct($dataLoaders);
    }

    /**
     *
     * @return \Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface[]
     */
    protected function getDataLoaders()
    {
        return parent::getDataLoaders();
    }

    /**
     *
     * @param \Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface[] $dataLoaders
     */
    protected function setDataLoaders($dataLoaders)
    {
        parent::setDataLoaders($dataLoaders);
    }

    /**
     *
     * @see \Chamilo\Configuration\Interfaces\CacheableDataLoaderInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        $identifierParts = array();

        foreach ($this->getDataLoaders() as $dataLoader)
        {
            $identifierParts[] = $dataLoader->getIdentifier();
        }

        return md5(json_encode($identifierParts));
    }
}
