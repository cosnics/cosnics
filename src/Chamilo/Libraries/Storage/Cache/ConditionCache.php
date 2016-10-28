<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\Doctrine\Provider\PhpFileCache;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @deprecated Use ConditionPartCache now
 */
class ConditionCache
{
    const PHP_FILE_CACHE_KEY = 'cache.condition';

    /**
     * The instance of the ConditionCache
     *
     * @var \Chamilo\Libraries\Storage\Cache\ConditionCache
     */
    private static $instance;

    /**
     * The cache
     *
     * @var string[][]
     */
    private $cache;

    /**
     *
     * @var \Chamilo\Libraries\File\Cache\PhpFileCache
     */
    private $phpFileCache;

    /**
     *
     * @var boolean
     */
    private $queryFileCacheEnabled;

    /**
     *
     * @param boolean $queryFileCacheEnabled
     */
    public function __construct($queryFileCacheEnabled = true)
    {
        $this->cache = array();
        $this->queryFileCacheEnabled = $queryFileCacheEnabled;

        if ($this->queryFileCacheEnabled)
        {
            $this->phpFileCache = new PhpFileCache(Path::getInstance()->getCachePath(__NAMESPACE__));

            if (! $this->phpFileCache->contains(self::PHP_FILE_CACHE_KEY))
            {
                $this->phpFileCache->save(self::PHP_FILE_CACHE_KEY, array());
            }

            $this->cache = $this->phpFileCache->fetch(self::PHP_FILE_CACHE_KEY);
        }
    }

    /**
     * Get an instance of the ConditionCache
     *
     * @return \Chamilo\Libraries\Storage\Cache\ConditionCache
     */
    public static function getInstance()
    {
        if (! isset(self::$instance))
        {
            $queryFileCacheEnabled = Configuration::get_instance()->get_setting(
                array('Chamilo\Configuration', 'debug', 'enable_query_file_cache'));

            self::$instance = new self($queryFileCacheEnabled);
        }

        return self::$instance;
    }

    /**
     * Get a translated condition from the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return string
     */
    public function get(Condition $condition)
    {
        if ($this->exists($condition))
        {
            return $this->cache[$condition->hash()];
        }
        else

        {
            return false;
        }
    }

    /**
     * Returns whether a Condition object exists in the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @return boolean
     */
    public function exists(Condition $condition)
    {
        if (isset($this->cache[$condition->hash()]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Set the cache value for a specific Condition
     *
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     * @param string $value
     */
    public function set($condition, $value)
    {
        $this->cache[$condition->hash()] = $value;

        if ($this->queryFileCacheEnabled)
        {
            $this->phpFileCache->save(self::PHP_FILE_CACHE_KEY, $this->cache);
        }
    }

    public static function reset()
    {
        $this->cache = array();

        if ($this->queryFileCacheEnabled)
        {
            $this->phpFileCache->save(self::PHP_FILE_CACHE_KEY, $this->cache);
        }
    }
}
