<?php
namespace Chamilo\Libraries\Architecture\Domain;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
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
    use DependencyInjectionContainerTrait;

    protected ApplicationHeaderRenderer $applicationHeaderRenderer;

    protected DefaultFooterRenderer $defaultFooterRenderer;

    protected ChamiloRequest $request;

    protected Translator $translator;

    public function __construct(
        ChamiloRequest $request, ApplicationHeaderRenderer $applicationHeaderRenderer,
        DefaultFooterRenderer $defaultFooterRenderer, Translator $translator

    )
    {
        $this->request = $request;
        $this->applicationHeaderRenderer = $applicationHeaderRenderer;
        $this->defaultFooterRenderer = $defaultFooterRenderer;
        $this->translator = $translator;
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
        return $this->getRequest()->query->get(self::PARAM_ACTION, $this->getDefaultApplicationAction());
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

    protected function renderFooter(): string
    {
        return $this->getDefaultFooterRenderer()->render();
    }

    protected function renderHeader(?User $user = null): string
    {
        return $this->getApplicationHeaderRenderer()->render($this, $user);
    }
}
