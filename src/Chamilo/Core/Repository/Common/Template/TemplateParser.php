<?php
namespace Chamilo\Core\Repository\Common\Template;

use DOMXPath;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TemplateParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return Template
     */
    public static function parse(DOMXPath $dom_xpath);
}