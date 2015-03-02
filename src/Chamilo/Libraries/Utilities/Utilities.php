<?php
namespace Chamilo\Libraries\Utilities;

use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Header;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Exception;
use PEAR;
use XML_Unserializer;

/**
 *
 * @package common This class provides some common methods that are used throughout the platform.
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Utilities
{
    const TOOLBAR_DISPLAY_ICON = 1;
    const TOOLBAR_DISPLAY_LABEL = 2;
    const TOOLBAR_DISPLAY_ICON_AND_LABEL = 3;
    const COMMON_LIBRARIES = 'Chamilo\Libraries';

    private static $us_camel_map = array();

    private static $camel_us_map = array();

    /**
     * Splits a Google-style search query.
     * For example, the query /"chamilo repository" utilities/ would be parsed into
     * array('chamilo repository', 'utilities').
     *
     * @param $pattern The query.
     * @return array The query's parts.
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
                if (! is_null($m) && strlen($m) > 0)
                    $parts[] = $m;
            }
        }
        return (count($parts) ? $parts : null);
    }

    /**
     * Transforms a search string (given by an end user in a search form) to a Condition, which can be used to retrieve
     * learning objects from the repository.
     *
     * @param $query string The query as given by the end user.
     * @param $properties mixed The learning object properties which should be taken into account for the condition. For
     *        example, array('title','type') will yield a Condition which can be used to search for learning objects
     *        on the properties 'title' or 'type'. By default the properties are 'title' and 'description'. If the
     *        condition should apply to a single property, you can pass a string instead of an array.
     * @return Condition The condition.
     * @deprecated Use the function get_conditions() in action_bar_renderer to access the search property. This function
     *             uses this method to create the conditions.
     */
    public static function query_to_condition($query, $properties)
    {
        if (! is_array($properties))
        {
            $properties = array($properties);
        }
        $queries = self :: split_query($query);
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
                $pattern_conditions[] = new PatternMatchCondition($property->get_property(), $q);
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
        $result = new AndCondition($cond);
        return $result;
    }

    /**
     * Converts a date/time value retrieved from a FormValidator datepicker element to the corresponding UNIX itmestamp.
     *
     * @param $string string The date/time value.
     * @return int The UNIX timestamp.
     */
    public static function time_from_datepicker($string)
    {
        list($date, $time) = split(' ', $string);
        list($year, $month, $day) = split('-', $date);
        list($hours, $minutes, $seconds) = split(':', $time);
        return mktime($hours, $minutes, $seconds, $month, $day, $year);
    }

    /**
     * Converts a date/time value retrieved from a FormValidator datepicker without timepicker element to the
     * corresponding UNIX itmestamp.
     *
     * @param $string string The date/time value.
     * @return int The UNIX timestamp.
     */
    public static function time_from_datepicker_without_timepicker($string, $h = 0, $m = 0, $s = 0)
    {
        list($year, $month, $day) = split('-', $string);
        return mktime($h, $m, $s, $month, $day, $year);
    }

    /**
     * Orders the given learning objects by their title.
     * Note that the ordering happens in-place; there is no return
     * value.
     *
     * @param $objects array The learning objects to order.
     */
    public static function order_content_objects_by_title($objects)
    {
        usort($objects, array(get_class(), 'by_title'));
    }

    public static function order_content_objects_by_id_desc($objects)
    {
        usort($objects, array(get_class(), 'by_id_desc'));
    }

    /**
     * Prepares the given learning objects for use as a value for the element_finder QuickForm element.
     *
     * @param $objects array The learning objects.
     * @return array The value.
     */
    public static function content_objects_for_element_finder($objects)
    {
        $return = array();
        foreach ($objects as $object)
        {
            $id = $object->get_id();
            $return[$id] = self :: content_object_for_element_finder($object);
        }
        return $return;
    }

    /**
     * Prepares the given learning object for use as a value for the element_finder QuickForm element's value array.
     *
     * @param $object ContentObject The learning object.
     * @return array The value.
     */
    public static function content_object_for_element_finder($object)
    {
        $type = $object->get_type();
        // TODO: i18n
        $date = date('r', $object->get_modification_date());
        $return = array();
        $return['id'] = 'lo_' . $object->get_id();
        $return['classes'] = 'type type_' . ClassnameUtilities :: getInstance()->getClassNameFromNamespace($type, true);
        $return['title'] = $object->get_title();
        $return['description'] = Translation :: get(
            'TypeName',
            array(),
            ClassnameUtilities :: getInstance()->getNamespaceFromClassname($type)) . ' (' . $date . ')';
        return $return;
    }

    /**
     * Compares learning objects by title.
     *
     * @param $content_object_1 ContentObject
     * @param $content_object_2 ContentObject
     * @return int
     */
    public static function by_title($content_object_1, $content_object_2)
    {
        return strcasecmp($content_object_1->get_title(), $content_object_2->get_title());
    }

    private static function by_id_desc($content_object_1, $content_object_2)
    {
        return ($content_object_1->get_id() < $content_object_2->get_id() ? 1 : - 1);
    }

    /**
     * Checks if a file is an HTML document.
     */
    // TODO: SCARA - MOVED / FROM: document_form_class / TO: Utilities or some other relevant class.
    public static function is_html_document($path)
    {
        return (preg_match('/\.x?html?$/', $path) === 1);
    }

    /*
     * Checks if string is HTTP or FTP uri
     */
    public static function is_web_uri($uri)
    {
        return ((stripos($uri, 'http://') === 0) || (stripos($uri, 'https://') === 0) || (stripos($uri, 'ftp://') === 0));
    }

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

    public static function build_block_hider($id = null, $message = null, $display_block = false)
    {
        $html = array();

        if (isset($id))
        {
            if (! isset($message))
            {
                $message = self :: underscores_to_camelcase($id);
            }

            $show_message = 'Show' . $message;
            $hide_message = 'Hide' . $message;

            $html[] = '<div id="plus-' . $id . '"><a href="javascript:showElement(\'' . $id . '\')">' . Translation :: get(
                'Show' . $message) . '</a></div>';
            $html[] = '<div id="minus-' . $id . '" style="display: none;"><a href="javascript:showElement(\'' . $id .
                 '\')">' . Translation :: get('Hide' . $message) . '</a></div>';
            $html[] = '<div id="' . $id . '" style="display: ' . ($display_block ? 'block' : 'none') . ';">';
        }
        else
        {
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    // 2 simple functions to display an array, a bit prettier as print_r
    // for testing purposes only!
    // @author Dieter De Neef
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
                    $html[] = self :: DisplayInlineArray($array[$i], $depth + 1, $i);
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
                $html[] = self :: DisplayInlineArray($inlinearray[$i], $depth + 1, $i);
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

    public static function format_seconds_to_hours($seconds)
    {
        $hours = floor($seconds / 3600);
        $rest = $seconds % 3600;

        $minutes = floor($rest / 60);
        $seconds = $rest % 60;

        if ($minutes < 10)
        {
            $minutes = '0' . $minutes;
        }

        if ($seconds < 10)
        {
            $seconds = '0' . $seconds;
        }

        return $hours . ':' . $minutes . ':' . $seconds;
    }

    public static function format_seconds_to_minutes($seconds)
    {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        if ($minutes < 10)
        {
            $minutes = '0' . $minutes;
        }

        if ($seconds < 10)
        {
            $seconds = '0' . $seconds;
        }

        return $minutes . ':' . $seconds;
    }

    /**
     * Strips the tags on request, and truncates if necessary a given string to the given length in characters.
     * Adds a
     * character at the end (either specified or default ...) when the string is truncated. Boolean $strip to indicate
     * if the tags within the string have to be stripped
     *
     * @param $string string The input string, UTF-8 encoded.
     * @param $length int The limit of the resulting length in characters.
     * @param $strip boolean Indicates if the tags within the string have to be stripped.
     * @param $char string A UTF-8 encoded character put at the end of the result string indicating truncation, by
     *        default it is the horizontal ellipsis (\u2026)
     * @return string The result string, html-entities (if any) are converted to normal UTF-8 characters.
     */
    public static function truncate_string($string, $length = 200, $strip = true, $char = "\xE2\x80\xA6")
    {
        if ($strip)
        {
            $string = strip_tags($string);
        }

        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        if (mb_strlen($string, 'UTF-8') > $length)
        {
            $string = mb_substr($string, 0, $length - mb_strlen($char, 'UTF-8'), 'UTF-8') . $char;
        }

        return $string;
    }

    public static function extract_xml_file($file, $extra_options = array())
    {
        require_once 'XML/Unserializer.php';
        if (file_exists($file))
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);

            foreach ($extra_options as $op => $value)
                $unserializer->setOption($op, $value);

                // userialize the document
            $status = $unserializer->unserialize($file, true);
            if (PEAR :: isError($status))
            {
                return false;
            }
            else
            {
                $data = $unserializer->getUnserializedData();
                return $data;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @param $application string
     */
    public static function set_application($application)
    {
        Translation :: set_application($application);
        Header :: get_instance()->set_section($application);
    }

    /**
     *
     * @param $value mixed
     * @return string
     */
    public static function display_true_false_icon($value)
    {
        if ($value)
        {
            $icon = 'action_setting_true';
        }
        else
        {
            $icon = 'action_setting_false';
        }
        return '<img src="' . Theme :: getInstance()->getCommonImagePath($icon) . '">';
    }

    /**
     *
     * @param $string string
     */
    public static function htmlentities($string)
    {
        return htmlentities($string, ENT_COMPAT, 'UTF-8');
    }

    /**
     *
     * @return int
     */
    public static function get_usable_memory()
    {
        $val = trim(@ini_get('memory_limit'));

        if (preg_match('/(\\d+)([mkg]?)/i', $val, $regs))
        {
            $memory_limit = (int) $regs[1];
            switch ($regs[2])
            {

                case 'k' :
                case 'K' :
                    $memory_limit *= 1024;
                    break;

                case 'm' :
                case 'M' :
                    $memory_limit *= 1048576;
                    break;

                case 'g' :
                case 'G' :
                    $memory_limit *= 1073741824;
                    break;
            }

            // how much memory PHP requires at the start of export (it is really a little less)
            if ($memory_limit > 6100000)
            {
                $memory_limit -= 6100000;
            }

            // allow us to consume half of the total memory available
            $memory_limit /= 2;
        }
        else
        {
            // set the buffer to 1M if we have no clue how much memory PHP will give us :P
            $memory_limit = 1048576;
        }

        return $memory_limit;
    }

    /**
     *
     * @param $mimetype string
     * @return string The image html
     */
    public static function mimetype_to_image($mimetype)
    {
        $mimetype_image = str_replace('/', '_', $mimetype);
        return Theme :: getInstance()->getCommonImage(
            'mimetype/' . $mimetype_image,
            'png',
            $mimetype,
            '',
            ToolbarItem :: DISPLAY_ICON);
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
     * Get the class name from a fully qualified namespaced class name if and only if it's in the given namespace
     *
     * @param $namespace string
     * @param $classname string
     * @return string boolean class name or false
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

    public static function clone_array($items)
    {
        $result = array();
        foreach ($items as $key => $value)
        {
            $result[$key] = is_object($value) ? clone ($value) : $value;
        }
        return $result;
    }

    public static function handle_exception($exception)
    {
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
        	<head>
        		<title>Uncaught exception</title>
        		<link rel="stylesheet" href="Configuration/Resources/Css/Aqua/Stylesheet.css" type="text/css"/>
        	</head>
        	<body dir="ltr">
        		<div id="outerframe">
        			<div id="header">
        				<div id="header1">
        					<div class="banner"><span class="logo"></span><span class="text">Chamilo</span></div>
        					<div class="clear">&nbsp;</div>
        				</div>
        				<div class="clear">&nbsp;</div>
        			</div>

                    <div id="trailbox">
                        <ul id="breadcrumbtrail">
                        	<li><a href="#">Uncaught exception</a></li>
                        </ul>
                    </div>

        			<div id="main" style="min-height: 300px;">
        				<div class="error-message">' . $exception->getMessage() . '</div><br /><br />
        			</div>

        			<div id="footer">
        				<div id="copyright">
        					<div class="logo">
        					<a href="http://www.chamilo.org"><img src="Configuration/Resources/Images/Aqua/logo_footer.png" alt="footer"/></a>
        					</div>
        					<div class="links">
        						<a href="http://www.chamilo.org">http://www.chamilo.org</a>&nbsp;|&nbsp;&copy;&nbsp;' . @date(
            'Y') . '
        					</div>
        					<div class="clear"></div>
        				</div>
        			</div>
        		</div>
        	</body>
        </html>';
        return $html;
    }

    /**
     * Error handling function
     */
    public static function handle_error($errno, $errstr, $errfile, $errline)
    {
        switch ($errno)
        {
            case E_USER_ERROR :
                self :: write_error('PHP Fatal error', $errstr, $errfile, $errline);
                break;
            case E_USER_WARNING :
                self :: write_error('PHP Warning', $errstr, $errfile, $errline);
                break;
            case E_USER_NOTICE :
                self :: write_error('PHP Notice', $errstr, $errfile, $errline);
            case E_RECOVERABLE_ERROR :
                self :: write_error('PHP Recoverable error', $errstr, $errfile, $errline);
            default :
        }
        return true;
    }

    public static function write_error($errno, $errstr, $errfile, $errline)
    {
        $path = Path :: getInstance()->getLogPath();
        $file = $path . 'error_log.txt';
        $fh = fopen($file, 'a');

        $message = date('[H:i:s] ', time()) . $errno . ' File: ' . $errfile . ' - Line: ' . $errline . ' - Message: ' .
             $errstr;

        fwrite($fh, $message . "\n");
        fclose($fh);
    }

    /**
     * Get the current query string (e.g.
     * "?foo=bar&faa=bor")
     *
     * @param $append array optional array of key/value pairs to be appended to the current QS.
     * @return string
     */
    public static function get_current_query_string($append = array())
    {
        $query_string = $_SERVER['QUERY_STRING'];
        foreach ($append as $key => $value)
        {
            $query_string .= ($query_string === '' ? '' : '&') . $key . '=' . $value;
        }
        return $query_string;
    }

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
}
