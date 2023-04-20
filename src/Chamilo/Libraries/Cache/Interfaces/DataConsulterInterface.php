<?php
namespace Chamilo\Libraries\Cache\Interfaces;

/**
 * @package Chamilo\Libraries\Cache\Interfaces
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface DataConsulterInterface
{
    public function getDataReader(): CacheDataReaderInterface;
}