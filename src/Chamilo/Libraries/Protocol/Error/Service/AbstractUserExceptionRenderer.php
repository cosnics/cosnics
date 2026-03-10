<?php
namespace Chamilo\Libraries\Protocol\Error\Service;

use Chamilo\Libraries\Protocol\Error\Architecture\Interface\UserExceptionInterface;
use Chamilo\Libraries\Protocol\Security\Service\SecurityUtilities;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Domain\Alert;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Alert\Service\AlertRenderer;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Protocol\Error\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractUserExceptionRenderer
{
    protected AlertRenderer $alertRenderer;

    protected BreadcrumbGenerator $breadcrumbGenerator;

    protected SecurityUtilities $securityUtilities;

    protected Translator $translator;

    public function __construct(
        AlertRenderer $alertRenderer, BreadcrumbGenerator $breadcrumbGenerator, SecurityUtilities $securityUtilities,
        Translator $translator
    )
    {
        $this->alertRenderer = $alertRenderer;
        $this->breadcrumbGenerator = $breadcrumbGenerator;
        $this->securityUtilities = $securityUtilities;
        $this->translator = $translator;
    }

    public function render(UserExceptionInterface $userException): string
    {
        $securityUtilities = $this->getSecurityUtilities();

        $this->getBreadcrumbGenerator()->addRootAndTitleBreadcrumbs(
            $securityUtilities->removeXSS($this->renderTitle($userException))
        );

        return $this->getAlertRenderer()->render(
            new Alert($securityUtilities->removeXSS($this->renderMessage($userException)), AlertEnum::DANGER)
        );
    }

    public function getAlertRenderer(): AlertRenderer
    {
        return $this->alertRenderer;
    }

    protected function getBreadcrumbGenerator(): BreadcrumbGenerator
    {
        return $this->breadcrumbGenerator;
    }

    protected function getSecurityUtilities(): SecurityUtilities
    {
        return $this->securityUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    abstract public function renderMessage(UserExceptionInterface $userException): string;

    abstract public function renderTitle(UserExceptionInterface $userException): string;
}