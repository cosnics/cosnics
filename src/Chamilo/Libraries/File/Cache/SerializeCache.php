<?php
namespace Chamilo\Libraries\File\Cache;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

/**
 *
 * @package Chamilo\Libraries\File\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SerializeCache extends Cache
{

    /**
     *
     * @var string
     */
    private $cachePath;

    /**
     *
     * @var mixed
     */
    private $cacheValue;

    /**
     *
     * @see \Chamilo\Libraries\File\Cache\Cache::get()
     */
    public function get()
    {
        if ($this->verifyCache())
        {
            if (! isset($this->cacheValue))
            {
                $this->cacheValue = unserialize(file_get_contents($this->getCachePath()));
            }

            return $this->cacheValue;
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Cache\Cache::set()
     */
    public function set($data)
    {
        $this->cacheValue = $data;
        Filesystem :: write_to_file($this->getCachePath(), serialize($data));
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Cache\Cache::reset()
     */
    public function truncate()
    {
        try
        {
            if ($this->verifyCache())
            {
                $this->cacheValue = null;
                Filesystem :: remove($this->getCachePath());
            }
        }
        catch (CacheUnavailableException $exception)
        {
        }
    }

    /**
     *
     * @return string
     */
    public function getCachePath()
    {
        if (! isset($this->cachePath))
        {
            $this->cachePath = Path :: getInstance()->getCachePath($this->getContext()) . self :: TYPE_SERIALIZE . '.' .
                 $this->getKey();
        }

        return $this->cachePath;
    }

    /**
     *
     * @return boolean
     */
    public function isCacheSet()
    {
        return file_exists($this->getCachePath());
    }
}