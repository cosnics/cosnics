<?php
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Platform\Translation;

/**
 * Split off some of the internal bits from Skin.php.
 * These functions are used for primarily page content: links,
 * embedded images, table of contents. Links are also used in the skin. For the moment, Skin is a descendent class of
 * Linker. In the future, it should probably be further split so that every other bit of the wiki doesn't have to go
 * loading up Skin to get at it. @ingroup Skins
 */
class MediawikiLinker
{
    
    /**
     * Flags for userToolLinks()
     */
    const TOOL_LINKS_NOBLOCK = 1;

    function __construct()
    {
    }

    /**
     *
     * @deprecated
     *
     *
     *
     */
    function postParseLinkColour($s = null)
    {
        wfDeprecated(__METHOD__);
        return null;
    }

    /**
     * Get the appropriate HTML attributes to add to the "a" element of an ex- ternal link, as created by [wikisyntax].
     * 
     * @param $title string The (unescaped) title text for the link
     * @param $unused string Unused
     * @param $class string The contents of the class attribute; if an empty string is passed, which is the default
     *        value, defaults to 'external'.
     */
    function getExternalLinkAttributes($title, $unused = null, $class = '')
    {
        return self :: getLinkAttributesInternal($title, $class, 'external');
    }

    /**
     * Get the appropriate HTML attributes to add to the "a" element of an in- terwiki link.
     * 
     * @param $title string The title text for the link, URL-encoded (???) but not HTML-escaped
     * @param $unused string Unused
     * @param $class string The contents of the class attribute; if an empty string is passed, which is the default
     *        value, defaults to 'external'.
     */
    function getInterwikiLinkAttributes($title, $unused = null, $class = '')
    {
        global $wgContLang;
        
        // FIXME: We have a whole bunch of handling here that doesn't happen in
        // getExternalLinkAttributes, why?
        $title = urldecode($title);
        $title = $wgContLang->checkTitleEncoding($title);
        $title = preg_replace('/[\\x00-\\x1f]/', ' ', $title);
        
        return self :: getLinkAttributesInternal($title, $class, 'external');
    }

    /**
     * Get the appropriate HTML attributes to add to the "a" element of an in- ternal link.
     * 
     * @param $title string The title text for the link, URL-encoded (???) but not HTML-escaped
     * @param $unused string Unused
     * @param $class string The contents of the class attribute, default none
     */
    function getInternalLinkAttributes($title, $unused = null, $class = '')
    {
        $title = urldecode($title);
        $title = str_replace('_', ' ', $title);
        return self :: getLinkAttributesInternal($title, $class);
    }

    /**
     * Get the appropriate HTML attributes to add to the "a" element of an in- ternal link, given the Title object for
     * the page we want to link to.
     * 
     * @param $nt Title The Title object
     * @param $unused string Unused
     * @param $class string The contents of the class attribute, default none
     * @param $title mixed Optional (unescaped) string to use in the title attribute; if false, default to the name of
     *        the page we're linking to
     */
    function getInternalLinkAttributesObj($nt, $unused = null, $class = '', $title = false)
    {
        if ($title === false)
        {
            $title = $nt->getPrefixedText();
        }
        return self :: getLinkAttributesInternal($title, $class);
    }

    /**
     * Common code for getLinkAttributesX functions
     */
    private function getLinkAttributesInternal($title, $class, $classDefault = false)
    {
        $title = htmlspecialchars($title);
        if ($class === '' and $classDefault !== false)
        {
            // FIXME: Parameter defaults the hard way! We should just have
            // $class = 'external' or whatever as the default in the externally-
            // exposed functions, not $class = ''.
            $class = $classDefault;
        }
        $class = htmlspecialchars($class);
        $r = '';
        if ($class !== '')
        {
            $r .= " class=\"$class\"";
        }
        $r .= " title=\"$title\"";
        return $r;
    }

    /**
     * Return the CSS colour of a known link
     * 
     * @param $t Title
     * @param $threshold integer user defined threshold
     * @return string CSS class
     */
    function getLinkColour($t, $threshold)
    {
        $colour = '';
        if ($t->isRedirect())
        {
            // Page is a redirect
            $colour = 'mw-redirect';
        }
        elseif ($threshold > 0 && $t->exists() && $t->getLength() < $threshold &&
             MWNamespace :: isContent($t->getNamespace()))
        {
            // Page is a stub
            $colour = 'stub';
        }
        return $colour;
    }

    /**
     * This function returns an HTML link to the given target.
     * It serves a few purposes: 1) If $target is a Title, the
     * correct URL to link to will be figured out automatically. 2) It automatically adds the usual classes for various
     * types of link targets: "new" for red links, "stub" for short articles, etc. 3) It escapes all attribute values
     * safely so there's no risk of XSS. 4) It provides a default tooltip if the target is a Title (the page name of the
     * target). link() replaces the old functions in the makeLink() family.
     * 
     * @param $target Title Can currently only be a Title, but this may change to support Images, literal URLs, etc.
     * @param $text string The HTML contents of the <a> element, i.e., the link text. This is raw HTML and will not be
     *        escaped. If null, defaults to the prefixed text of the Title; or if the Title is just a fragment, the
     *        contents of the fragment.
     * @param $customAttribs array A key => value array of extra HTML attri- butes, such as title and class. (href is
     *        ignored.) Classes will be merged with the default classes, while other attributes will replace default
     *        attributes. All passed attribute values will be HTML-escaped. A false attribute value means to
     *        suppress that attribute.
     * @param $query array The query string to append to the URL you're linking to, in key => value array form. Query
     *        keys and values will be URL-encoded.
     * @param $options mixed String or array of strings: 'known': Page is known to exist, so don't check if it does.
     *        'broken': Page is known not to exist, so don't check if it does. 'noclasses': Don't add any classes
     *        automatically (includes "new", "stub", "mw-redirect", "extiw"). Only use the class attribute provided,
     *        if any, so you get a simple blue link with no funny i- cons. 'forcearticlepath': Use the article path
     *        always, even with a querystring. Has compatibility issues on some setups, so avoid wherever possible.
     * @return string HTML <a> attribute
     */
    public function link($target, $text = null, $customAttribs = array(), $query = array(), $options = array())
    {
        if (! $target instanceof MediawikiTitle)
        {
            return "<!-- ERROR -->$text";
        }
        $options = (array) $options;
        
        $ret = null;
        
        // If we don't know whether the page exists, let's find out.
        if (! in_array('known', $options) and ! in_array('broken', $options))
        {
            if ($target->isKnown())
            {
                $options[] = 'known';
            }
            else
            {
                $options[] = 'broken';
            }
        }
        
        $oldquery = array();
        if (in_array("forcearticlepath", $options) && $query)
        {
            $oldquery = $query;
            $query = array();
        }
        
        // Note: we want the href attribute first, for prettiness.
        $attribs = array('href' => self :: linkUrl($target, $query, $options));
        if (in_array('forcearticlepath', $options) && $oldquery)
        {
            $attribs['href'] = wfAppendQuery($attribs['href'], wfArrayToCgi($oldquery));
        }
        
        $attribs = array_merge($attribs, self :: linkAttribs($target, $customAttribs, $options));
        if (is_null($text))
        {
            $text = self :: linkText($target);
        }
        
        $ret = Xml :: openElement('a', $attribs) . $text . Xml :: closeElement('a');
        
        return $ret;
    }

    private function linkUrl($target, $query, $options)
    {
        // We don't want to include fragments for broken links, because they
        // generally make no sense.
        if (in_array('broken', $options) and $target->mFragment !== '')
        {
            $target = clone $target;
            $target->mFragment = '';
        }
        
        // If it's a broken link, add the appropriate query pieces, unless
        // there's already an action specified, or unless 'edit' makes no sense
        // (i.e., for a nonexistent special page).
        if (in_array('broken', $options) and empty($query['action']) and $target->getNamespace() != NS_SPECIAL)
        {
            $query[\Chamilo\Core\Repository\Display\Action\Manager :: PARAM_ACTION] = \Chamilo\Core\Repository\ContentObject\Wiki\Display\Manager :: ACTION_CREATE_PAGE;
            $query[ContentObject :: PROPERTY_TITLE] = $target->getText();
            
            // $query['redlink'] = '1';
        }
        $ret = $target->getLinkUrl($query);
        return $ret;
    }

    private function linkAttribs($target, $attribs, $options)
    {
        $defaults = array();
        
        if (! in_array('noclasses', $options))
        {
            // Now build the classes.
            $classes = array();
            
            if (in_array('broken', $options))
            {
                $classes[] = 'new';
            }
            
            if ($target->isExternal())
            {
                $classes[] = 'extiw';
            }
            
            // Note that redirects never count as stubs here.
            if ($target->isRedirect())
            {
                $classes[] = 'mw-redirect';
            }
            elseif ($target->isContentPage())
            {
                // Check for stub.
                $threshold = 0;
                if ($threshold > 0 and $target->exists() and $target->getLength() < $threshold)
                {
                    $classes[] = 'stub';
                }
            }
            if ($classes != array())
            {
                $defaults['class'] = implode(' ', $classes);
            }
        }
        
        // Get a default title attribute.
        if (in_array('known', $options))
        {
            $defaults['title'] = $target->getPrefixedText();
        }
        else
        {
            $defaults['title'] = $target->getPrefixedText();
        }
        
        // Finally, merge the custom attribs with the default ones, and iterate
        // over that, deleting all "false" attributes.
        $ret = array();
        $merged = MediawikiSanitizer :: mergeAttributes($defaults, $attribs);
        foreach ($merged as $key => $val)
        {
            // A false value suppresses the attribute, and we don't want the
            // href attribute to be overridden.
            if ($key != 'href' and $val !== false)
            {
                $ret[$key] = $val;
            }
        }
        return $ret;
    }

    private function linkText($target)
    {
        // We might be passed a non-Title by make*LinkObj(). Fail gracefully.
        if (! $target instanceof MediawikiTitle)
        {
            return '';
        }
        
        // If the target is just a fragment, with no title, we return the frag-
        // ment text. Otherwise, we return the title text itself.
        if ($target->getPrefixedText() === '' and $target->getFragment() !== '')
        {
            return htmlspecialchars($target->getFragment());
        }
        return htmlspecialchars($target->getPrefixedText());
    }

    /**
     *
     * @deprecated Use link() This function is a shortcut to makeLinkObj(Title::newFromText($title),...). Do not call it
     *             if you already have a title object handy. See makeLinkObj for further documentation.
     * @param $title String: the text of the title
     * @param $text String: link text
     * @param $query String: optional query part
     * @param $trail String: optional trail. Alphabetic characters at the start of this string will be included in the
     *        link text. Other characters will be appended after the end of the link.
     */
    function makeLink($title, $text = '', $query = '', $trail = '')
    {
        $nt = MediawikiTitle :: newFromText($title);
        if ($nt instanceof MediawikiTitle)
        {
            $result = self :: makeLinkObj($nt, $text, $query, $trail);
        }
        else
        {
            $result = $text == "" ? $title : $text;
        }
        
        return $result;
    }

    /**
     *
     * @deprecated Use link() This function is a shortcut to makeKnownLinkObj(Title::newFromText($title),...). Do not
     *             call it if you already have a title object handy. See makeKnownLinkObj for further documentation.
     * @param $title String: the text of the title
     * @param $text String: link text
     * @param $query String: optional query part
     * @param $trail String: optional trail. Alphabetic characters at the start of this string will be included in the
     *        link text. Other characters will be appended after the end of the link.
     */
    function makeKnownLink($title, $text = '', $query = '', $trail = '', $prefix = '', $aprops = '')
    {
        $nt = MediawikiTitle :: newFromText($title);
        if ($nt instanceof MediawikiTitle)
        {
            return self :: makeKnownLinkObj($nt, $text, $query, $trail, $prefix, $aprops);
        }
        else
        {
            return $text == '' ? $title : $text;
        }
    }

    /**
     *
     * @deprecated Use link() This function is a shortcut to makeBrokenLinkObj(Title::newFromText($title),...). Do not
     *             call it if you already have a title object handy. See makeBrokenLinkObj for further documentation.
     * @param $title string The text of the title
     * @param $text string Link text
     * @param $query string Optional query part
     * @param $trail string Optional trail. Alphabetic characters at the start of this string will be included in the
     *        link text. Other characters will be appended after the end of the link.
     */
    function makeBrokenLink($title, $text = '', $query = '', $trail = '')
    {
        $nt = MediawikiTitle :: newFromText($title);
        if ($nt instanceof MediawikiTitle)
        {
            return self :: makeBrokenLinkObj($nt, $text, $query, $trail);
        }
        else
        {
            return $text == '' ? $title : $text;
        }
    }

    /**
     *
     * @deprecated Use link() This function is a shortcut to makeStubLinkObj(Title::newFromText($title),...). Do not
     *             call it if you already have a title object handy. See makeStubLinkObj for further documentation.
     * @param $title String: the text of the title
     * @param $text String: link text
     * @param $query String: optional query part
     * @param $trail String: optional trail. Alphabetic characters at the start of this string will be included in the
     *        link text. Other characters will be appended after the end of the link.
     */
    function makeStubLink($title, $text = '', $query = '', $trail = '')
    {
        $nt = MediawikiTitle :: newFromText($title);
        if ($nt instanceof MediawikiTitle)
        {
            return self :: makeStubLinkObj($nt, $text, $query, $trail);
        }
        else
        {
            return $text == '' ? $title : $text;
        }
    }

    /**
     *
     * @deprecated Use link() Make a link for a title which may or may not be in the database. If you need to call this
     *             lots of times, pre-fill the link cache with a LinkBatch, otherwise each call to this will result in a
     *             DB query.
     * @param $nt Title: the title object to make the link from, e.g. from Title::newFromText.
     * @param $text String: link text
     * @param $query String: optional query part
     * @param $trail String: optional trail. Alphabetic characters at the start of this string will be included in the
     *        link text. Other characters will be appended after the end of the link.
     * @param $prefix String: optional prefix. As trail, only before instead of after.
     */
    function makeLinkObj($nt, $text = '', $query = '', $trail = '', $prefix = '')
    {
        $query = wfCgiToArray($query);
        list($inside, $trail) = MediawikiLinker :: splitTrail($trail);
        if ($text === '')
        {
            $text = self :: linkText($nt);
        }
        
        $ret = self :: link($nt, "$prefix$text$inside", array(), $query) . $trail;
        
        return $ret;
    }

    /**
     *
     * @deprecated Use link() Make a link for a title which definitely exists. This is faster than makeLinkObj because
     *             it doesn't have to do a database query. It's also valid for interwiki titles and special pages.
     * @param $nt Title object of target page
     * @param $text String: text to replace the title
     * @param $query String: link target
     * @param $trail String: text after link
     * @param $prefix String: text before link text
     * @param $aprops String: extra attributes to the a-element
     * @param $style String: style to apply - if empty, use getInternalLinkAttributesObj instead
     * @return the a-element
     */
    function makeKnownLinkObj($title, $text = '', $query = array(), $trail = '', $prefix = '', $aprops = '', $style = '')
    {
        
        // dump($query);
        if ($text == '')
        {
            $text = self :: linkText($title);
        }
        $attribs = MediawikiSanitizer :: mergeAttributes(
            MediawikiSanitizer :: decodeTagAttributes($aprops), 
            MediawikiSanitizer :: decodeTagAttributes($style));
        // $query = wfCgiToArray($query);
        list($inside, $trail) = MediawikiLinker :: splitTrail($trail);
        
        $ret = self :: link($title, "$prefix$text$inside", $attribs, $query, array('known', 'noclasses')) . $trail;
        
        return $ret;
    }

    /**
     *
     * @deprecated Use link() Make a red link to the edit page of a given title.
     * @param $nt Title object of the target page
     * @param $text String: Link text
     * @param $query String: Optional query part
     * @param $trail String: Optional trail. Alphabetic characters at the start of this string will be included in the
     *        link text. Other characters will be appended after the end of the link.
     */
    function makeBrokenLinkObj($title, $text = '', $query = array(), $trail = '', $prefix = '')
    {
        list($inside, $trail) = self :: splitTrail($trail);
        if ($text === '')
        {
            $text = self :: linkText($title);
        }
        
        // $ret = self :: link($title, "$prefix$text$inside", array(),
        // wfCgiToArray($query), 'broken') . $trail;
        $ret = self :: link($title, "$prefix$text$inside", array(), $query, 'broken') . $trail;
        
        return $ret;
    }

    /**
     *
     * @deprecated Use link() Make a brown link to a short article.
     * @param $nt Title object of the target page
     * @param $text String: link text
     * @param $query String: optional query part
     * @param $trail String: optional trail. Alphabetic characters at the start of this string will be included in the
     *        link text. Other characters will be appended after the end of the link.
     */
    function makeStubLinkObj($nt, $text = '', $query = '', $trail = '', $prefix = '')
    {
        return self :: makeColouredLinkObj($nt, 'stub', $text, $query, $trail, $prefix);
    }

    /**
     *
     * @deprecated Use link() Make a coloured link.
     * @param $nt Title object of the target page
     * @param $colour Integer: colour of the link
     * @param $text String: link text
     * @param $query String: optional query part
     * @param $trail String: optional trail. Alphabetic characters at the start of this string will be included in the
     *        link text. Other characters will be appended after the end of the link.
     */
    function makeColouredLinkObj($nt, $colour, $text = '', $query = array(), $trail = '', $prefix = '')
    {
        if ($colour != '')
        {
            $style = self :: getInternalLinkAttributesObj($nt, $text, $colour);
        }
        else
            $style = '';
        return self :: makeKnownLinkObj($nt, $text, $query, $trail, $prefix, '', $style);
    }

    /**
     * Generate either a normal exists-style link or a stub link, depending on the given page size.
     * 
     * @param $size Integer
     * @param $nt Title object.
     * @param $text String
     * @param $query String
     * @param $trail String
     * @param $prefix String
     * @return string HTML of link
     */
    function makeSizeLinkObj($size, $nt, $text = '', $query = '', $trail = '', $prefix = '')
    {
        $threshold = 0;
        $colour = ($size < $threshold) ? 'stub' : '';
        return self :: makeColouredLinkObj($nt, $colour, $text, $query, $trail, $prefix);
    }

    /**
     * Make appropriate markup for a link to the current article.
     * This is currently rendered as the bold link text. The
     * calling sequence is the same as the other make*LinkObj functions, despite $query not being used.
     */
    function makeSelfLinkObj($nt, $text = '', $query = '', $trail = '', $prefix = '')
    {
        if ('' == $text)
        {
            $text = htmlspecialchars($nt->getPrefixedText());
        }
        list($inside, $trail) = Linker :: splitTrail($trail);
        return "<strong class=\"selflink\">{$prefix}{$text}{$inside}</strong>{$trail}";
    }

    function normaliseSpecialPage(Title $title)
    {
        if ($title->getNamespace() == NS_SPECIAL)
        {
            list($name, $subpage) = SpecialPage :: resolveAliasWithSubpage($title->getDBkey());
            if (! $name)
                return $title;
            $ret = SpecialPage :: getTitleFor($name, $subpage);
            $ret->mFragment = $title->getFragment();
            return $ret;
        }
        else
        {
            return $title;
        }
    }

    /**
     *
     * @todo document
     */
    function fnamePart($url)
    {
        $basename = strrchr($url, '/');
        if (false === $basename)
        {
            $basename = $url;
        }
        else
        {
            $basename = substr($basename, 1);
        }
        return $basename;
    }

    /**
     * Obsolete alias
     */
    function makeImage($url, $alt = '')
    {
        return self :: makeExternalImage($url, $alt);
    }

    /**
     *
     * @todo document
     */
    function makeExternalImage($url, $alt = '')
    {
        if ('' == $alt)
        {
            $alt = self :: fnamePart($url);
        }
        
        return Xml :: element('img', array('src' => $url, 'alt' => $alt));
    }

    /**
     * Creates the HTML source for images
     * 
     * @deprecated use makeImageLink2
     * @param $title object
     * @param $label string label text
     * @param $alt string alt text
     * @param $align string horizontal alignment: none, left, center, right)
     * @param $handlerParams array Parameters to be passed to the media handler
     * @param $framed boolean shows image in original size in a frame
     * @param $thumb boolean shows image as thumbnail in a frame
     * @param $manualthumb string image name for the manual thumbnail
     * @param $valign string vertical alignment: baseline, sub, super, top, text-top, middle, bottom, text-bottom
     * @param $time, string timestamp of the file, set as false for current
     * @return string
     */
    function makeImageLinkObj($title, $label, $alt, $align = '', $handlerParams = array(), $framed = false, $thumb = false, 
        $manualthumb = '', $valign = '', $time = false)
    {
        $frameParams = array('alt' => $alt, 'caption' => $label);
        if ($align)
        {
            $frameParams['align'] = $align;
        }
        if ($framed)
        {
            $frameParams['framed'] = true;
        }
        if ($thumb)
        {
            $frameParams['thumbnail'] = true;
        }
        if ($manualthumb)
        {
            $frameParams['manualthumb'] = $manualthumb;
        }
        if ($valign)
        {
            $frameParams['valign'] = $valign;
        }
        $file = wfFindFile($title, $time);
        return $this->makeImageLink2($title, $file, $frameParams, $handlerParams, $time);
    }

    /**
     * Given parameters derived from [[Image:Foo|options...]], generate the HTML that that syntax inserts in the page.
     * 
     * @param $title Title Title object
     * @param $file File File object, or false if it doesn't exist
     * @param $frameParams array Associative array of parameters external to the media handler. Boolean parameters are
     *        indicated by presence or absence, the value is arbitrary and will often be false. thumbnail If
     *        present, downscale and frame manualthumb Image name to use as a thumbnail, instead of automatic
     *        scaling framed Shows image in original size in a frame frameless Downscale but don't frame upright If
     *        present, tweak default sizes for portrait orientation upright_factor Fudge factor for "upright" tweak
     *        (default 0.75) border If present, show a border around the image align Horizontal alignment (left,
     *        right, center, none) valign Vertical alignment (baseline, sub, super, top, text-top, middle, bottom,
     *        text-bottom) alt Alternate text for image (i.e. alt attribute). Plain text. caption HTML for image
     *        caption. link-url URL to link to link-title Title object to link to no-link Boolean, suppress
     *        description link
     * @param $handlerParams array Associative array of media handler parameters, to be passed to transform(). Typical
     *        keys are "width" and "page".
     * @param $time, string timestamp of the file, set as false for current
     * @param $query, string query params for desc url
     * @return string HTML for an image, with links, wrappers, etc.
     */
    function makeImageLink2(Title $title, $file, $frameParams = array(), $handlerParams = array(), $time = false, $query = "")
    {
        $res = null;
        if (! wfRunHooks(
            'ImageBeforeProduceHTML', 
            array(&$this, &$title, &$file, &$frameParams, &$handlerParams, &$time, &$res)))
        {
            return $res;
        }
        
        global $wgContLang, $wgUser, $wgThumbLimits, $wgThumbUpright;
        if ($file && ! $file->allowInlineDisplay())
        {
            wfDebug(__METHOD__ . ': ' . $title->getPrefixedDBkey() . " does not allow inline display\n");
            return $this->link($title);
        }
        
        // Shortcuts
        $fp = & $frameParams;
        $hp = & $handlerParams;
        
        // Clean up parameters
        $page = isset($hp['page']) ? $hp['page'] : false;
        if (! isset($fp['align']))
            $fp['align'] = '';
        if (! isset($fp['alt']))
            $fp['alt'] = '';
            
            // Backward compatibility, title used to always be equal to alt text
        if (! isset($fp['title']))
            $fp['title'] = $fp['alt'];
        
        $prefix = $postfix = '';
        
        if ('center' == $fp['align'])
        {
            $prefix = '<div class="center">';
            $postfix = '</div>';
            $fp['align'] = 'none';
        }
        if ($file && ! isset($hp['width']))
        {
            $hp['width'] = $file->getWidth($page);
            
            if (isset($fp['thumbnail']) || isset($fp['framed']) || isset($fp['frameless']) || ! $hp['width'])
            {
                $wopt = $wgUser->getOption('thumbsize');
                
                if (! isset($wgThumbLimits[$wopt]))
                {
                    $wopt = User :: getDefaultOption('thumbsize');
                }
                
                // Reduce width for upright images when parameter 'upright' is
                // used
                if (isset($fp['upright']) && $fp['upright'] == 0)
                {
                    $fp['upright'] = $wgThumbUpright;
                }
                // Use width which is smaller: real image width or user
                // preference width
                // For caching health: If width scaled down due to upright
                // parameter, round to full __0 pixel to avoid the creation of a
                // lot of odd thumbs
                $prefWidth = isset($fp['upright']) ? round($wgThumbLimits[$wopt] * $fp['upright'], - 1) : $wgThumbLimits[$wopt];
                if ($hp['width'] <= 0 || $prefWidth < $hp['width'])
                {
                    $hp['width'] = $prefWidth;
                }
            }
        }
        
        if (isset($fp['thumbnail']) || isset($fp['manualthumb']) || isset($fp['framed']))
        {
            // Create a thumbnail. Alignment depends on language
            // writing direction, # right aligned for left-to-right-
            // languages ("Western languages"), left-aligned
            // for right-to-left-languages ("Semitic languages")
            //
            // If thumbnail width has not been provided, it is set
            // to the default user option as specified in Language*.php
            if ($fp['align'] == '')
            {
                $fp['align'] = $wgContLang->isRTL() ? 'left' : 'right';
            }
            return $prefix . $this->makeThumbLink2($title, $file, $fp, $hp, $time, $query) . $postfix;
        }
        
        if ($file && isset($fp['frameless']))
        {
            $srcWidth = $file->getWidth($page);
            // For "frameless" option: do not present an image bigger than the
            // source (for bitmap-style images)
            // This is the same behaviour as the "thumb" option does it already.
            if ($srcWidth && ! $file->mustRender() && $hp['width'] > $srcWidth)
            {
                $hp['width'] = $srcWidth;
            }
        }
        
        if ($file && $hp['width'])
        {
            // Create a resized image, without the additional thumbnail features
            $thumb = $file->transform($hp);
        }
        else
        {
            $thumb = false;
        }
        
        if (! $thumb)
        {
            $s = $this->makeBrokenImageLinkObj($title, '', '', '', '', $time == true);
        }
        else
        {
            $params = array(
                'alt' => $fp['alt'], 
                'title' => $fp['title'], 
                'valign' => isset($fp['valign']) ? $fp['valign'] : false, 
                'img-class' => isset($fp['border']) ? 'thumbborder' : false);
            if (! empty($fp['link-url']))
            {
                $params['custom-url-link'] = $fp['link-url'];
            }
            elseif (! empty($fp['link-title']))
            {
                $params['custom-title-link'] = $fp['link-title'];
            }
            elseif (! empty($fp['no-link']))
            {
                // No link
            }
            else
            {
                $params['desc-link'] = true;
                $params['desc-query'] = $query;
            }
            
            $s = $thumb->toHtml($params);
        }
        if ('' != $fp['align'])
        {
            $s = "<div class=\"float{$fp['align']}\">{$s}</div>";
        }
        return str_replace("\n", ' ', $prefix . $s . $postfix);
    }

    /**
     * Make HTML for a thumbnail including image, border and caption
     * 
     * @param $title Title
     * @param $file File File object or false if it doesn't exist
     */
    function makeThumbLinkObj(Title $title, $file, $label = '', $alt, $align = 'right', $params = array(), $framed = false, $manualthumb = "")
    {
        $frameParams = array('alt' => $alt, 'caption' => $label, 'align' => $align);
        if ($framed)
            $frameParams['framed'] = true;
        if ($manualthumb)
            $frameParams['manualthumb'] = $manualthumb;
        return $this->makeThumbLink2($title, $file, $frameParams, $params);
    }

    function makeThumbLink2(Title $title, $file, $frameParams = array(), $handlerParams = array(), $time = false, $query = "")
    {
        global $wgStylePath, $wgContLang;
        $exists = $file && $file->exists();
        
        // Shortcuts
        $fp = & $frameParams;
        $hp = & $handlerParams;
        
        $page = isset($hp['page']) ? $hp['page'] : false;
        if (! isset($fp['align']))
            $fp['align'] = 'right';
        if (! isset($fp['alt']))
            $fp['alt'] = '';
            
            // Backward compatibility, title used to always be equal to alt text
        if (! isset($fp['title']))
            $fp['title'] = $fp['alt'];
        if (! isset($fp['caption']))
            $fp['caption'] = '';
        
        if (empty($hp['width']))
        {
            // Reduce width for upright images when parameter 'upright' is used
            $hp['width'] = isset($fp['upright']) ? 130 : 180;
        }
        $thumb = false;
        
        if (! $exists)
        {
            $outerWidth = $hp['width'] + 2;
        }
        else
        {
            if (isset($fp['manualthumb']))
            {
                // Use manually specified thumbnail
                $manual_title = Title :: makeTitleSafe(NS_FILE, $fp['manualthumb']);
                if ($manual_title)
                {
                    $manual_img = wfFindFile($manual_title);
                    if ($manual_img)
                    {
                        $thumb = $manual_img->getUnscaledThumb();
                    }
                    else
                    {
                        $exists = false;
                    }
                }
            }
            elseif (isset($fp['framed']))
            {
                // Use image dimensions, don't scale
                $thumb = $file->getUnscaledThumb($page);
            }
            else
            {
                // Do not present an image bigger than the source, for
                // bitmap-style images
                // This is a hack to maintain compatibility with arbitrary
                // pre-1.10 behaviour
                $srcWidth = $file->getWidth($page);
                if ($srcWidth && ! $file->mustRender() && $hp['width'] > $srcWidth)
                {
                    $hp['width'] = $srcWidth;
                }
                $thumb = $file->transform($hp);
            }
            
            if ($thumb)
            {
                $outerWidth = $thumb->getWidth() + 2;
            }
            else
            {
                $outerWidth = $hp['width'] + 2;
            }
        }
        
        // ThumbnailImage::toHtml() already adds page= onto the end of DjVu URLs
        // So we don't need to pass it here in $query. However, the URL for the
        // zoom icon still needs it, so we make a unique query for it. See bug
        // 14771
        $url = $title->getLocalURL($query);
        if ($page)
        {
            $url = wfAppendQuery($url, 'page=' . urlencode($page));
        }
        
        $more = htmlspecialchars(wfMsg('thumbnail-more'));
        
        $s = "<div class=\"thumb t{$fp['align']}\"><div class=\"thumbinner\" style=\"width:{$outerWidth}px;\">";
        if (! $exists)
        {
            $s .= $this->makeBrokenImageLinkObj($title, '', '', '', '', $time == true);
            $zoomicon = '';
        }
        elseif (! $thumb)
        {
            $s .= htmlspecialchars(wfMsg('thumbnail_error', ''));
            $zoomicon = '';
        }
        else
        {
            $s .= $thumb->toHtml(
                array(
                    'alt' => $fp['alt'], 
                    'title' => $fp['title'], 
                    'img-class' => 'thumbimage', 
                    'desc-link' => true, 
                    'desc-query' => $query));
            if (isset($fp['framed']))
            {
                $zoomicon = "";
            }
            else
            {
                $zoomicon = '<div class="magnify">' . '<a href="' . $url . '" class="internal" title="' . $more . '">' .
                     '<img src="' . $wgStylePath . '/common/images/magnify-clip.png" ' .
                     'width="15" height="11" alt="" /></a></div>';
            }
        }
        $s .= '  <div class="thumbcaption">' . $zoomicon . $fp['caption'] . "</div></div></div>";
        return str_replace("\n", ' ', $s);
    }

    /**
     * Make a "broken" link to an image
     * 
     * @param $title Title Image title
     * @param $text string Link label
     * @param $query string Query string
     * @param $trail string Link trail
     * @param $prefix string Link prefix
     * @param $time, bool a file of a certain timestamp was requested
     * @return string
     */
    public function makeBrokenImageLinkObj($title, $text = '', $query = '', $trail = '', $prefix = '', $time = false)
    {
        global $wgEnableUploads;
        if ($title instanceof Title)
        {
            wfProfileIn(__METHOD__);
            $currentExists = $time ? (wfFindFile($title) != false) : false;
            if ($wgEnableUploads && ! $currentExists)
            {
                $upload = SpecialPage :: getTitleFor('Upload');
                if ($text == '')
                    $text = htmlspecialchars($title->getPrefixedText());
                $redir = RepoGroup :: singleton()->getLocalRepo()->checkRedirect($title);
                if ($redir)
                {
                    return $this->makeKnownLinkObj($title, $text, $query, $trail, $prefix);
                }
                $q = 'wpDestFile=' . $title->getPartialUrl();
                if ($query != '')
                    $q .= '&' . $query;
                list($inside, $trail) = self :: splitTrail($trail);
                $style = $this->getInternalLinkAttributesObj($title, $text, 'new');
                wfProfileOut(__METHOD__);
                return '<a href="' . $upload->escapeLocalUrl($q) . '"' . $style . '>' . $prefix . $text . $inside .
                     '</a>' . $trail;
            }
            else
            {
                wfProfileOut(__METHOD__);
                return $this->makeKnownLinkObj($title, $text, $query, $trail, $prefix);
            }
        }
        else
        {
            return "<!-- ERROR -->{$prefix}{$text}{$trail}";
        }
    }

    /**
     *
     * @deprecated use Linker::makeMediaLinkObj()
     */
    function makeMediaLink($name, $unused = '', $text = '', $time = false)
    {
        $nt = Title :: makeTitleSafe(NS_FILE, $name);
        return $this->makeMediaLinkObj($nt, $text, $time);
    }

    /**
     * Create a direct link to a given uploaded file.
     * 
     * @param $title Title object.
     * @param $text String: pre-sanitized HTML
     * @param $time string: time image was created
     * @return string HTML @public
     * @todo Handle invalid or missing images better.
     */
    function makeMediaLinkObj($title, $text = '', $time = false)
    {
        if (is_null($title))
        {
            // # HOTFIX. Instead of breaking, return empty string.
            return $text;
        }
        else
        {
            $img = wfFindFile($title, $time);
            if ($img)
            {
                $url = $img->getURL();
                $class = 'internal';
            }
            else
            {
                $upload = SpecialPage :: getTitleFor('Upload');
                $url = $upload->getLocalUrl('wpDestFile=' . urlencode($title->getDBkey()));
                $class = 'new';
            }
            $alt = htmlspecialchars($title->getText());
            if ($text == '')
            {
                $text = $alt;
            }
            $u = htmlspecialchars($url);
            return "<a href=\"{$u}\" class=\"$class\" title=\"{$alt}\">{$text}</a>";
        }
    }

    /**
     *
     * @todo document
     */
    function specialLink($name, $key = '')
    {
        global $wgContLang;
        
        if ('' == $key)
        {
            $key = strtolower($name);
        }
        $pn = $wgContLang->ucfirst($name);
        return self :: makeKnownLink($wgContLang->specialPage($pn), wfMsg($key));
    }

    /**
     * Make an external link
     * 
     * @param $url String URL to link to
     * @param $text String text of link
     * @param $escape boolean Do we escape the link text?
     * @param $linktype String Type of external link. Gets added to the classes
     * @param $attribs array Array of extra attributes to <a>
     * @todo ! @FIXME! This is a really crappy implementation. $linktype and 'external' are mashed into the class attrib
     *       for the link (which is made into a string). Then, if we've got additional params in $attribs, we add to it.
     *       People using this might want to change the classes (or other default link attributes), but passing
     *       $attribsText is just messy. Would make a lot more sense to make put the classes into $attribs, let the hook
     *       play with them, *then* expand it all at once.
     */
    function makeExternalLink($url, $text, $escape = true, $linktype = '', $attribs = array())
    {
        $attribsText = self :: getExternalLinkAttributes($url, $text, 'external ' . $linktype);
        $url = htmlspecialchars($url);
        if ($escape)
        {
            $text = htmlspecialchars($text);
        }
        
        if ($attribs)
        {
            $attribsText .= Xml :: expandAttributes($attribs);
        }
        return '<a href="' . $url . '"' . $attribsText . '>' . $text . '</a>';
    }

    /**
     *
     * @todo document
     */
    function tocIndent()
    {
        return "\n<ul>";
    }

    /**
     *
     * @todo document
     */
    function tocUnindent($level)
    {
        return "</li>\n" . str_repeat("</ul>\n</li>\n", $level > 0 ? $level : 0);
    }

    /**
     * parameter level defines if we are on an indentation level
     */
    function tocLine($anchor, $tocline, $tocnumber, $level)
    {
        return "\n<li class=\"toclevel-$level\"><a href=\"#" . $anchor . '"><span class="tocnumber">' . $tocnumber .
             '</span> <span class="toctext">' . $tocline . '</span></a>';
    }

    /**
     *
     * @todo document
     */
    function tocLineEnd()
    {
        return "</li>\n";
    }

    /**
     *
     * @todo document
     */
    function tocList($toc)
    {
        // global $wgJsMimeType;
        // $title = wfMsgHtml('toc');
        $title = Translation :: get('Contents');
        return '<table id="toc" class="toc" summary="' . $title . '"><tr><td>' . '<div id="toctitle"><h2>' . $title .
             "</h2></div>\n" . $toc . "</ul>\n</td></tr></table>";
        
        // '<script type="' . $wgJsMimeType . '">' . ' if (window.showTocToggle)
        // {' . ' var tocShowText = "' . Xml :: escapeJsString(wfMsg('showtoc')) .
        // '";' . ' var tocHideText = "' . Xml :: escapeJsString(wfMsg('hidetoc')) .
        // '";' . ' showTocToggle();' . ' } ' . "</script>\n";
    }

    /**
     * Create a headline for content
     * 
     * @param $level int The level of the headline (1-6)
     * @param $attribs string Any attributes for the headline, starting with a space and ending with '>' This *must* be
     *        at least '>' for no attribs
     * @param $anchor string The anchor to give the headline (the bit after the #)
     * @param $text string The text of the header
     * @param $link string HTML to add for the section edit link
     * @param $legacyAnchor mixed A second, optional anchor to give for backward compatibility (false to omit)
     * @return string HTML headline
     */
    public function makeHeadline($level, $attribs, $anchor, $text, $link, $legacyAnchor = false)
    {
        $ret = "<a name=\"$anchor\" id=\"$anchor\"></a>" . "<h$level$attribs" . $link .
             " <span class=\"mw-headline\">$text</span>" . "</h$level>";
        if ($legacyAnchor !== false)
        {
            $ret = "<a name=\"$legacyAnchor\" id=\"$legacyAnchor\"></a>$ret";
        }
        return $ret;
    }

    /**
     * Split a link trail, return the "inside" portion and the remainder of the trail as a two-element array
     * 
     * @static
     *
     *
     *
     */
    static function splitTrail($trail)
    {
        static $regex = false;
        if ($regex === false)
        {
            // CHAMILO | Use the default english regex for now
            // "Translations" should be provided for all languages
            // $regex = Translation :: get('MediaWikiLinkTrail');
            $regex = '/^([a-z]+)(.*)$/sD';
        }
        $inside = '';
        if ('' != $trail)
        {
            $m = array();
            if (preg_match($regex, $trail, $m))
            {
                $inside = $m[1];
                $trail = $m[2];
            }
        }
        return array($inside, $trail);
    }

    /**
     *
     * @deprecated Returns raw bits of HTML, use titleAttrib() and accesskey()
     */
    public function tooltipAndAccesskey($name)
    {
        // FIXME: If Sanitizer::expandAttributes() treated "false" as "output
        // no attribute" instead of "output '' as value for attribute", this
        // would be three lines.
        $attribs = array('title' => $this->titleAttrib($name, 'withaccess'), 'accesskey' => $this->accesskey($name));
        if ($attribs['title'] === false)
        {
            unset($attribs['title']);
        }
        if ($attribs['accesskey'] === false)
        {
            unset($attribs['accesskey']);
        }
        return Xml :: expandAttributes($attribs);
    }

    /**
     *
     * @deprecated Returns raw bits of HTML, use titleAttrib()
     */
    public function tooltip($name, $options = null)
    {
        // FIXME: If Sanitizer::expandAttributes() treated "false" as "output
        // no attribute" instead of "output '' as value for attribute", this
        // would be two lines.
        $tooltip = $this->titleAttrib($name, $options);
        if ($tooltip === false)
        {
            return '';
        }
        return Xml :: expandAttributes(array('title' => $this->titleAttrib($name, $options)));
    }

    /**
     * Given the id of an interface element, constructs the appropriate title attribute from the system messages.
     * (Note,
     * this is usually the id but isn't always, because sometimes the accesskey needs to go on a different element than
     * the id, for reverse-compatibility, etc.)
     * 
     * @param $name string Id of the element, minus prefixes.
     * @param $options mixed null or the string 'withaccess' to add an access- key hint
     * @return string Contents of the title attribute (which you must HTML- escape), or false for no title attribute
     */
    public function titleAttrib($name, $options = null)
    {
        wfProfileIn(__METHOD__);
        
        $tooltip = wfMsg("tooltip-$name");
        // Compatibility: formerly some tooltips had [alt-.] hardcoded
        $tooltip = preg_replace("/ ?\[alt-.\]$/", '', $tooltip);
        
        // Message equal to '-' means suppress it.
        if (wfEmptyMsg("tooltip-$name", $tooltip) || $tooltip == '-')
        {
            $tooltip = false;
        }
        
        if ($options == 'withaccess')
        {
            $accesskey = $this->accesskey($name);
            if ($accesskey !== false)
            {
                if ($tooltip === false || $tooltip === '')
                {
                    $tooltip = "[$accesskey]";
                }
                else
                {
                    $tooltip .= " [$accesskey]";
                }
            }
        }
        
        wfProfileOut(__METHOD__);
        return $tooltip;
    }

    /**
     * Given the id of an interface element, constructs the appropriate accesskey attribute from the system messages.
     * (Note, this is usually the id but isn't always, because sometimes the accesskey needs to go on a different
     * element than the id, for reverse-compatibility, etc.)
     * 
     * @param $name string Id of the element, minus prefixes.
     * @return string Contents of the accesskey attribute (which you must HTML- escape), or false for no accesskey
     *         attribute
     */
    public function accesskey($name)
    {
        wfProfileIn(__METHOD__);
        
        $accesskey = wfMsg("accesskey-$name");
        
        // FIXME: Per standard MW behavior, a value of '-' means to suppress the
        // attribute, but this is broken for accesskey: that might be a useful
        // value.
        if ($accesskey != '' && $accesskey != '-' && ! wfEmptyMsg("accesskey-$name", $accesskey))
        {
            wfProfileOut(__METHOD__);
            return $accesskey;
        }
        
        wfProfileOut(__METHOD__);
        return false;
    }
}
