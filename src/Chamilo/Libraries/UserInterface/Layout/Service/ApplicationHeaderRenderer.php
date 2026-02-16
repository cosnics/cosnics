<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Domain\Application;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ApplicationHeaderRenderer
{
    protected BreadcrumbGenerator $breadcrumbGenerator;

    protected DefaultHeaderRenderer $defaultHeaderRenderer;

    public function __construct(DefaultHeaderRenderer $defaultHeaderRenderer, BreadcrumbGenerator $breadcrumbGenerator)
    {
        $this->defaultHeaderRenderer = $defaultHeaderRenderer;
        $this->breadcrumbGenerator = $breadcrumbGenerator;
    }

    public function render(Application $application, ?User $user = null): string
    {
        $this->getBreadcrumbGenerator()->addComponentBreadcrumb($application);

        return $this->getDefaultHeaderRenderer()->render($user);
    }

    public function getBreadcrumbGenerator(): BreadcrumbGenerator
    {
        return $this->breadcrumbGenerator;
    }

    public function getDefaultHeaderRenderer(): DefaultHeaderRenderer
    {
        return $this->defaultHeaderRenderer;
    }
}