<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\CacheDataReaderInterface;

/**
 * @package Chamilo\Libraries\Cache
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DataConsulterTrait
{
    protected CacheDataReaderInterface $dataReader;

    public function getDataReader(): CacheDataReaderInterface
    {
        return $this->dataReader;
    }
}