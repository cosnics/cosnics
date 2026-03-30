<?php
namespace Chamilo\Libraries\Protocol\ExceptionHandling\Service;

use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Interface\UserExceptionInterface;
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
    public function __construct(
        protected AlertRenderer $alertRenderer, protected BreadcrumbGenerator $breadcrumbGenerator,
        protected SecurityUtilities $securityUtilities, protected Translator $translator
    )
    {
    }

    public function render(UserExceptionInterface $userException): string
    {
        $this->breadcrumbGenerator->addRootAndTitleBreadcrumbs(
            $this->securityUtilities->removeXSS($this->renderTitle($userException))
        );

        return $this->alertRenderer->render(
            new Alert($this->securityUtilities->removeXSS($this->renderMessage($userException)), AlertEnum::DANGER)
        );
    }

    abstract public function renderMessage(UserExceptionInterface $userException): string;

    abstract public function renderTitle(UserExceptionInterface $userException): string;
}