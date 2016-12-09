<?php
namespace Chamilo\Configuration\Test\Archive;

/*
 * @author: Parcifal Aertssen (Howest)
 */
class SecurityTest extends \PHPUnit_Framework_TestCase
{

    public function test_xss()
    {
        $html = <<<EOT
                <script>alert('script injection!');</script>
                <ScRiPt></sCrIpT>
                <a href="javascript:alert('injection!')" onload="alert('onload');">but still are able to talk about "javascript:"</a>.
                Same goes for text like &lt;script&gt; this should not be filtered.
                Also prevent nested hacks like this <<script>script> or <onloadscript> trying to bypass bad filtering practices (the last one works in Chamilo 2.1!)
                Only works in IE: <span style="width:eXpressiOn(alert('Ping!'));"></span> but allow normal styles <span style="color:red">red</span>
EOT;
        
        $secure = Security :: remove_XSS($html, false);
        
        // filter injections
        $this->assertNotContains('<script>', $secure);
        $this->assertNotContains('</script>', $secure);
        $this->assertNotContains('<ScRiPt>', $secure);
        $this->assertNotContains('</sCrIpT>', $secure);
        $this->assertNotContains('<a href="javascript:', $secure);
        $this->assertNotContains('" onload="', $secure);
        $this->assertNotContains('eXpressiOn', $secure);
        
        // avoid false positives
        $this->assertContains('&lt;script&gt;', $secure);
        $this->assertContains('javascript:', $secure);
        $this->assertContains('<span style="color:red">', $secure);
    }
}
