<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\DataLoaderInterface;

/**
 * @package Chamilo\Libraries\Cache
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DataConsulterTrait
{
    protected DataLoaderInterface $dataLoader;

    public function __construct(DataLoaderInterface $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    public function getDataLoader(): DataLoaderInterface
    {
        return $this->dataLoader;
    }
}