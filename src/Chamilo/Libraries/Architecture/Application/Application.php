<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupportInterface;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbGenerator;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @package Chamilo\Libraries\Architecture\Application
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Application
{
    use DependencyInjectionContainerTrait;

    public const ACTION_BROWSER = 'Browser';
    public const ACTION_CREATOR = 'Creator';
    public const ACTION_DELETER = 'Deleter';
    public const ACTION_UPDATER = 'Updater';

    public const PARAM_ACTION = 'go';
    public const PARAM_CONTEXT = 'application';
    public const PARAM_ERROR_MESSAGE = 'error_message';
    public const PARAM_MESSAGE = 'message';
    public const PARAM_MESSAGES = 'messages';
    public const PARAM_MESSAGE_TYPE = 'message_type';
    public const PARAM_WARNING_MESSAGE = 'warning_message';

    public const RESULT_TYPE_CREATED = 'Created';
    public const RESULT_TYPE_DELETED = 'Deleted';
    public const RESULT_TYPE_MOVED = 'Moved';
    public const RESULT_TYPE_UPDATED = 'Updated';

    protected ApplicationConfigurationInterface $applicationConfiguration;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;
        $this->getBreadcrumbGenerator()->addDefaultBreadcrumbs();
    }

    abstract public function run(): Response;

    /**
     * Helper function to call the authorization checker with the current logged in user.
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function checkAuthorization(string $context, ?string $action = null): void
    {
        if (!$this instanceof NoAuthenticationSupportInterface)
        {
            if (!$this->getUser() instanceof User)
            {
                throw new NotAllowedException();
            }
        }
    }

    public function display_error_message(string $message): string
    {
        return $this->getNotificationMessageRenderer()->renderOne(NotificationMessage::error($message));
    }

    public function display_error_page(string $message): string
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->display_error_message($message);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function display_message(string $message, string $type = NotificationMessage::TYPE_INFO): string
    {
        return $this->getNotificationMessageRenderer()->renderOne(new NotificationMessage($message, $type));
    }

    public function display_messages(array $messages, array $types): string
    {
        $notificationMessages = [];

        foreach ($types as $key => $type)
        {
            $notificationMessages[] = new NotificationMessage($messages[$key], $type);
        }

        return $this->getNotificationMessageRenderer()->render($notificationMessages);
    }

    public function display_warning_message(string $message): string
    {
        return $this->getNotificationMessageRenderer()->renderOne(NotificationMessage::warning($message));
    }

    public function display_warning_page(string $message): string
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->display_warning_message($message);
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getAction(): string
    {
        return $this->getRequest()->query->get(static::PARAM_ACTION, static::DEFAULT_ACTION);
    }

    public function getApplicationConfiguration(): ApplicationConfigurationInterface
    {
        return $this->applicationConfiguration;
    }

    public function getBreadcrumbGenerator(): BreadcrumbGenerator
    {
        return $this->getService(BreadcrumbGenerator::class);
    }

    public function getContext(): string
    {
        return $this->getRequest()->query->get(static::PARAM_CONTEXT, 'Chamilo\Core\Admin');
    }

    protected function getPageTitle(): string
    {
        $configurationConsulter = $this->getConfigurationConsulter();

        return $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'institution']) . ' - ' .
            $configurationConsulter->getSetting(['Chamilo\Core\Admin', 'site_name']);
    }

    public function getUser(): ?User
    {
        return $this->getApplicationConfiguration()->getUser();
    }

    public function get_general_result(
        int $failures, int $count, string $singleObject, string $multipleObject,
        string $type = Application::RESULT_TYPE_CREATED
    ): string
    {
        if ($count == 1)
        {
            $param = ['OBJECT' => $singleObject];

            if ($failures)
            {
                $message = 'ObjectNot' . $type;
            }
            else
            {
                $message = 'Object' . $type;
            }
        }
        else
        {
            $param = ['OBJECTS' => $multipleObject];

            if ($failures)
            {
                $message = 'ObjectsNot' . $type;
            }
            else
            {
                $message = 'Objects' . $type;
            }
        }

        return $this->getTranslator()->trans($message, $param, static::CONTEXT);
    }

    public function get_result(
        int $failures, int $count, string $failMessageSingle, string $failMessageMultiple, string $succesMessageSingle,
        string $succesMessageMultiple, string $context = null
    ): string
    {
        if ($failures)
        {
            if ($count == 1)
            {
                $message = $failMessageSingle;
            }
            else
            {
                $message = $failMessageMultiple;
            }
        }
        elseif ($count == 1)
        {
            $message = $succesMessageSingle;
        }
        else
        {
            $message = $succesMessageMultiple;
        }

        return $this->getTranslator()->trans($message, [], $context ?: static::CONTEXT);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function not_allowed(bool $showLoginForm = true)
    {
        throw new NotAllowedException($showLoginForm);
    }

    public function redirect(array $parameters = []): RedirectResponse
    {
        return new RedirectResponse($this->getUrlGenerator()->fromParameters($parameters));
    }

    /**
     * @param string[] $parameters
     */
    public function redirectWithMessage(?string $message = null, bool $errorMessage = false, array $parameters = []
    ): RedirectResponse
    {
        if ($message)
        {
            $messageType = (!$errorMessage) ? NotificationMessage::TYPE_INFO : NotificationMessage::TYPE_DANGER;
            $this->getNotificationMessageManager()->addMessage(new NotificationMessage($message, $messageType));
        }

        return $this->redirect($parameters);
    }

    public function renderFooter(): string
    {
        $html = [];

        if ($this->getPageConfiguration()->isFullPage())
        {
            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';

            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';
        }

        $html[] = $this->getFooterRenderer()->render();

        return implode(PHP_EOL, $html);
    }

    public function renderHeader(string $pageTitle = ''): string
    {
        if ($this->getAction() != static::DEFAULT_ACTION)
        {
            $this->getBreadcrumbGenerator()->addComponentBreadcrumb($this);
        }

        if (!$pageTitle)
        {
            $pageTitle = $this->renderPageTitle();
        }

        $pageConfiguration = $this->getPageConfiguration();
        $pageConfiguration->setApplication($this);
        $pageConfiguration->setTitle($this->getPageTitle());

        $html = [];

        $html[] = $this->getHeaderRenderer()->render();

        if ($pageConfiguration->isFullPage())
        {
            $html[] = '<div class="row">';
            $html[] = '<div class="col-xs-12">';
            $html[] = $pageTitle;
            $html[] = '<div class="clearfix"></div>';
        }

        // Display messages
        $session = $this->getSession();
        $request = $this->getRequest();

        $messages = $session->get(self::PARAM_MESSAGES);

        $session->remove(self::PARAM_MESSAGES);
        if (is_array($messages))
        {
            $html[] = $this->display_messages($messages[self::PARAM_MESSAGE], $messages[self::PARAM_MESSAGE_TYPE]);
        }

        $html[] = $this->getNotificationMessageManager()->renderMessages();

        // DEPRECATED
        // Display messages
        $message = $request->query->get(self::PARAM_MESSAGE);
        $type = $request->query->get(self::PARAM_MESSAGE_TYPE);

        if ($message)
        {
            $html[] = $this->display_message($message, $type);
        }

        $message = $request->query->get(self::PARAM_ERROR_MESSAGE);
        if ($message)
        {
            $html[] = $this->display_error_message($message);
        }

        $message = $request->query->get(self::PARAM_WARNING_MESSAGE);
        if ($message)
        {
            $html[] = $this->display_warning_message($message);
        }

        return implode(PHP_EOL, $html);
    }

    protected function renderPageTitle(): string
    {
        $breadcrumbTrail = $this->getBreadcrumbTrail();

        if ($breadcrumbTrail->size() > 0)
        {
            $pageTitle = $breadcrumbTrail->getLast()->getName();

            return '<h3 id="page-title" title="' . htmlentities(strip_tags($pageTitle)) . '">' . $pageTitle . '</h3>';
        }

        return '';
    }
}
