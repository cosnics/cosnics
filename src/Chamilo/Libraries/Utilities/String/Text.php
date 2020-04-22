<?php
namespace Chamilo\Libraries\Utilities\String;

use Chamilo\Libraries\Utilities\StringUtilities;
use DOMDocument;

/**
 * @package Chamilo\Libraries\Utilities\String
 */
class Text
{

    /**
     * Function to recreate the charAt function from javascript
     * Found at http://be.php.net/manual/en/function.substr.php#81491
     *
     * @param string $str
     * @param integer $pos
     *
     * @return string|integer
     */
    public static function char_at($str, $pos)
    {
        return (substr($str, $pos, 1) !== false) ? substr($str, $pos, 1) : - 1;
    }

    /**
     *
     * @param string $url
     * @param string $text
     * @param boolean $new_page
     * @param string $class
     * @param string[] $styles
     *
     * @return string
     */
    public function create_link($url, $text, $new_page = false, $class = null, $styles = array())
    {
        $link = '<a href="' . $url . '" ';

        if ($new_page)
        {
            $link .= 'target="about:blank" ';
        }

        if ($class)
        {
            $link .= 'class="' . $class . '" ';
        }

        if (count($styles) > 0)
        {
            $link .= 'style="';

            foreach ($styles as $name => $value)
            {
                $link .= $name . ': ' . $value . ';';
            }

            $link .= '" ';
        }

        $link .= '>' . $text . '</a>';

        return $link;
    }

    /**
     *
     * @param integer $length
     *
     * @return string
     * @deprecated Use vendor library now: hackzilla/password-generator
     */
    public static function generate_password($length = 8)
    {
        $characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        if ($length < 2)
        {
            $length = 2;
        }
        $password = '';
        for ($i = 0; $i < $length; $i ++)
        {
            $password .= $characters[rand() % strlen($characters)];
        }

        return $password;
    }

    /**
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return string
     */
    public static function highlight($haystack, $needle)
    {
        if (strlen($haystack) < 1 || strlen($needle) < 1)
        {
            return $haystack;
        }

        $matches = array();
        $matches_done = array();

        preg_match_all("/$needle+/i", $haystack, $matches);

        if (is_array($matches[0]) && count($matches[0]) >= 1)
        {
            foreach ($matches[0] as $match)
            {
                if (in_array($match, $matches_done))
                {
                    continue;
                }

                $matches_done[] = $match;
                $haystack = str_replace($match, '<mark>' . $match . '</mark>', $haystack);
            }
        }

        return $haystack;
    }

    /**
     * Checks if a given directory is valid
     * Use this method before deleting a path!
     *
     * @param string $path
     *
     * @return boolean
     */
    public static function is_valid_path($path)
    {
        $filtered_path = Text::remove_non_alphanumerical($path);
        if (!$path || !$filtered_path)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param string $string
     * @param string $tag
     *
     * @return \DOMNodeList
     */
    public static function parse_html_file($string, $tag = 'img')
    {
        $document = new DOMDocument();
        $document->loadHTML($string);

        return $document->getElementsByTagname($tag);
    }

    /**
     *
     * @param string $query
     *
     * @return string[]
     */
    public static function parse_query_string($query = '')
    {
        $queries = array();
        $variables = explode('&', $query);

        foreach ($variables as $variable)
        {
            list($key, $value) = explode('=', $variable, 2);
            $queries[$key] = $value;
        }

        return $queries;
    }

    /**
     *
     * @param string $string
     *
     * @return string
     */
    public static function remove_non_alphanumerical($string)
    {
        $string = str_replace(' ', '', $string);
        $string = preg_replace('/[^a-zA-Z0-9\s]/', '', $string);

        return (string) StringUtilities::getInstance()->createString($string)->underscored()->__toString();
    }
}
