<?php
namespace Chamilo\Libraries\Utilities;

use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;

/**
 *
 * @package Chamilo\Libraries\Utilities
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Utilities
{
    const COMMON_LIBRARIES = 'Chamilo\Libraries';

    /**
     *
     * @param string[] $parsed_url
     *
     * @return string
     */
    public static function build_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? $pass . '@' : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        if (isset($parsed_url['query']) && is_array($parsed_url['query']))
        {
            $query = '?' . http_build_query($parsed_url['query']);
        }
        elseif (isset($parsed_url['query']) && is_string($parsed_url['query']))
        {
            $query = '?' . $parsed_url['query'];
        }
        else
        {
            $query = '';
        }

        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    }

    /**
     *
     * @param string $string
     *
     * @return string
     */
    public static function htmlentities($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
    }

    /**
     * Orders the given learning objects by their title.
     * Note that the ordering happens in-place; there is no return
     * value.
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $objects The content objects to order.
     */
    public static function order_content_objects_by_title($objects)
    {
        usort(
            $objects, function ($content_object_1, $content_object_2) {
            return strcasecmp($content_object_1->get_title(), $content_object_2->get_title());
        }
        );
    }

    /**
     * Transforms a search string (given by an end user in a search form) to a Condition, which can be used to retrieve
     * learning objects from the repository.
     *
     * @param string $query The query as given by the end user.
     * @param \Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable[] $properties The learning object
     *        properties which should be taken into account for the condition. For
     *        example, array('title','type') will yield a Condition which can be used to search for learning objects
     *        on the properties 'title' or 'type'. By default the properties are 'title' and 'description'. If the
     *        condition should apply to a single property, you can pass a string instead of an array.
     *
     * @return \Chamilo\Libraries\Storage\Query\Condition\Condition The condition.
     * @deprecated Use the function get_conditions() in action_bar_renderer to access the search property. This function
     *             uses this method to create the conditions.
     */
    public static function query_to_condition($query, $properties)
    {
        if (!is_array($properties))
        {
            $properties = array($properties);
        }
        $queries = self::split_query($query);
        if (is_null($queries))
        {
            return null;
        }
        $cond = array();
        foreach ($queries as $q)
        {
            $q = '*' . $q . '*';
            $pattern_conditions = array();
            foreach ($properties as $index => $property)
            {
                $pattern_conditions[] = new PatternMatchCondition($property, $q);
            }
            if (count($pattern_conditions) > 1)
            {
                $cond[] = new OrCondition($pattern_conditions);
            }
            else
            {
                $cond[] = $pattern_conditions[0];
            }
        }

        return new AndCondition($cond);
    }

    /**
     *
     * @param integer $width
     * @param integer $height
     * @param integer[] $imageProperties
     *
     * @return integer[]
     */
    public static function scaleDimensions($width, $height, $imageProperties)
    {
        if ($imageProperties['width'] > $width || $imageProperties['height'] > $height)
        {
            if ($imageProperties['width'] >= $imageProperties['height'])
            {
                $imageProperties['thumbnailWidth'] = $width;
                $imageProperties['thumbnailHeight'] =
                    ($imageProperties['thumbnailWidth'] / $imageProperties['width']) * $imageProperties['height'];
            }
            else
            {
                $imageProperties['thumbnailHeight'] = $height;
                $imageProperties['thumbnailWidth'] =
                    ($imageProperties['thumbnailHeight'] / $imageProperties['height']) * $imageProperties['width'];
            }
        }
        else
        {
            $imageProperties['thumbnailWidth'] = $imageProperties['width'];
            $imageProperties['thumbnailHeight'] = $imageProperties['height'];
        }

        return $imageProperties;
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
    public static function split_query($pattern)
    {
        $matches = array();
        preg_match_all('/(?:"([^"]+)"|""|(\S+))/', $pattern, $matches);
        $parts = array();
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
