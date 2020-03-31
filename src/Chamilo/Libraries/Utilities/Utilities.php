<?php
namespace Chamilo\Libraries\Utilities;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Translation\Translation;

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

    const TOOLBAR_DISPLAY_ICON = 1;

    const TOOLBAR_DISPLAY_ICON_AND_LABEL = 3;

    const TOOLBAR_DISPLAY_LABEL = 2;

    private static $us_camel_map = array();

    private static $camel_us_map = array();

    /**
     *
     * @param string[] $array
     *
     * @return string
     */
    public static function DisplayArray($array)
    {
        $html = array();

        $depth = 0;
        if (is_array($array))
        {
            $html[] = "Array (<br />";
            for ($i = 0; $i < count($array); $i ++)
            {
                if (is_array($array[$i]))
                {
                    $html[] = self::DisplayInlineArray($array[$i], $depth + 1, $i);
                }
                else
                {
                    $html[] = "[" . $i . "] => " . $array[$i];
                    $html[] = "<br />";
                    $depth = 0;
                }
            }
            $html[] = ")<br />";
        }
        else
        {
            $html[] = "Variabele is geen array";
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string[] $inlinearray
     * @param integer $depth
     * @param string $element
     *
     * @return string
     */
    public static function DisplayInlineArray($inlinearray, $depth, $element)
    {
        $html = array();

        $spaces = null;

        for ($j = 0; $j < $depth - 1; $j ++)
        {
            $spaces .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }

        $html[] = $spaces . "[" . $element . "]" . "Array (<br />";

        $spaces .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        for ($i = 0; $i < count($inlinearray); $i ++)
        {
            $key = key($inlinearray);

            if (is_array($inlinearray[$i]))
            {
                $html[] = self::DisplayInlineArray($inlinearray[$i], $depth + 1, $i);
            }
            else
            {
                $html[] = $spaces . "[" . $key . "] => " . $inlinearray[$key];
                $html[] = "<br />";
            }

            next($inlinearray);
        }

        $html[] = $spaces . ")<br />";

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public static function add_block_hider()
    {
        $html = array();

        $html[] = '<script type="text/javascript">';
        $html[] .= 'function showElement(item)';
        $html[] .= '{';
        $html[] .= '	if (document.getElementById(item).style.display == \'block\')';
        $html[] .= '  {';
        $html[] .= '		document.getElementById(item).style.display = \'none\';';
        $html[] .= '		document.getElementById(\'plus-\'+item).style.display = \'inline\';';
        $html[] .= '		document.getElementById(\'minus-\'+item).style.display = \'none\';';
        $html[] .= '  }';
        $html[] .= '	else';
        $html[] .= '  {';
        $html[] .= '		document.getElementById(item).style.display = \'block\';';
        $html[] .= '		document.getElementById(\'plus-\'+item).style.display = \'none\';';
        $html[] .= '		document.getElementById(\'minus-\'+item).style.display = \'inline\';';
        $html[] .= '		document.getElementById(item).value = \'Version comments here ...\';';
        $html[] .= '	}';
        $html[] .= '}';
        $html[] .= '</script>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $id
     * @param string $message
     * @param boolean $display_block
     *
     * @return string
     */
    public static function build_block_hider($id = null, $message = null, $display_block = false)
    {
        $html = array();

        if (isset($id))
        {
            if (!isset($message))
            {
                $message = self::underscores_to_camelcase($id);
            }

            $show_message = 'Show' . $message;
            $hide_message = 'Hide' . $message;

            $html[] =
                '<div id="plus-' . $id . '"><a href="javascript:showElement(\'' . $id . '\')">' . Translation::get(
                    'Show' . $message
                ) . '</a></div>';
            $html[] = '<div id="minus-' . $id . '" style="display: none;"><a href="javascript:showElement(\'' . $id .
                '\')">' . Translation::get('Hide' . $message) . '</a></div>';
            $html[] = '<div id="' . $id . '" style="display: ' . ($display_block ? 'block' : 'none') . ';">';
        }
        else
        {
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

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
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object_1
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object_2
     *
     * @return integer
     */
    private static function by_id_desc($content_object_1, $content_object_2)
    {
        return ($content_object_1->get_id() < $content_object_2->get_id() ? 1 : - 1);
    }

    /**
     * Compares learning objects by title.
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object_1
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $content_object_2
     *
     * @return integer
     */
    public static function by_title($content_object_1, $content_object_2)
    {
        return strcasecmp($content_object_1->get_title(), $content_object_2->get_title());
    }

    /**
     * Prepares the given learning object for use as a value for the element_finder QuickForm element's value array.
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $object
     *
     * @return string[] The value.
     */
    public static function content_object_for_element_finder($object)
    {
        $type = $object->get_type();
        // TODO: i18n
        $date = date('r', $object->get_modification_date());
        $return = array();
        $return['id'] = 'lo_' . $object->get_id();
        $return['classes'] =
            $object->getGlyph(IdentGlyph::SIZE_MINI, true, array('fa-fw'))
                ->getClassNamesString();
        $return['title'] = $object->get_title();
        $return['description'] = Translation::get(
                'TypeName', array(), ClassnameUtilities::getInstance()->getNamespaceFromClassname($type)
            ) . ' (' . $date . ')';

        return $return;
    }

    /**
     * Prepares the given learning objects for use as a value for the element_finder QuickForm element.
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $objects
     *
     * @return string[] The value.
     */
    public static function content_objects_for_element_finder($objects)
    {
        $return = array();
        foreach ($objects as $object)
        {
            $id = $object->get_id();
            $return[$id] = self::content_object_for_element_finder($object);
        }

        return $return;
    }

    /**
     *
     * @param boolean $value
     *
     * @return string
     */
    public static function display_true_false_icon($value)
    {
        if ($value)
        {
            $glyph = new FontAwesomeGlyph('circle', array('text-success'));
        }
        else
        {
            $glyph = new FontAwesomeGlyph('circle', array('text-danger'));
        }

        return $glyph->render();
    }

    /**
     * Render a complete backtrace for the currently executing script
     *
     * @return string The backtrace
     */
    public static function get_backtrace()
    {
        $html = array();
        $backtraces = debug_backtrace();
        foreach ($backtraces as $backtrace)
        {
            $html[] = implode(' ', $backtrace);
        }

        return implode('<br/>', $html);
    }

    /**
     * Get the current query string (e.g.
     * "?foo=bar&faa=bor")
     *
     * @param string[] $append optional array of key/value pairs to be appended to the current QS.
     *
     * @return string
     */
    public static function get_current_query_string($append = array())
    {
        $queryString = $_SERVER['QUERY_STRING'];
        foreach ($append as $key => $value)
        {
            $queryString .= ($queryString === '' ? '' : '&') . $key . '=' . $value;
        }

        return $queryString;
    }

    /**
     * Get the class name from a fully qualified namespaced class name if and only if it's in the given namespace
     *
     * @param string $namespace
     * @param string $classname
     *
     * @return string|boolean class name or false
     */
    public static function get_namespace_classname($namespace, $classname)
    {
        $classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            return false;
        }
        else
        {
            $class_name = $classname_parts[count($classname_parts) - 1];
            array_pop($classname_parts);
            if (implode('\\', $classname_parts) != $namespace)
            {
                return false;
            }
            else
            {
                return $class_name;
            }
        }
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
     *
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject[] $objects The content objects to order.
     */
    public static function order_content_objects_by_id_desc($objects)
    {
        usort($objects, array(get_class(), 'by_id_desc'));
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
        usort($objects, array(get_class(), 'by_title'));
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
     * @return integer
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
