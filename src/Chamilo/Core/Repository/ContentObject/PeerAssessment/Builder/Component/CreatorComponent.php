<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Component;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Builder\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

class CreatorComponent extends Manager
{

    public function run()
    {
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\Builder\Action\Manager::context(), 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $factory->run();
    }
    
    /*
     * function get_additional_parameters() { return array(self :: PARAM_TYPE); }
     */
}
