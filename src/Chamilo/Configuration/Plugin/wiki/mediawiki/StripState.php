<?php
/**
 *
 * @todo document, briefly.
 *       @ingroup Parser
 */
class MediawikiStripState
{

    var $general, $nowiki;

    function __construct()
    {
        $this->general = new ReplacementArray();
        $this->nowiki = new ReplacementArray();
    }

    function unstripGeneral($text)
    {
        do
        {
            $oldText = $text;
            $text = $this->general->replace($text);
        }
        while ($text !== $oldText);
        return $text;
    }

    function unstripNoWiki($text)
    {
        do
        {
            $oldText = $text;
            $text = $this->nowiki->replace($text);
        }
        while ($text !== $oldText);
        return $text;
    }

    function unstripBoth($text)
    {
        do
        {
            $oldText = $text;
            $text = $this->general->replace($text);
            $text = $this->nowiki->replace($text);
        }
        while ($text !== $oldText);
        return $text;
    }
}
?>