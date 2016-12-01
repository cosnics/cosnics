<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\AssignmentDataProvider;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Preview\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ViewerComponent extends Manager
{

    public function run()
    {
        $configuration = new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this);
        $configuration->set(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::CONFIGURATION_DATA_PROVIDER, 
            new AssignmentDataProvider());
        
        $factory = new ApplicationFactory(
            \Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager::context(), 
            $configuration);
        
        return $factory->run();
    }
}
