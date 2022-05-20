<?php
namespace Chamilo\Libraries\Storage\Cache;

use Chamilo\Libraries\Storage\Query\ConditionPart;

/**
 *
 * @package Chamilo\Libraries\Storage\Cache
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class ConditionPartCache
{

    /**
     * @var string[][][]
     */
    private array $cache;

    /**
     */
    public function __construct()
    {
        $this->cache = [true => [], false => []];
    }

    public function exists(ConditionPart $conditionPart, bool $enableAliasing): bool
    {
        if (isset($this->cache[$enableAliasing][$conditionPart->hash()]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get(ConditionPart $conditionPart, bool $enableAliasing): string
    {
        if ($this->exists($conditionPart, $enableAliasing))
        {
            return $this->cache[$enableAliasing][$conditionPart->hash()];
        }
        else
        {
            return false;
        }
    }

    public function set(ConditionPart $conditionPart, bool $enableAliasing, string $value): ConditionPartCache
    {
        $this->cache[$enableAliasing][$conditionPart->hash()] = $value;

        return $this;
    }
}
