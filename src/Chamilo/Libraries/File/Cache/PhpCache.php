<?php
namespace Chamilo\Libraries\File\Cache;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;

class PhpCache extends Cache
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
                $this->cacheValue = require ($this->getCachePath());
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
        Filesystem :: write_to_file($this->getCachePath(), sprintf('<?php return %s;', var_export($data, true)));
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Cache\Cache::reset()
     */
    public function truncate()
    {
    }

    /**
     *
     * @return string
     */
    public function getCachePath()
    {
        if (! isset($this->cachePath))
        {
            $this->cachePath = Path :: getInstance()->getCachePath($this->getContext()) . self :: TYPE_PHP . '.' .
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