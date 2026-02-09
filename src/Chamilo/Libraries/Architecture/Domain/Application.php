<?php
namespace Chamilo\Libraries\Architecture\Domain;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Application
{
    use DependencyInjectionContainerTrait;

    public const PARAM_ACTION = 'go';
    public const PARAM_CONTEXT = 'application';

    protected ?User $user;

    public function __construct(?User $user = null)
    {
        $this->user = $user;

        $this->getBreadcrumbGenerator()->addDefaultBreadcrumbs();
    }

    abstract public function run(): Response;

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function checkAuthorization(string $context, ?string $action = null): void
    {
        if (!$this instanceof NoAuthenticationSupportInterface) {
            if (!$this->getUser() instanceof User) {
                throw new NotAllowedException();
            }
        }
    }

    public function displayErrorMessage(string $message): string
    {
        return $this->getNotificationMessageRenderer()->renderOne(NotificationMessage::error($message));
    }

    public function displayErrorPage(string $message): string
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->displayErrorMessage($message);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function displayMessage(string $message, string $type = NotificationMessage::TYPE_INFO): string
    {
        return $this->getNotificationMessageRenderer()->renderOne(new NotificationMessage($message, $type));
    }

    public function displayMessages(array $messages, array $types): string
    {
        $notificationMessages = [];

        foreach ($types as $key => $type) {
            $notificationMessages[] = new NotificationMessage($messages[$key], $type);
        }

        return $this->getNotificationMessageRenderer()->render($notificationMessages);
    }

    public function displayWarningMessage(string $message): string
    {
        return $this->getNotificationMessageRenderer()->renderOne(NotificationMessage::warning($message));
    }

    public function displayWarningPage(string $message): string
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->displayWarningMessage($message);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getBreadcrumbGenerator(): BreadcrumbGenerator
    {
        return $this->getService(BreadcrumbGenerator::class);
    }

    public function getResult(
        int $failures, int $count, string $failMessageSingle, string $failMessageMultiple, string $succesMessageSingle,
        string $succesMessageMultiple, ?string $context = null
    ): string
    {
        if ($failures) {
            if ($count == 1) {
                $message = $failMessageSingle;
            }
            else {
                $message = $failMessageMultiple;
            }
        }
        elseif ($count == 1) {
            $message = $succesMessageSingle;
        }
        else {
            $message = $succesMessageMultiple;
        }

        return $this->getTranslator()->trans($message, [], $context ?: static::CONTEXT);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param string[] $parameters
     */
    public function redirectWithMessage(?string $message = null, bool $errorMessage = false, array $parameters = []
    ): RedirectResponse
    {
        if ($message) {
            $messageType = (!$errorMessage) ? NotificationMessage::TYPE_INFO : NotificationMessage::TYPE_DANGER;
            $this->getNotificationMessageManager()->addMessage(new NotificationMessage($message, $messageType));
        }

        return new RedirectResponse($this->getUrlGenerator()->fromParameters($parameters));
    }

    public function renderFooter(): string
    {
        return $this->getDefaultFooterRenderer()->render();
    }

    public function renderHeader(): string
    {
        return $this->getDefaultHeaderRenderer()->render($this, $this->getUser());
    }
}
