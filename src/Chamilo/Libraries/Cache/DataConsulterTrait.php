<?php
namespace Chamilo\Libraries\Cache;

use Chamilo\Libraries\Cache\Interfaces\DataAccessorInterface;

/**
 * @package Chamilo\Libraries\Cache
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait DataConsulterTrait
{
    protected DataAccessorInterface $dataLoader;

    public function __construct(DataAccessorInterface $dataLoader)
    {
        $this->dataLoader = $dataLoader;
    }

    public function getDataLoader(): DataAccessorInterface
    {
        return $this->dataLoader;
    }
}