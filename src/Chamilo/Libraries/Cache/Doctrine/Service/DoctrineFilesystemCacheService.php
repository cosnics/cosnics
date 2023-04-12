<?php
namespace Chamilo\Libraries\Cache\Doctrine\Service;

use Chamilo\Libraries\Cache\Doctrine\DoctrineCacheService;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Libraries\Cache\Doctrine\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class DoctrineFilesystemCacheService extends DoctrineCacheService
{

    public function setupCacheAdapter(): AdapterInterface
    {
        return new FilesystemAdapter(md5($this->getCachePathNamespace()), 0, $this->getCachePath());
    }
}