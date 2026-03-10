<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ApplicationHeaderRenderer
{
    protected BreadcrumbGenerator $breadcrumbGenerator;

    protected DefaultHeaderRenderer $defaultHeaderRenderer;

    protected ChamiloRequest $request;

    public function __construct(
        DefaultHeaderRenderer $baseHeaderRenderer, BreadcrumbGenerator $breadcrumbGenerator, ChamiloRequest $request
    )
    {
        $this->defaultHeaderRenderer = $baseHeaderRenderer;
        $this->breadcrumbGenerator = $breadcrumbGenerator;
        $this->request = $request;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Error\Architecture\Exception\UserException
     */
    public function render(ApplicationInterface $application, ?User $user = null): string
    {
        $this->getBreadcrumbGenerator()->addDefaultApplicationBreadcrumbs(
            $application->getApplicationContext(), $this->getCurrentAction($application),
            $application->getDefaultApplicationAction()
        );

        return $this->getDefaultHeaderRenderer()->render($user);
    }

    public function getBreadcrumbGenerator(): BreadcrumbGenerator
    {
        return $this->breadcrumbGenerator;
    }

    public function getCurrentAction(ApplicationInterface $application): string
    {
        return $this->getRequest()->query->get(
            ApplicationInterface::PARAM_ACTION, $application->getDefaultApplicationAction()
        );
    }

    public function getDefaultHeaderRenderer(): DefaultHeaderRenderer
    {
        return $this->defaultHeaderRenderer;
    }

    public function getRequest(): ChamiloRequest
    {
        return $this->request;
    }
}