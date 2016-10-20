<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Cache\Doctrine\Provider\PhpFileCache;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Query\Variable\ConditionVariable;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConditionVariableCache
{
    const PHP_FILE_CACHE_KEY = 'cache.condition.variable';

    /**
     * The instance of the ConditionVariableCache
     *
     * @var \Chamilo\Libraries\Storage\Cache\ConditionVariableCache
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
     * Get an instance of the ConditionVariableCache
     *
     * @return \Chamilo\Libraries\Storage\Cache\ConditionVariableCache
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
     * Get a translated condition_variable from the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @return string
     */
    public function get(ConditionVariable $conditionVariable)
    {
        if ($this->exists($conditionVariable))
        {
            return $this->cache[$conditionVariable->hash()];
        }
        else

        {
            return false;
        }
    }

    /**
     * Returns whether a ConditionVariable object exists in the cache
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @return boolean
     */
    public function exists(ConditionVariable $conditionVariable)
    {
        if (isset($this->cache[$conditionVariable->hash()]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Set the cache value for a specific ConditionVariable
     *
     * @param \Chamilo\Libraries\Storage\Query\Variable\ConditionVariable $conditionVariable
     * @param string $value
     */
    public function set($conditionVariable, $value)
    {
        $this->cache[$conditionVariable->hash()] = $value;

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
