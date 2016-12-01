<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Application\ApplicationFactory;
use Chamilo\Libraries\Architecture\Interfaces\ApplicationSupport;

/**
 *
 * @package Chamilo\Core\Repository\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WorkspaceComponent extends Manager implements ApplicationSupport
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $context = \Chamilo\Core\Repository\Workspace\Manager::context();
        $applicationFactory = new ApplicationFactory(
            $context, 
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this));
        return $applicationFactory->run();
    }

    public function get_additional_parameters()
    {
        $parameters = parent::get_additional_parameters();
        $parameters[] = FilterData::FILTER_CATEGORY;
        
        return $parameters;
    }
}
