<?php
namespace Chamilo\Libraries\Architecture\Domain;

use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Layout\Service\ApplicationHeaderRenderer;
use Chamilo\Libraries\UserInterface\Layout\Service\DefaultFooterRenderer;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Application implements ApplicationInterface
{
    public function __construct(
        protected readonly ChamiloRequest $request,
        protected readonly ApplicationHeaderRenderer $applicationHeaderRenderer,
        protected readonly DefaultFooterRenderer $defaultFooterRenderer, protected readonly Translator $translator,
        protected readonly UrlGenerator $urlGenerator
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    protected function checkAuthorization(string $context, ?User $user = null, ?string $action = null): void
    {
        if (!$this instanceof NoAuthenticationSupportInterface) {
            if (!$user instanceof User) {
                throw new NotAllowedException();
            }
        }
    }

    protected function getApplicationHeaderRenderer(): ApplicationHeaderRenderer
    {
        return $this->applicationHeaderRenderer;
    }

    protected function getCurrentAction(): string
    {
        return $this->request->query->get(self::PARAM_ACTION, $this->getDefaultApplicationAction());
    }

    protected function getDefaultFooterRenderer(): DefaultFooterRenderer
    {
        return $this->defaultFooterRenderer;
    }

    protected function getRequest(): ChamiloRequest
    {
        return $this->request;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    protected function renderFooter(): string
    {
        return $this->defaultFooterRenderer->render();
    }

    protected function renderHeader(?User $user = null): string
    {
        return $this->applicationHeaderRenderer->render($this, $user);
    }
}
