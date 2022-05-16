<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
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

        return $this->getApplicationFactory()->getApplication(
            $context, new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    public function get_additional_parameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = FilterData::FILTER_CATEGORY;

        return $additionalParameters;
    }
}
