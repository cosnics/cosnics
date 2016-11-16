<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\DataLoaderInterface;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class AggregatedDataLoader implements DataLoaderInterface
{

    /**
     *
     * @var \Chamilo\Configuration\Interfaces\DataLoaderInterface[]
     */
    private $dataLoaders;

    /**
     *
     * @param \Chamilo\Configuration\Interfaces\DataLoaderInterface[] $dataLoaders
     */
    public function __construct($dataLoaders)
    {
        $this->dataLoaders = $dataLoaders;
    }

    /**
     *
     * @return \Chamilo\Configuration\Interfaces\DataLoaderInterface[]
     */
    protected function getDataLoaders()
    {
        return $this->dataLoaders;
    }

    /**
     *
     * @param \Chamilo\Configuration\Interfaces\DataLoaderInterface[] $dataLoaders
     */
    protected function setDataLoaders($dataLoaders)
    {
        $this->dataLoaders = $dataLoaders;
    }

    /**
     *
     * @return string[]
     */
    public function getData()
    {
        $data = array();
        
        foreach ($this->getDataLoaders() as $dataLoader)
        {
            $data = array_merge_recursive($data, $dataLoader->getData());
        }
        
        return $data;
    }
}
