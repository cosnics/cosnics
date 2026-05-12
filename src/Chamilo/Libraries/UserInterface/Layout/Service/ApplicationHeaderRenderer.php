<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ApplicationHeaderRenderer
{
    public function __construct(
        protected DefaultHeaderRenderer $defaultHeaderRenderer, protected BreadcrumbGenerator $breadcrumbGenerator,
        protected ChamiloRequest $request
    )
    {
    }

    public function render(ApplicationInterface $application, ?User $user = null): string
    {
        $this->breadcrumbGenerator->addDefaultApplicationBreadcrumbs(
            $application->getApplicationContext(), $this->getCurrentAction($application),
            $application->getDefaultApplicationAction()
        );

        return $this->defaultHeaderRenderer->render($user);
    }

    public function getCurrentAction(ApplicationInterface $application): string
    {
        return $this->request->query->get(
            ApplicationInterface::PARAM_ACTION, $application->getDefaultApplicationAction()
        );
    }
}