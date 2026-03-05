<?php
namespace Chamilo\Libraries\Architecture\Domain;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\DependencyInjection\Architecture\Trait\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\NoAuthenticationSupportInterface;
use Chamilo\Libraries\UserInterface\Breadcrumb\Service\BreadcrumbGenerator;
use Chamilo\Libraries\UserInterface\NotificationMessage\Architecture\Domain\NotificationMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Libraries\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Application implements ApplicationInterface
{
    use DependencyInjectionContainerTrait;

    public const PARAM_ACTION = 'action';
    public const PARAM_CONTEXT = 'context';

    /**
     * @throws \Chamilo\Libraries\Protocol\Authentication\Architecture\Exception\NotAllowedException
     */
    public function checkAuthorization(string $context, ?User $user = null, ?string $action = null): void
    {
        if (!$this instanceof NoAuthenticationSupportInterface) {
            if (!$user instanceof User) {
                throw new NotAllowedException();
            }
        }
    }

    public function displayErrorMessage(string $message): string
    {
        return $this->getNotificationMessageRenderer()->renderOne(NotificationMessage::error($message));
    }

    public function displayErrorPage(string $message, ?User $user = null): string
    {
        $html = [];

        $html[] = $this->renderHeader($user);
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

    public function displayWarningPage(string $message, ?User $user = null): string
    {
        $html = [];

        $html[] = $this->renderHeader($user);
        $html[] = $this->displayWarningMessage($message);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getAction(): string
    {
        return $this->getRequest()->query->get(self::PARAM_ACTION, $this->getDefaultAction());
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

    public function renderHeader(?User $user = null): string
    {
        $this->getBreadcrumbGenerator()->addDefaultBreadcrumbs(
            $this->getContext(), $this->getAction(), $this->getDefaultAction()
        );

        return $this->getDefaultHeaderRenderer()->render($user);
    }
}
