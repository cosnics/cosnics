<?php
namespace Chamilo\Libraries\Ajax\Component;

use Chamilo\Libraries\Ajax\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Architecture\Interfaces\NoVisitTraceComponentInterface;
use Chamilo\Libraries\Architecture\Resource\ResourceGenerator;

/**
 * @package Chamilo\Libraries\Ajax\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class StylesheetComponent extends Manager implements NoAuthenticationSupport, NoVisitTraceComponentInterface
{

    public function run()
    {
        $this->getResourceGenerator()->generateResourceFiles();
    }

    /**
     * @return \Chamilo\Libraries\Architecture\Resource\ResourceGenerator
     */
    public function getResourceGenerator()
    {
        return $this->getService(ResourceGenerator::class);
    }

}