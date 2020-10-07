<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\Resource\StylesheetGenerator;

/**
 * @package Chamilo\Libraries\Ajax\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StylesheetComponent extends Manager implements NoAuthenticationSupport, NoVisitTraceComponentInterface
{

    public function run()
    {
        $this->getStyleSheetGenerator()->generateStylesheets();
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Resource\StylesheetGenerator
     */
    public function getStyleSheetGenerator()
    {
        return $this->getService(StylesheetGenerator::class);
    }
}