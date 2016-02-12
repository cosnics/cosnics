<?php
require_once dirname(__FILE__) . '/mediawiki/Utilities.php';
require_once dirname(__FILE__) . '/mediawiki/Sanitizer.php';
require_once dirname(__FILE__) . '/mediawiki/StringUtils.php';
require_once dirname(__FILE__) . '/mediawiki/Xml.php';
require_once dirname(__FILE__) . '/mediawiki/StripState.php';
require_once dirname(__FILE__) . '/mediawiki/Title.php';
require_once dirname(__FILE__) . '/mediawiki/LinkHolderArray.php';
require_once dirname(__FILE__) . '/mediawiki/Linker.php';
require_once dirname(__FILE__) . '/mediawiki/LinkCache.php';
require_once dirname(__FILE__) . '/mediawiki/Defines.php';
require_once dirname(__FILE__) . '/mediawiki/ParserOutput.php';
require_once dirname(__FILE__) . '/mediawiki/Namespace.php';
require_once dirname(__FILE__) . '/mediawiki_parser_context.class.php';

function wfUrlProtocols()
{
    /**
     * The external URL protocols
     */
    $wgUrlProtocols = array('http://', 'https://', 'ftp://', 'irc://', 'gopher://', 'telnet://',     // Well if we're going
                                                                                                 // to support the
                                                                                                 // above.. -ævar
    'nntp://',     // @bug 3808 RFC 1738
    'worldwind://', 'mailto:', 'news:', 'svn://');
    
    // Support old-style $wgUrlProtocols strings, for backwards compatibility
    // with LocalSettings files from 1.5
    if (is_array($wgUrlProtocols))
    {
        $protocols = array();
        foreach ($wgUrlProtocols as $protocol)
            $protocols[] = preg_quote($protocol, '/');
        
        return implode('|', $protocols);
    }
    else
    {
        return $wgUrlProtocols;
    }
}

function wfUrlencode($s)
{
    $s = urlencode($s);
    $s = str_ireplace(
        array('%3B', '%3A', '%40', '%24', '%21', '%2A', '%28', '%29', '%2C', '%2F'), 
        array(';', ':', '@', '$', '!', '*', '(', ')', ',', '/'), 
        $s);
    
    return $s;
}

/**
 * This is the logical opposite of wfArrayToCGI(): it accepts a query string as
 * its argument and returns the same string in array form.
 * This allows compa-
 * tibility with legacy functions that accept raw query strings instead of nice
 * arrays. Of course, keys and values are urldecode()d. Don't try passing in-
 * valid query strings, or it will explode.
 * 
 * @param $query string Query string
 * @return array Array version of input
 */
function wfCgiToArray($query)
{
    if (isset($query[0]) and $query[0] == '?')
    {
        $query = substr($query, 1);
    }
    $bits = explode('&', $query);
    $ret = array();
    foreach ($bits as $bit)
    {
        if ($bit === '')
        {
            continue;
        }
        list($key, $value) = explode('=', $bit);
        $key = urldecode($key);
        $value = urldecode($value);
        $ret[$key] = $value;
    }
    return $ret;
}

/**
 * This function takes two arrays as input, and returns a CGI-style string, e.g.
 * "days=7&limit=100". Options in the first array override options in the second.
 * Options set to "" will not be output.
 */
function wfArrayToCGI($array1, $array2 = NULL)
{
    if (! is_null($array2))
    {
        $array1 = $array1 + $array2;
    }
    
    $cgi = '';
    foreach ($array1 as $key => $value)
    {
        if ('' !== $value)
        {
            if ('' != $cgi)
            {
                $cgi .= '&';
            }
            if (is_array($value))
            {
                $firstTime = true;
                foreach ($value as $v)
                {
                    $cgi .= ($firstTime ? '' : '&') . urlencode($key . '[]') . '=' . urlencode($v);
                    $firstTime = false;
                }
            }
            else
                $cgi .= urlencode($key) . '=' . urlencode($value);
        }
    }
    return $cgi;
}

/**
 * Append a query string to an existing URL, which may or may not already
 * have query string parameters already.
 * If so, they will be combined.
 * 
 * @param string $url
 * @param string $query
 * @return string
 */
function wfAppendQuery($url, $query)
{
    if ($query != '')
    {
        if (false === strpos($url, '?'))
        {
            $url .= '?';
        }
        else
        {
            $url .= '&';
        }
        $url .= $query;
    }
    return $url;
}

/**
 * A Mediawiki wikitext parser using the same functions
 * as used by Mediawiki's parsing engine
 * 
 * @author Hans De Bisschop
 * @see Parser
 *
 */
class MediawikiParser
{
    // State constants for the definition list colon extraction
    const COLON_STATE_TEXT = 0;
    const COLON_STATE_TAG = 1;
    const COLON_STATE_TAGSTART = 2;
    const COLON_STATE_CLOSETAG = 3;
    const COLON_STATE_TAGSLASH = 4;
    const COLON_STATE_COMMENT = 5;
    const COLON_STATE_COMMENTDASH = 6;
    const COLON_STATE_COMMENTDASHDASH = 7;
    const MARKER_SUFFIX = "-QINU\x7f";
    const VERSION = '1.6.4';
    
    // Flags for preprocessToDom
    const PTD_FOR_INCLUSION = 1;

    private $mUniqPrefix;

    /**
     * The context of the MediawikiParser
     * 
     * @var MediawikiParserContext
     */
    private $mediawiki_parser_context;

    function __construct(MediaWikiParserContext $mediawiki_parser_context)
    {
        $this->mediawiki_parser_context = $mediawiki_parser_context;
        $this->mUniqPrefix = "\x7fUNIQ" . self :: getRandomString();
        $this->mLinkID = 0;
        $this->mOutput = new MediawikiParserOutput();
        $this->mStripState = new MediawikiStripState();
        $this->mLinkHolders = new MediawikiLinkHolderArray($this);
    }

    function get_mediawiki_parser_context()
    {
        return $this->mediawiki_parser_context;
    }

    /**
     * Get a random string
     * @private
     * 
     * @static
     *
     */
    function getRandomString()
    {
        return dechex(mt_rand(0, 0x7fffffff)) . dechex(mt_rand(0, 0x7fffffff));
    }

    function parse()
    {
        $text = $this->mediawiki_parser_context->get_body();
        $text = $this->internalParse($text);
        
        // Clean up special characters, only run once, next-to-last before doBlockLevels
        $fixtags = array(        // french spaces, last one Guillemet-left
                          // only if there is something before the space
        '/(.) (?=\\?|:|;|!|%|\\302\\273)/' => '\\1&nbsp;\\2',         // french spaces, Guillemet-right
        '/(\\302\\253) /' => '\\1&nbsp;', '/&nbsp;(!\s*important)/' => ' \\1'); // Beware of CSS magic word !important,
                                                                                // bug #11874.
        
        $text = preg_replace(array_keys($fixtags), array_values($fixtags), $text);
        
        $text = $this->doBlockLevels($text, $linestart);
        
        $this->replaceLinkHolders($text);
        
        return $text;
    }

    /**
     * Replace <!--LINK--> link placeholders with actual links, in the buffer
     * Placeholders created in Skin::makeLinkObj()
     * Returns an array of link CSS classes, indexed by PDBK.
     */
    function replaceLinkHolders(&$text, $options = 0)
    {
        return $this->mLinkHolders->replace($text);
    }

    function internalParse($text)
    {
        $isMain = true;
        // $text = Sanitizer :: removeHTMLtags($text, array(&$this, 'attributeStripCallback'), false,
        // array_keys($this->mTransparentTagHooks));
        
        // Tables need to come after variable replacement for things to work
        // properly; putting them before other transformations should keep
        // exciting things like link expansions from showing up in surprising
        // places.
        $text = $this->doTableStuff($text);
        
        $text = preg_replace('/(^|\n)-----*/', '\\1<hr />', $text);
        //
        // $text = $this->doDoubleUnderscore($text);
        $text = $this->doHeadings($text);
        // //if ($this->mOptions->getUseDynamicDates())
        // //{
        // // $df = DateFormatter :: getInstance();
        // // $text = $df->reformat($this->mOptions->getDateFormat(), $text);
        // //}
        $text = $this->doAllQuotes($text);
        $text = $this->replaceInternalLinks($text);
        // $text = $this->replaceExternalLinks($text);
        //
        // # replaceInternalLinks may sometimes leave behind
        // # absolute URLs, which have to be masked to hide them from replaceExternalLinks
        // $text = str_replace($this->mUniqPrefix . 'NOPARSE', '', $text);
        //
        // $text = $this->doMagicLinks($text);
        $text = $this->formatHeadings($text, $isMain);
        
        return $text;
    }

    /**
     * parse the wiki syntax used to render tables
     * @private
     */
    function doTableStuff($text)
    {
        $lines = MediawikiStringUtils :: explode("\n", $text);
        $out = '';
        $td_history = array(); // Is currently a td tag open?
        $last_tag_history = array(); // Save history of last lag activated (td, th or caption)
        $tr_history = array(); // Is currently a tr tag open?
        $tr_attributes = array(); // history of tr attributes
        $has_opened_tr = array(); // Did this table open a <tr> element?
        $indent_level = 0; // indent level of the table
        
        foreach ($lines as $outLine)
        {
            $line = trim($outLine);
            
            if ($line == '')
            { // empty line, go to next line
                $out .= $outLine . "\n";
                continue;
            }
            $first_character = $line[0];
            $matches = array();
            
            if (preg_match('/^(:*)\{\|(.*)$/', $line, $matches))
            {
                // First check if we are starting a new table
                $indent_level = strlen($matches[1]);
                
                $attributes = $this->mStripState->unstripBoth($matches[2]);
                $attributes = MediawikiSanitizer :: fixTagAttributes($attributes, 'table');
                
                $outLine = str_repeat('<dl><dd>', $indent_level) . "<table{$attributes}>";
                array_push($td_history, false);
                array_push($last_tag_history, '');
                array_push($tr_history, false);
                array_push($tr_attributes, '');
                array_push($has_opened_tr, false);
            }
            else 
                if (count($td_history) == 0)
                {
                    // Don't do any of the following
                    $out .= $outLine . "\n";
                    continue;
                }
                else 
                    if (substr($line, 0, 2) === '|}')
                    {
                        // We are ending a table
                        $line = '</table>' . substr($line, 2);
                        $last_tag = array_pop($last_tag_history);
                        
                        if (! array_pop($has_opened_tr))
                        {
                            $line = "<tr><td></td></tr>{$line}";
                        }
                        
                        if (array_pop($tr_history))
                        {
                            $line = "</tr>{$line}";
                        }
                        
                        if (array_pop($td_history))
                        {
                            $line = "</{$last_tag}>{$line}";
                        }
                        array_pop($tr_attributes);
                        $outLine = $line . str_repeat('</dd></dl>', $indent_level);
                    }
                    else 
                        if (substr($line, 0, 2) === '|-')
                        {
                            // Now we have a table row
                            $line = preg_replace('#^\|-+#', '', $line);
                            
                            // Whats after the tag is now only attributes
                            $attributes = $this->mStripState->unstripBoth($line);
                            $attributes = MediawikiSanitizer :: fixTagAttributes($attributes, 'tr');
                            array_pop($tr_attributes);
                            array_push($tr_attributes, $attributes);
                            
                            $line = '';
                            $last_tag = array_pop($last_tag_history);
                            array_pop($has_opened_tr);
                            array_push($has_opened_tr, true);
                            
                            if (array_pop($tr_history))
                            {
                                $line = '</tr>';
                            }
                            
                            if (array_pop($td_history))
                            {
                                $line = "</{$last_tag}>{$line}";
                            }
                            
                            $outLine = $line;
                            array_push($tr_history, false);
                            array_push($td_history, false);
                            array_push($last_tag_history, '');
                        }
                        else 
                            if ($first_character === '|' || $first_character === '!' || substr($line, 0, 2) === '|+')
                            {
                                // This might be cell elements, td, th or captions
                                if (substr($line, 0, 2) === '|+')
                                {
                                    $first_character = '+';
                                    $line = substr($line, 1);
                                }
                                
                                $line = substr($line, 1);
                                
                                if ($first_character === '!')
                                {
                                    $line = str_replace('!!', '||', $line);
                                }
                                
                                // Split up multiple cells on the same line.
                                // FIXME : This can result in improper nesting of tags processed
                                // by earlier parser steps, but should avoid splitting up eg
                                // attribute values containing literal "||".
                                $cells = MediawikiStringUtils :: explodeMarkup('||', $line);
                                
                                $outLine = '';
                                
                                // Loop through each table cell
                                foreach ($cells as $cell)
                                {
                                    $previous = '';
                                    if ($first_character !== '+')
                                    {
                                        $tr_after = array_pop($tr_attributes);
                                        if (! array_pop($tr_history))
                                        {
                                            $previous = "<tr{$tr_after}>\n";
                                        }
                                        array_push($tr_history, true);
                                        array_push($tr_attributes, '');
                                        array_pop($has_opened_tr);
                                        array_push($has_opened_tr, true);
                                    }
                                    
                                    $last_tag = array_pop($last_tag_history);
                                    
                                    if (array_pop($td_history))
                                    {
                                        $previous = "</{$last_tag}>{$previous}";
                                    }
                                    
                                    if ($first_character === '|')
                                    {
                                        $last_tag = 'td';
                                    }
                                    else 
                                        if ($first_character === '!')
                                        {
                                            $last_tag = 'th';
                                        }
                                        else 
                                            if ($first_character === '+')
                                            {
                                                $last_tag = 'caption';
                                            }
                                            else
                                            {
                                                $last_tag = '';
                                            }
                                    
                                    array_push($last_tag_history, $last_tag);
                                    
                                    // A cell could contain both parameters and data
                                    $cell_data = explode('|', $cell, 2);
                                    
                                    // Bug 553: Note that a '|' inside an invalid link should not
                                    // be mistaken as delimiting cell parameters
                                    if (strpos($cell_data[0], '[[') !== false)
                                    {
                                        $cell = "{$previous}<{$last_tag}>{$cell}";
                                    }
                                    else 
                                        if (count($cell_data) == 1)
                                            $cell = "{$previous}<{$last_tag}>{$cell_data[0]}";
                                        else
                                        {
                                            $attributes = $this->mStripState->unstripBoth($cell_data[0]);
                                            $attributes = MediawikiSanitizer :: fixTagAttributes($attributes, $last_tag);
                                            $cell = "{$previous}<{$last_tag}{$attributes}>{$cell_data[1]}";
                                        }
                                    
                                    $outLine .= $cell;
                                    array_push($td_history, true);
                                }
                            }
            $out .= $outLine . "\n";
        }
        
        // Closing open td, tr && table
        while (count($td_history) > 0)
        {
            if (array_pop($td_history))
            {
                $out .= "</td>\n";
            }
            if (array_pop($tr_history))
            {
                $out .= "</tr>\n";
            }
            if (! array_pop($has_opened_tr))
            {
                $out .= "<tr><td></td></tr>\n";
            }
            
            $out .= "</table>\n";
        }
        
        // Remove trailing line-ending (b/c)
        if (substr($out, - 1) === "\n")
        {
            $out = substr($out, 0, - 1);
        }
        
        // special case: don't return empty table
        if ($out === "<table>\n<tr><td></td></tr>\n</table>")
        {
            $out = '';
        }
        
        return $out;
    }

    /**
     * Parse headers and return html
     * @private
     */
    function doHeadings($text)
    {
        for ($i = 6; $i >= 1; -- $i)
        {
            $h = str_repeat('=', $i);
            $text = preg_replace("/^$h(.+)$h\\s*$/m", "<h$i>\\1</h$i>", $text);
        }
        return $text;
    }

    /**
     * Replace single quotes with HTML markup
     * @private
     * 
     * @return string the altered text
     */
    function doAllQuotes($text)
    {
        $outtext = '';
        $lines = MediawikiStringUtils :: explode("\n", $text);
        foreach ($lines as $line)
        {
            $outtext .= $this->doQuotes($line) . "\n";
        }
        $outtext = substr($outtext, 0, - 1);
        return $outtext;
    }

    /**
     * Helper function for doAllQuotes()
     */
    public function doQuotes($text)
    {
        $arr = preg_split("/(''+)/", $text, - 1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($arr) == 1)
            return $text;
        else
        {
            // First, do some preliminary work. This may shift some apostrophes from
            // being mark-up to being text. It also counts the number of occurrences
            // of bold and italics mark-ups.
            $i = 0;
            $numbold = 0;
            $numitalics = 0;
            foreach ($arr as $r)
            {
                if (($i % 2) == 1)
                {
                    // If there are ever four apostrophes, assume the first is supposed to
                    // be text, and the remaining three constitute mark-up for bold text.
                    if (strlen($arr[$i]) == 4)
                    {
                        $arr[$i - 1] .= "'";
                        $arr[$i] = "'''";
                    }
                    // If there are more than 5 apostrophes in a row, assume they're all
                    // text except for the last 5.
                    else 
                        if (strlen($arr[$i]) > 5)
                        {
                            $arr[$i - 1] .= str_repeat("'", strlen($arr[$i]) - 5);
                            $arr[$i] = "'''''";
                        }
                    // Count the number of occurrences of bold and italics mark-ups.
                    // We are not counting sequences of five apostrophes.
                    if (strlen($arr[$i]) == 2)
                    {
                        $numitalics ++;
                    }
                    else 
                        if (strlen($arr[$i]) == 3)
                        {
                            $numbold ++;
                        }
                        else 
                            if (strlen($arr[$i]) == 5)
                            {
                                $numitalics ++;
                                $numbold ++;
                            }
                }
                $i ++;
            }
            
            // If there is an odd number of both bold and italics, it is likely
            // that one of the bold ones was meant to be an apostrophe followed
            // by italics. Which one we cannot know for certain, but it is more
            // likely to be one that has a single-letter word before it.
            if (($numbold % 2 == 1) && ($numitalics % 2 == 1))
            {
                $i = 0;
                $firstsingleletterword = - 1;
                $firstmultiletterword = - 1;
                $firstspace = - 1;
                foreach ($arr as $r)
                {
                    if (($i % 2 == 1) and (strlen($r) == 3))
                    {
                        $x1 = substr($arr[$i - 1], - 1);
                        $x2 = substr($arr[$i - 1], - 2, 1);
                        if ($x1 === ' ')
                        {
                            if ($firstspace == - 1)
                                $firstspace = $i;
                        }
                        else 
                            if ($x2 === ' ')
                            {
                                if ($firstsingleletterword == - 1)
                                    $firstsingleletterword = $i;
                            }
                            else
                            {
                                if ($firstmultiletterword == - 1)
                                    $firstmultiletterword = $i;
                            }
                    }
                    $i ++;
                }
                
                // If there is a single-letter word, use it!
                if ($firstsingleletterword > - 1)
                {
                    $arr[$firstsingleletterword] = "''";
                    $arr[$firstsingleletterword - 1] .= "'";
                }
                // If not, but there's a multi-letter word, use that one.
                else 
                    if ($firstmultiletterword > - 1)
                    {
                        $arr[$firstmultiletterword] = "''";
                        $arr[$firstmultiletterword - 1] .= "'";
                    }
                    // ... otherwise use the first one that has neither.
                    // (notice that it is possible for all three to be -1 if, for example,
                    // there is only one pentuple-apostrophe in the line)
                    else 
                        if ($firstspace > - 1)
                        {
                            $arr[$firstspace] = "''";
                            $arr[$firstspace - 1] .= "'";
                        }
            }
            
            // Now let's actually convert our apostrophic mush to HTML!
            $output = '';
            $buffer = '';
            $state = '';
            $i = 0;
            foreach ($arr as $r)
            {
                if (($i % 2) == 0)
                {
                    if ($state === 'both')
                        $buffer .= $r;
                    else
                        $output .= $r;
                }
                else
                {
                    if (strlen($r) == 2)
                    {
                        if ($state === 'i')
                        {
                            $output .= '</i>';
                            $state = '';
                        }
                        else 
                            if ($state === 'bi')
                            {
                                $output .= '</i>';
                                $state = 'b';
                            }
                            else 
                                if ($state === 'ib')
                                {
                                    $output .= '</b></i><b>';
                                    $state = 'b';
                                }
                                else 
                                    if ($state === 'both')
                                    {
                                        $output .= '<b><i>' . $buffer . '</i>';
                                        $state = 'b';
                                    }
                                    else // $state can be 'b' or ''
                                    {
                                        $output .= '<i>';
                                        $state .= 'i';
                                    }
                    }
                    else 
                        if (strlen($r) == 3)
                        {
                            if ($state === 'b')
                            {
                                $output .= '</b>';
                                $state = '';
                            }
                            else 
                                if ($state === 'bi')
                                {
                                    $output .= '</i></b><i>';
                                    $state = 'i';
                                }
                                else 
                                    if ($state === 'ib')
                                    {
                                        $output .= '</b>';
                                        $state = 'i';
                                    }
                                    else 
                                        if ($state === 'both')
                                        {
                                            $output .= '<i><b>' . $buffer . '</b>';
                                            $state = 'i';
                                        }
                                        else // $state can be 'i' or ''
                                        {
                                            $output .= '<b>';
                                            $state .= 'b';
                                        }
                        }
                        else 
                            if (strlen($r) == 5)
                            {
                                if ($state === 'b')
                                {
                                    $output .= '</b><i>';
                                    $state = 'i';
                                }
                                else 
                                    if ($state === 'i')
                                    {
                                        $output .= '</i><b>';
                                        $state = 'b';
                                    }
                                    else 
                                        if ($state === 'bi')
                                        {
                                            $output .= '</i></b>';
                                            $state = '';
                                        }
                                        else 
                                            if ($state === 'ib')
                                            {
                                                $output .= '</b></i>';
                                                $state = '';
                                            }
                                            else 
                                                if ($state === 'both')
                                                {
                                                    $output .= '<i><b>' . $buffer . '</b></i>';
                                                    $state = '';
                                                }
                                                else // ($state == '')
                                                {
                                                    $buffer = '';
                                                    $state = 'both';
                                                }
                            }
                }
                $i ++;
            }
            // Now close all remaining tags. Notice that the order is important.
            if ($state === 'b' || $state === 'ib')
                $output .= '</b>';
            if ($state === 'i' || $state === 'bi' || $state === 'ib')
                $output .= '</i>';
            if ($state === 'bi')
                $output .= '</b>';
                
                // There might be lonely ''''', so make sure we have a buffer
            if ($state === 'both' && $buffer)
                $output .= '<b><i>' . $buffer . '</i></b>';
            return $output;
        }
    }

    /**
     * Make lists from lines starting with ':', '*', '#', etc.
     * (DBL)
     * @private
     * 
     * @return string the lists rendered as HTML
     */
    function doBlockLevels($text, $linestart)
    {
        // Parsing through the text line by line. The main thing
        // happening here is handling of block-level elements p, pre,
        // and making lists from lines starting with * # : etc.
        //
        $textLines = MediawikiStringUtils :: explode("\n", $text);
        
        $lastPrefix = $output = '';
        $this->mDTopen = $inBlockElem = false;
        $prefixLength = 0;
        $paragraphStack = false;
        
        foreach ($textLines as $oLine)
        {
            // Fix up $linestart
            if (! $linestart)
            {
                $output .= $oLine;
                $linestart = true;
                continue;
            }
            
            $lastPrefixLength = strlen($lastPrefix);
            $preCloseMatch = preg_match('/<\\/pre/i', $oLine);
            $preOpenMatch = preg_match('/<pre/i', $oLine);
            if (! $this->mInPre)
            {
                // Multiple prefixes may abut each other for nested lists.
                $prefixLength = strspn($oLine, '*#:;');
                $prefix = substr($oLine, 0, $prefixLength);
                
                // eh?
                $prefix2 = str_replace(';', ':', $prefix);
                $t = substr($oLine, $prefixLength);
                $this->mInPre = (bool) $preOpenMatch;
            }
            else
            {
                // Don't interpret any other prefixes in preformatted text
                $prefixLength = 0;
                $prefix = $prefix2 = '';
                $t = $oLine;
            }
            
            // List generation
            if ($prefixLength && $lastPrefix === $prefix2)
            {
                // Same as the last item, so no need to deal with nesting or opening stuff
                $output .= $this->nextItem(substr($prefix, - 1));
                $paragraphStack = false;
                
                if (substr($prefix, - 1) === ';')
                {
                    // The one nasty exception: definition lists work like this:
                    // ; title : definition text
                    // So we check for : in the remainder text to split up the
                    // title and definition, without b0rking links.
                    $term = $t2 = '';
                    if ($this->findColonNoLinks($t, $term, $t2) !== false)
                    {
                        $t = $t2;
                        $output .= $term . $this->nextItem(':');
                    }
                }
            }
            elseif ($prefixLength || $lastPrefixLength)
            {
                // Either open or close a level...
                $commonPrefixLength = $this->getCommon($prefix, $lastPrefix);
                $paragraphStack = false;
                
                while ($commonPrefixLength < $lastPrefixLength)
                {
                    $output .= $this->closeList($lastPrefix[$lastPrefixLength - 1]);
                    -- $lastPrefixLength;
                }
                if ($prefixLength <= $commonPrefixLength && $commonPrefixLength > 0)
                {
                    $output .= $this->nextItem($prefix[$commonPrefixLength - 1]);
                }
                while ($prefixLength > $commonPrefixLength)
                {
                    $char = substr($prefix, $commonPrefixLength, 1);
                    $output .= $this->openList($char);
                    
                    if (';' === $char)
                    {
                        // FIXME: This is dupe of code above
                        if ($this->findColonNoLinks($t, $term, $t2) !== false)
                        {
                            $t = $t2;
                            $output .= $term . $this->nextItem(':');
                        }
                    }
                    ++ $commonPrefixLength;
                }
                $lastPrefix = $prefix2;
            }
            if (0 == $prefixLength)
            {
                // No prefix (not in list)--go to paragraph mode
                // XXX: use a stack for nestable elements like span, table and div
                $openmatch = preg_match(
                    '/(?:<table|<blockquote|<h1|<h2|<h3|<h4|<h5|<h6|<pre|<tr|<p|<ul|<ol|<li|<\\/tr|<\\/td|<\\/th)/iS', 
                    $t);
                $closematch = preg_match(
                    '/(?:<\\/table|<\\/blockquote|<\\/h1|<\\/h2|<\\/h3|<\\/h4|<\\/h5|<\\/h6|' .
                         '<td|<th|<\\/?div|<hr|<\\/pre|<\\/p|' . $this->mUniqPrefix .
                         '-pre|<\\/li|<\\/ul|<\\/ol|<\\/?center)/iS', 
                        $t);
                if ($openmatch or $closematch)
                {
                    $paragraphStack = false;
                    //  TODO bug 5718: paragraph closed
                    $output .= $this->closeParagraph();
                    if ($preOpenMatch and ! $preCloseMatch)
                    {
                        $this->mInPre = true;
                    }
                    if ($closematch)
                    {
                        $inBlockElem = false;
                    }
                    else
                    {
                        $inBlockElem = true;
                    }
                }
                else 
                    if (! $inBlockElem && ! $this->mInPre)
                    {
                        if (' ' == substr($t, 0, 1) and ($this->mLastSection === 'pre' or trim($t) != ''))
                        {
                            // pre
                            if ($this->mLastSection !== 'pre')
                            {
                                $paragraphStack = false;
                                $output .= $this->closeParagraph() . '<pre>';
                                $this->mLastSection = 'pre';
                            }
                            $t = substr($t, 1);
                        }
                        else
                        {
                            // paragraph
                            if ('' == trim($t))
                            {
                                if ($paragraphStack)
                                {
                                    $output .= $paragraphStack . '<br />';
                                    $paragraphStack = false;
                                    $this->mLastSection = 'p';
                                }
                                else
                                {
                                    if ($this->mLastSection !== 'p')
                                    {
                                        $output .= $this->closeParagraph();
                                        $this->mLastSection = '';
                                        $paragraphStack = '<p>';
                                    }
                                    else
                                    {
                                        $paragraphStack = '</p><p>';
                                    }
                                }
                            }
                            else
                            {
                                if ($paragraphStack)
                                {
                                    $output .= $paragraphStack;
                                    $paragraphStack = false;
                                    $this->mLastSection = 'p';
                                }
                                else 
                                    if ($this->mLastSection !== 'p')
                                    {
                                        $output .= $this->closeParagraph() . '<p>';
                                        $this->mLastSection = 'p';
                                    }
                            }
                        }
                    }
            }
            // somewhere above we forget to get out of pre block (bug 785)
            if ($preCloseMatch && $this->mInPre)
            {
                $this->mInPre = false;
            }
            if ($paragraphStack === false)
            {
                $output .= $t . "\n";
            }
        }
        while ($prefixLength)
        {
            $output .= $this->closeList($prefix2[$prefixLength - 1]);
            -- $prefixLength;
        }
        if ('' != $this->mLastSection)
        {
            $output .= '</' . $this->mLastSection . '>';
            $this->mLastSection = '';
        }
        
        return $output;
    }
    
    /* private */
    function nextItem($char)
    {
        if ('*' === $char || '#' === $char)
        {
            return '</li><li>';
        }
        else 
            if (':' === $char || ';' === $char)
            {
                $close = '</dd>';
                if ($this->mDTopen)
                {
                    $close = '</dt>';
                }
                if (';' === $char)
                {
                    $this->mDTopen = true;
                    return $close . '<dt>';
                }
                else
                {
                    $this->mDTopen = false;
                    return $close . '<dd>';
                }
            }
        return '<!-- ERR 2 -->';
    }

    /**
     * Split up a string on ':', ignoring any occurences inside tags
     * to prevent illegal overlapping.
     * 
     * @param string $str the string to split
     * @param string &$before set to everything before the ':'
     * @param string &$after set to everything after the ':'
     *        return string the position of the ':', or false if none found
     */
    function findColonNoLinks($str, &$before, &$after)
    {
        $pos = strpos($str, ':');
        if ($pos === false)
        {
            // Nothing to find!
            return false;
        }
        
        $lt = strpos($str, '<');
        if ($lt === false || $lt > $pos)
        {
            // Easy; no tag nesting to worry about
            $before = substr($str, 0, $pos);
            $after = substr($str, $pos + 1);
            return $pos;
        }
        
        // Ugly state machine to walk through avoiding tags.
        $state = self :: COLON_STATE_TEXT;
        $stack = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i ++)
        {
            $c = $str{$i};
            
            switch ($state)
            {
                // (Using the number is a performance hack for common cases)
                case 0 : // self::COLON_STATE_TEXT:
                    switch ($c)
                    {
                        case "<" :
                            // Could be either a <start> tag or an </end> tag
                            $state = self :: COLON_STATE_TAGSTART;
                            break;
                        case ":" :
                            if ($stack == 0)
                            {
                                // We found it!
                                $before = substr($str, 0, $i);
                                $after = substr($str, $i + 1);
                                return $i;
                            }
                            // Embedded in a tag; don't break it.
                            break;
                        default :
                            // Skip ahead looking for something interesting
                            $colon = strpos($str, ':', $i);
                            if ($colon === false)
                            {
                                // Nothing else interesting
                                return false;
                            }
                            $lt = strpos($str, '<', $i);
                            if ($stack === 0)
                            {
                                if ($lt === false || $colon < $lt)
                                {
                                    // We found it!
                                    $before = substr($str, 0, $colon);
                                    $after = substr($str, $colon + 1);
                                    return $i;
                                }
                            }
                            if ($lt === false)
                            {
                                // Nothing else interesting to find; abort!
                                // We're nested, but there's no close tags left. Abort!
                                break 2;
                            }
                            // Skip ahead to next tag start
                            $i = $lt;
                            $state = self :: COLON_STATE_TAGSTART;
                    }
                    break;
                case 1 : // self::COLON_STATE_TAG:
                         // In a <tag>
                    switch ($c)
                    {
                        case ">" :
                            $stack ++;
                            $state = self :: COLON_STATE_TEXT;
                            break;
                        case "/" :
                            // Slash may be followed by >?
                            $state = self :: COLON_STATE_TAGSLASH;
                            break;
                        default :
                        
                        // ignore
                    }
                    break;
                case 2 : // self::COLON_STATE_TAGSTART:
                    switch ($c)
                    {
                        case "/" :
                            $state = self :: COLON_STATE_CLOSETAG;
                            break;
                        case "!" :
                            $state = self :: COLON_STATE_COMMENT;
                            break;
                        case ">" :
                            // Illegal early close? This shouldn't happen D:
                            $state = self :: COLON_STATE_TEXT;
                            break;
                        default :
                            $state = self :: COLON_STATE_TAG;
                    }
                    break;
                case 3 : // self::COLON_STATE_CLOSETAG:
                         // In a </tag>
                    if ($c === ">")
                    {
                        $stack --;
                        if ($stack < 0)
                        {
                            return false;
                        }
                        $state = self :: COLON_STATE_TEXT;
                    }
                    break;
                case self :: COLON_STATE_TAGSLASH :
                    if ($c === ">")
                    {
                        // Yes, a self-closed tag <blah/>
                        $state = self :: COLON_STATE_TEXT;
                    }
                    else
                    {
                        // Probably we're jumping the gun, and this is an attribute
                        $state = self :: COLON_STATE_TAG;
                    }
                    break;
                case 5 : // self::COLON_STATE_COMMENT:
                    if ($c === "-")
                    {
                        $state = self :: COLON_STATE_COMMENTDASH;
                    }
                    break;
                case self :: COLON_STATE_COMMENTDASH :
                    if ($c === "-")
                    {
                        $state = self :: COLON_STATE_COMMENTDASHDASH;
                    }
                    else
                    {
                        $state = self :: COLON_STATE_COMMENT;
                    }
                    break;
                case self :: COLON_STATE_COMMENTDASHDASH :
                    if ($c === ">")
                    {
                        $state = self :: COLON_STATE_TEXT;
                    }
                    else
                    {
                        $state = self :: COLON_STATE_COMMENT;
                    }
                    break;
                default :
                    throw new MWException("State machine error in " . __METHOD__);
            }
        }
        if ($stack > 0)
        {
            return false;
        }
        return false;
    }
    
    // getCommon() returns the length of the longest common substring
    // of both arguments, starting at the beginning of both.
    //
    function getCommon($st1, $st2)
    {
        $fl = strlen($st1);
        $shorter = strlen($st2);
        if ($fl < $shorter)
        {
            $shorter = $fl;
        }
        
        for ($i = 0; $i < $shorter; ++ $i)
        {
            if ($st1{$i} != $st2{$i})
            {
                break;
            }
        }
        return $i;
    }

    function closeList($char)
    {
        if ('*' === $char)
        {
            $text = '</li></ul>';
        }
        else 
            if ('#' === $char)
            {
                $text = '</li></ol>';
            }
            else 
                if (':' === $char)
                {
                    if ($this->mDTopen)
                    {
                        $this->mDTopen = false;
                        $text = '</dt></dl>';
                    }
                    else
                    {
                        $text = '</dd></dl>';
                    }
                }
                else
                {
                    return '<!-- ERR 3 -->';
                }
        return $text . "\n";
    }
    
    // These next three functions open, continue, and close the list
    // element appropriate to the prefix character passed into them.
    //
    function openList($char)
    {
        $result = $this->closeParagraph();
        
        if ('*' === $char)
        {
            $result .= '<ul><li>';
        }
        else 
            if ('#' === $char)
            {
                $result .= '<ol><li>';
            }
            else 
                if (':' === $char)
                {
                    $result .= '<dl><dd>';
                }
                else 
                    if (';' === $char)
                    {
                        $result .= '<dl><dt>';
                        $this->mDTopen = true;
                    }
                    else
                    {
                        $result = '<!-- ERR 1 -->';
                    }
        
        return $result;
    }

    /**
     * #@+
     * Used by doBlockLevels()
     * @private
     */
    function closeParagraph()
    {
        $result = '';
        if ('' != $this->mLastSection)
        {
            $result = '</' . $this->mLastSection . ">\n";
        }
        $this->mInPre = false;
        $this->mLastSection = '';
        return $result;
    }

    /**
     * This function accomplishes several tasks:
     * 1) Auto-number headings if that option is enabled
     * 2) Add an [edit] link to sections for users who have enabled the option and can edit the page
     * 3) Add a Table of contents on the top for users who have enabled the option
     * 4) Auto-anchor headings
     * It loops through all headlines, collects the necessary data, then splits up the
     * string and re-inserts the newly formatted headlines.
     * 
     * @param string $text
     * @param boolean $isMain @private
     */
    function formatHeadings($text)
    {
        $wgMaxTocLevel = 3;
        $doNumberHeadings = false;
        
        // Get all headlines for numbering them and adding funky stuff like [edit]
        // links - this is for later, but we need the number of headlines right now
        $matches = array();
        $numMatches = preg_match_all(
            '/<H(?P<level>[1-6])(?P<attrib>.*?' . '>)(?P<header>.*?)<\/H[1-6] *>/i', 
            $text, 
            $matches);
        
        // if there are fewer than 4 headlines in the article, do not show TOC
        $enoughToc = ($numMatches >= 4);
        
        // headline counter
        $headlineCount = 0;
        $numVisible = 0;
        
        // Ugh .. the TOC should have neat indentation levels which can be
        // passed to the skin functions. These are determined here
        $toc = '';
        $full = '';
        $head = array();
        $sublevelCount = array();
        $levelCount = array();
        $toclevel = 0;
        $level = 0;
        $prevlevel = 0;
        $toclevel = 0;
        $prevtoclevel = 0;
        $markerRegex = "{$this->mUniqPrefix}-h-(\d+)-" . self :: MARKER_SUFFIX;
        // $baseTitleText = $this->mTitle->getPrefixedDBkey();
        $tocraw = array();
        
        foreach ($matches[3] as $headline)
        {
            $isTemplate = false;
            $titleText = false;
            $sectionIndex = false;
            $numbering = '';
            $markerMatches = array();
            // if (preg_match("/^$markerRegex/", $headline, $markerMatches))
            // {
            // $serial = $markerMatches[1];
            // list($titleText, $sectionIndex) = $this->mHeadings[$serial];
            // $isTemplate = ($titleText != $baseTitleText);
            // $headline = preg_replace("/^$markerRegex/", "", $headline);
            // }
            
            if ($toclevel)
            {
                $prevlevel = $level;
                $prevtoclevel = $toclevel;
            }
            $level = $matches[1][$headlineCount];
            
            if ($doNumberHeadings || $enoughToc)
            {
                
                if ($level > $prevlevel)
                {
                    // Increase TOC level
                    $toclevel ++;
                    $sublevelCount[$toclevel] = 0;
                    if ($toclevel < $wgMaxTocLevel)
                    {
                        $prevtoclevel = $toclevel;
                        $toc .= MediawikiLinker :: tocIndent();
                        $numVisible ++;
                    }
                }
                elseif ($level < $prevlevel && $toclevel > 1)
                {
                    // Decrease TOC level, find level to jump to
                    
                    if ($toclevel == 2 && $level <= $levelCount[1])
                    {
                        // Can only go down to level 1
                        $toclevel = 1;
                    }
                    else
                    {
                        for ($i = $toclevel; $i > 0; $i --)
                        {
                            if ($levelCount[$i] == $level)
                            {
                                // Found last matching level
                                $toclevel = $i;
                                break;
                            }
                            elseif ($levelCount[$i] < $level)
                            {
                                // Found first matching level below current level
                                $toclevel = $i + 1;
                                break;
                            }
                        }
                    }
                    if ($toclevel < $wgMaxTocLevel)
                    {
                        if ($prevtoclevel < $wgMaxTocLevel)
                        {
                            // Unindent only if the previous toc level was shown :p
                            $toc .= MediawikiLinker :: tocUnindent($prevtoclevel - $toclevel);
                            $prevtoclevel = $toclevel;
                        }
                        else
                        {
                            $toc .= MediawikiLinker :: tocLineEnd();
                        }
                    }
                }
                else
                {
                    // No change in level, end TOC line
                    if ($toclevel < $wgMaxTocLevel)
                    {
                        $toc .= MediawikiLinker :: tocLineEnd();
                    }
                }
                
                $levelCount[$toclevel] = $level;
                
                // count number of headlines for each level
                @$sublevelCount[$toclevel] ++;
                $dot = 0;
                for ($i = 1; $i <= $toclevel; $i ++)
                {
                    if (! empty($sublevelCount[$i]))
                    {
                        if ($dot)
                        {
                            $numbering .= '.';
                        }
                        // $numbering .= $wgContLang->formatNum($sublevelCount[$i]);
                        $numbering .= $sublevelCount[$i];
                        $dot = 1;
                    }
                }
            }
            
            // The safe header is a version of the header text safe to use for links
            // Avoid insertion of weird stuff like <math> by expanding the relevant sections
            $safeHeadline = $this->mStripState->unstripBoth($headline);
            
            // Remove link placeholders by the link text.
            // <!--LINK number-->
            // turns into
            // link text with suffix
            // $safeHeadline = $this->replaceLinkHoldersText($safeHeadline);
            
            // Strip out HTML (other than plain <sup> and <sub>: bug 8393)
            $tocline = preg_replace(
                array('#<(?!/?(sup|sub)).*?' . '>#', '#<(/?(sup|sub)).*?' . '>#'), 
                array('', '<$1>'), 
                $safeHeadline);
            $tocline = trim($tocline);
            
            // For the anchor, strip out HTML-y stuff period
            $safeHeadline = preg_replace('/<.*?' . '>/', '', $safeHeadline);
            $safeHeadline = trim($safeHeadline);
            
            // Save headline for section edit hint before it's escaped
            $headlineHint = $safeHeadline;
            
            $legacyHeadline = false;
            $safeHeadline = MediawikiSanitizer :: escapeId($safeHeadline, 'noninitial');
            
            // HTML names must be case-insensitively unique (bug 10721). FIXME:
            // Does this apply to Unicode characters? Because we aren't
            // handling those here.
            $arrayKey = strtolower($safeHeadline);
            if ($legacyHeadline === false)
            {
                $legacyArrayKey = false;
            }
            else
            {
                $legacyArrayKey = strtolower($legacyHeadline);
            }
            
            // count how many in assoc. array so we can track dupes in anchors
            if (isset($refers[$arrayKey]))
            {
                $refers[$arrayKey] ++;
            }
            else
            {
                $refers[$arrayKey] = 1;
            }
            if (isset($refers[$legacyArrayKey]))
            {
                $refers[$legacyArrayKey] ++;
            }
            else
            {
                $refers[$legacyArrayKey] = 1;
            }
            
            // Don't number the heading if it is the only one (looks silly)
            if ($doNumberHeadings && count($matches[3]) > 1)
            {
                // the two are different if the line contains a link
                $headline = $numbering . ' ' . $headline;
            }
            
            // Create the anchor for linking from the TOC to the section
            $anchor = $safeHeadline;
            $legacyAnchor = $legacyHeadline;
            if ($refers[$arrayKey] > 1)
            {
                $anchor .= '_' . $refers[$arrayKey];
            }
            if ($legacyHeadline !== false && $refers[$legacyArrayKey] > 1)
            {
                $legacyAnchor .= '_' . $refers[$legacyArrayKey];
            }
            if ($enoughToc && (! isset($wgMaxTocLevel) || $toclevel < $wgMaxTocLevel))
            {
                $toc .= MediawikiLinker :: tocLine($anchor, $tocline, $numbering, $toclevel);
                
                $tocraw[] = array(
                    'toclevel' => $toclevel, 
                    'level' => $level, 
                    'line' => $tocline, 
                    'number' => $numbering);
            }
            // give headline the correct <h#> tag
            $head[$headlineCount] = MediawikiLinker :: makeHeadline(
                $level, 
                $matches['attrib'][$headlineCount], 
                $anchor, 
                $headline, 
                $editlink, 
                $legacyAnchor);
            
            $headlineCount ++;
        }
        
        // $this->mOutput->setSections($tocraw);
        
        // Never ever show TOC if no headers
        if ($numVisible < 1)
        {
            $enoughToc = false;
        }
        
        if ($enoughToc)
        {
            if ($prevtoclevel > 0 && $prevtoclevel < $wgMaxTocLevel)
            {
                $toc .= MediawikiLinker :: tocUnindent($prevtoclevel - 1);
            }
            
            $toc = MediawikiLinker :: tocList($toc);
        }
        
        // split up and insert constructed headlines
        
        $blocks = preg_split('/<H[1-6].*?' . '>.*?<\/H[1-6]>/i', $text);
        $i = 0;
        
        foreach ($blocks as $block)
        {
            $full .= $block;
            if ($enoughToc && ! $i)
            {
                // Top anchor now in skin
                $full = $full . $toc;
            }
            
            if (! empty($head[$i]))
            {
                $full .= $head[$i];
            }
            $i ++;
        }
        
        return $full;
    }

    /**
     * Process [[ ]] wikilinks
     * 
     * @return processed text
     *         @private
     */
    function replaceInternalLinks($s)
    {
        $this->mLinkHolders->merge($this->replaceInternalLinks2($s));
        return $s;
    }

    /**
     * Process [[ ]] wikilinks (RIL)
     * 
     * @return LinkHolderArray @private
     */
    function replaceInternalLinks2(&$s)
    {
        static $tc = FALSE, $e1, $e1_img;
        // the % is needed to support urlencoded titles as well
        if (! $tc)
        {
            $tc = MediawikiTitle :: legalChars() . '#%';
            // Match a link having the form [[namespace:link|alternate]]trail
            $e1 = "/^([{$tc}]+)(?:\\|(.+?))?]](.*)\$/sD";
            // Match cases where there is no "]]", which might still be images
            $e1_img = "/^([{$tc}]+)\\|(.*)\$/sD";
        }
        
        $holders = new MediawikiLinkHolderArray($this);
        
        // split the entire text string on occurences of [[
        $a = MediawikiStringUtils :: explode('[[', ' ' . $s);
        // get the first element (all text up to first [[), and remove the space we added
        $s = $a->current();
        $a->next();
        $line = $a->current(); // Workaround for broken ArrayIterator::next() that returns "void"
        $s = substr($s, 1);
        
        $e2 = null;
        
        $prefix = '';
        
        $selflink = array($this->mediawiki_parser_context->get_title());
        
        // Loop for each link
        for (; $line !== false && $line !== null; $a->next(), $line = $a->current())
        {
            // Check for excessive memory usage
            if ($holders->isBig())
            {
                // Too big
                // Do the existence check, replace the link holders and clear the array
                $holders->replace($s);
                $holders->clear();
            }
            
            $might_be_img = false;
            
            if (preg_match($e1, $line, $m))
            { // page with normal text or alt
                $text = $m[2];
                // If we get a ] at the beginning of $m[3] that means we have a link that's something like:
                // [[Image:Foo.jpg|[http://example.com desc]]] <- having three ] in a row fucks up,
                // the real problem is with the $e1 regex
                // See bug 1300.
                //
                // Still some problems for cases where the ] is meant to be outside punctuation,
                // and no image is in sight. See bug 2095.
                //
                if ($text !== '' && substr($m[3], 0, 1) === ']' && strpos($text, '[') !== false)
                {
                    $text .= ']'; // so that replaceExternalLinks($text) works later
                    $m[3] = substr($m[3], 1);
                }
                // fix up urlencoded title texts
                if (strpos($m[1], '%') !== false)
                {
                    // Should anchors '#' also be rejected?
                    $m[1] = str_replace(array('<', '>'), array('&lt;', '&gt;'), urldecode($m[1]));
                }
                $trail = $m[3];
            }
            elseif (preg_match($e1_img, $line, $m))
            { // Invalid, but might be an image with a link in its caption
                $might_be_img = true;
                $text = $m[2];
                if (strpos($m[1], '%') !== false)
                {
                    $m[1] = urldecode($m[1]);
                }
                $trail = "";
            }
            else
            { // Invalid form; output directly
                $s .= $prefix . '[[' . $line;
                continue;
            }
            
            // Don't allow internal links to pages containing
            // PROTO: where PROTO is a valid URL protocol; these
            // should be external links.
            if (preg_match('/^\b(?:' . wfUrlProtocols() . ')/', $m[1]))
            {
                $s .= $prefix . '[[' . $line;
                continue;
            }
            
            $link = $m[1];
            
            $noforce = (substr($m[1], 0, 1) !== ':');
            if (! $noforce)
            {
                // Strip off leading ':'
                $link = substr($link, 1);
            }
            
            $nt = MediawikiTitle :: newFromText($this->mStripState->unstripNoWiki($link));
            if ($nt === NULL)
            {
                $s .= $prefix . '[[' . $line;
                continue;
            }
            
            $ns = $nt->getNamespace();
            $iw = $nt->getInterWiki();
            
            if ($might_be_img)
            { // if this is actually an invalid link
                if ($ns == NS_FILE && $noforce)
                { // but might be an image
                    $found = false;
                    while (true)
                    {
                        // look at the next 'line' to see if we can close it there
                        $a->next();
                        $next_line = $a->current();
                        if ($next_line === false || $next_line === null)
                        {
                            break;
                        }
                        $m = explode(']]', $next_line, 3);
                        if (count($m) == 3)
                        {
                            // the first ]] closes the inner link, the second the image
                            $found = true;
                            $text .= "[[{$m[0]}]]{$m[1]}";
                            $trail = $m[2];
                            break;
                        }
                        elseif (count($m) == 2)
                        {
                            // if there's exactly one ]] that's fine, we'll keep looking
                            $text .= "[[{$m[0]}]]{$m[1]}";
                        }
                        else
                        {
                            // if $next_line is invalid too, we need look no further
                            $text .= '[[' . $next_line;
                            break;
                        }
                    }
                    if (! $found)
                    {
                        // we couldn't find the end of this imageLink, so output it raw
                        // but don't ignore what might be perfectly normal links in the text we've examined
                        $holders->merge($this->replaceInternalLinks2($text));
                        $s .= "{$prefix}[[$link|$text";
                        // note: no $trail, because without an end, there *is* no trail
                        continue;
                    }
                }
                else
                { // it's not an image, so output it raw
                    $s .= "{$prefix}[[$link|$text";
                    // note: no $trail, because without an end, there *is* no trail
                    continue;
                }
            }
            
            $wasblank = ('' == $text);
            if ($wasblank)
                $text = $link;
                
                // Link not escaped by : , create the various objects
            if ($noforce)
            {
                
                // Interwikis
                if ($iw && $this->mOptions->getInterwikiMagic() && $wgContLang->getLanguageName($iw))
                {
                    $this->mOutput->addLanguageLink($nt->getFullText());
                    $s = rtrim($s . $prefix);
                    $s .= trim($trail, "\n") == '' ? '' : $prefix . $trail;
                    continue;
                }
                
                if ($ns == NS_FILE)
                {
                    if (! wfIsBadImage($nt->getDBkey(), $this->mTitle))
                    {
                        // recursively parse links inside the image caption
                        // actually, this will parse them in any other parameters, too,
                        // but it might be hard to fix that, and it doesn't matter ATM
                        $text = $this->replaceExternalLinks($text);
                        $holders->merge($this->replaceInternalLinks2($text));
                        
                        // cloak any absolute URLs inside the image markup, so replaceExternalLinks() won't touch them
                        $s .= $prefix . $this->armorLinks($this->makeImage($nt, $text, $holders)) . $trail;
                    }
                    $this->mOutput->addImage($nt->getDBkey());
                    continue;
                }
                
                if ($ns == NS_CATEGORY)
                {
                    $s = rtrim($s . "\n"); // bug 87
                    
                    if ($wasblank)
                    {
                        $sortkey = $this->getDefaultSort();
                    }
                    else
                    {
                        $sortkey = $text;
                    }
                    $sortkey = Sanitizer :: decodeCharReferences($sortkey);
                    $sortkey = str_replace("\n", '', $sortkey);
                    $sortkey = $wgContLang->convertCategoryKey($sortkey);
                    $this->mOutput->addCategory($nt->getDBkey(), $sortkey);
                    
                    /**
                     * Strip the whitespace Category links produce, see bug 87
                     * 
                     * @todo We might want to use trim($tmp, "\n") here.
                     */
                    $s .= trim($prefix . $trail, "\n") == '' ? '' : $prefix . $trail;
                    
                    continue;
                }
            }
            
            // Self-link checking
            if ($nt->getFragment() === '' && $ns != NS_SPECIAL)
            {
                if (in_array($nt->getPrefixedText(), $selflink, true))
                {
                    $s .= $prefix . $sk->makeSelfLinkObj($nt, $text, '', $trail);
                    continue;
                }
            }
            
            // NS_MEDIA is a pseudo-namespace for linking directly to a file
            // FIXME: Should do batch file existence checks, see comment below
            if ($ns == NS_MEDIA)
            {
                // Give extensions a chance to select the file revision for us
                $skip = $time = false;
                if ($skip)
                {
                    $link = $sk->link($nt);
                }
                else
                {
                    $link = $sk->makeMediaLinkObj($nt, $text, $time);
                }
                // Cloak with NOPARSE to avoid replacement in replaceExternalLinks
                $s .= $prefix . $this->armorLinks($link) . $trail;
                $this->mOutput->addImage($nt->getDBkey());
                continue;
            }
            
            // Some titles, such as valid special pages or files in foreign repos, should
            // be shown as bluelinks even though they're not included in the page table
            //
            // FIXME: isAlwaysKnown() can be expensive for file links; we should really do
            // batch file existence checks for NS_FILE and NS_MEDIA
            if ($iw == '' && $nt->isAlwaysKnown())
            {
                $s .= $this->makeKnownLinkHolder($nt, $text, array(), $trail, $prefix);
            }
            else
            {
                // Links will be added to the output link list after checking
                $s .= $holders->makeHolder($nt, $text, '', $trail, $prefix);
            }
        }
        return $holders;
    }

    function nextLinkID()
    {
        return $this->mLinkID ++;
    }

    /**
     * Render a forced-blue link inline; protect against double expansion of
     * URLs if we're in a mode that prepends full URL prefixes to internal links.
     * Since this little disaster has to split off the trail text to avoid
     * breaking URLs in the following text without breaking trails on the
     * wiki links, it's been made into a horrible function.
     * 
     * @param Title $nt
     * @param string $text
     * @param string $query
     * @param string $trail
     * @param string $prefix
     * @return string HTML-wikitext mix oh yuck
     */
    function makeKnownLinkHolder($nt, $text = '', $query = array(), $trail = '', $prefix = '')
    {
        list($inside, $trail) = MediawikiLinker :: splitTrail($trail);
        $link = MediawikiLinker :: makeKnownLinkObj($nt, $text, $query, $inside, $prefix);
        return $this->armorLinks($link) . $trail;
    }

    /**
     * Insert a NOPARSE hacky thing into any inline links in a chunk that's
     * going to go through further parsing steps before inline URL expansion.
     * Not needed quite as much as it used to be since free links are a bit
     * more sensible these days. But bracketed links are still an issue.
     * 
     * @param string more-or-less HTML
     * @return string less-or-more HTML with NOPARSE bits
     */
    function armorLinks($text)
    {
        return preg_replace('/\b(' . wfUrlProtocols() . ')/', "{$this->mUniqPrefix}NOPARSE$1", $text);
    }

    function getOutput()
    {
        return $this->mOutput;
    }
}
?>