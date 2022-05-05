<?php
namespace Chamilo\Libraries\Storage\Service;

use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;

/**
 * @package Chamilo\Libraries\Storage\Service
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class SearchQueryConditionGenerator
{

    /**
     * @param string $searchQuery
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $properties
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\AndCondition
     */
    public function getSearchConditions(string $searchQuery, array $properties)
    {
        if (!is_array($properties))
        {
            $properties = array($properties);
        }

        $searchQueryParts = $this->splitSearchQuery($searchQuery);

        if (is_null($searchQueryParts))
        {
            return null;
        }

        $conditions = [];

        foreach ($searchQueryParts as $searchQueryPart)
        {
            $patternMatchConditions = [];

            foreach ($properties as $property)
            {
                $patternMatchConditions[] = new ContainsCondition($property, $searchQueryPart);
            }

            if (count($patternMatchConditions) > 1)
            {
                $conditions[] = new OrCondition($patternMatchConditions);
            }
            else
            {
                $conditions[] = $patternMatchConditions[0];
            }
        }

        return new AndCondition($conditions);
    }

    /**
     * Splits a Google-style search query.
     * For example, the query /"chamilo repository" utilities/ would be parsed into
     * array('chamilo repository', 'utilities').
     *
     * @param string $pattern
     *
     * @return string[] The query's parts.
     */
    public function splitSearchQuery($pattern)
    {
        $matches = [];
        preg_match_all('/(?:"([^"]+)"|""|(\S+))/', $pattern, $matches);
        $parts = [];

        for ($i = 1; $i <= 2; $i ++)
        {
            foreach ($matches[$i] as $m)
            {
                if (!is_null($m) && strlen($m) > 0)
                {
                    $parts[] = $m;
                }
            }
        }

        return (count($parts) ? $parts : null);
    }
}