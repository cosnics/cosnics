<?php
namespace Chamilo\Core\Repository\Workspace\Component;

use Chamilo\Core\Repository\Workspace\Manager;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\Workspace\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RightsComponent extends TabComponent implements DelegateComponent
{

    /**
     * Adds additional breadcrumbs
     *
     * @param BreadcrumbTrail $breadcrumb_trail
     */
    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumb_trail)
    {
        $browserSource = $this->get_parameter(self::PARAM_BROWSER_SOURCE);

        $breadcrumb_trail->add(
            new Breadcrumb(
                $this->get_url(array(Manager::PARAM_ACTION => $browserSource)),
                Translation::get($browserSource . 'Component')
            )
        );
    }

    public function build()
    {
        return $this->getApplicationFactory()->getApplication(
            \Chamilo\Core\Repository\Workspace\Rights\Manager::CONTEXT,
            new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
        )->run();
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_WORKSPACE_ID;
        $additionalParameters[] = self::PARAM_BROWSER_SOURCE;

        return parent::getAdditionalParameters($additionalParameters);
    }
}