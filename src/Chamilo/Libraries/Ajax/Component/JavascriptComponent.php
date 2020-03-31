<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\Resource\JavascriptGenerator;

/**
 * @package Chamilo\Libraries\Ajax\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JavascriptComponent extends Manager implements NoAuthenticationSupport, NoVisitTraceComponentInterface
{

    public function run()
    {
        $this->getJavascriptGenerator()->run();
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Resource\JavascriptGenerator
     */
    public function getJavascriptGenerator()
    {
        return $this->getService(JavascriptGenerator::class);
    }
}