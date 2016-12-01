<?php
class MediawikiUtilities
{

    /**
     * Return UTF-8 sequence for a given Unicode code point.
     * May die if fed out of range data.
     * 
     * @param $codepoint Integer:
     * @return String @public
     */
    static function codepointToUtf8($codepoint)
    {
        if ($codepoint < 0x80)
            return chr($codepoint);
        if ($codepoint < 0x800)
            return chr($codepoint >> 6 & 0x3f | 0xc0) . chr($codepoint & 0x3f | 0x80);
        if ($codepoint < 0x10000)
            return chr($codepoint >> 12 & 0x0f | 0xe0) . chr($codepoint >> 6 & 0x3f | 0x80) .
                 chr($codepoint & 0x3f | 0x80);
        if ($codepoint < 0x110000)
            return chr($codepoint >> 18 & 0x07 | 0xf0) . chr($codepoint >> 12 & 0x3f | 0x80) .
                 chr($codepoint >> 6 & 0x3f | 0x80) . chr($codepoint & 0x3f | 0x80);
        
        echo "Asked for code outside of range ($codepoint)\n";
        die(- 1);
    }
}
?>