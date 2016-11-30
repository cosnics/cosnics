<?php
namespace Chamilo\Configuration\Service;

use Chamilo\Configuration\Interfaces\DataLoaderInterface;

/**
 *
 * @package Chamilo\Configuration\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class DataConsulter
{

    /**
     *
     * @var string[]
     */
    private $data;

    /**
     *
     * @var \Chamilo\Configuration\Interfaces\DataLoaderInterface
     */
    private $dataLoader;

    /**
     *
     * @param \Chamilo\Configuration\Interfaces\DataLoaderInterface $dataLoader
     */
    public function __construct(DataLoaderInterface $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    /**
     *
     * @return \Chamilo\Configuration\Interfaces\DataLoaderInterface
     */
    public function getDataLoader()
    {
        return $this->dataLoader;
    }

    /**
     *
     * @param \Chamilo\Configuration\Interfaces\DataLoaderInterface $configurationCache
     */
    public function setDataLoader(DataLoaderInterface $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    /**
     *
     * @return string[]
     */
    public function getData()
    {
        if (! isset($this->data))
        {
            $this->data = $this->getDataLoader()->getData();
        }
        
        return $this->data;
    }
}
