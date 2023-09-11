<?php
namespace Chamilo\Libraries\Architecture\Application;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\MenuComponent;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\DependencyInjection\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @package Chamilo\Libraries\Architecture\Application
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Application
{
    use DependencyInjectionContainerTrait;

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

    public const SETTING_BREADCRUMBS_DISABLED = 'breadcrumbs_disabled';

    /**
     * @var string[]
     */
    public static array $application_path_cache = [];

    protected ApplicationConfigurationInterface $applicationConfiguration;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        $this->applicationConfiguration = $applicationConfiguration;
    }

    /**
     * @return string|\Symfony\Component\HttpFoundation\Response
     */
    abstract public function run();

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
    }

    public function areBreadcrumbsDisabled(): bool
    {
        return $this->getApplicationConfiguration()->get(self::SETTING_BREADCRUMBS_DISABLED) == true;
    }

    /**
     * Helper function to call the authorization checker with the current logged in user.
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function checkAuthorization(string $context, ?string $action = null)
    {
        if (!$this instanceof NoAuthenticationSupport)
        {
            if (!$this->getUser() instanceof User)
            {
                throw new NotAllowedException();
            }

            $this->getAuthorizationChecker()->checkAuthorization($this->getUser(), $context, $action);
        }
    }

    public function display_error_message(string $message): string
    {
        return $this->getNotificationMessageRenderer()->renderOne(NotificationMessage::error($message));
    }

    public function display_error_page(string $message): string
    {
        if ($this->get_application() instanceof Application)
        {
            return $this->get_application()->display_error_page($message);
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->display_error_message($message);
        $html[] = $this->render_footer();

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
        if ($this->get_application() instanceof Application)
        {
            return $this->get_application()->display_warning_page($message);
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->display_warning_message($message);
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        return $additionalParameters;
    }

    public function getApplicationConfiguration(): ApplicationConfigurationInterface
    {
        return $this->applicationConfiguration;
    }

    /**
     * @param string[] $parameters
     * @param string[] $filter
     */
    public function getLink(array $parameters = [], array $filter = []): string
    {
        return $this->getUrlGenerator()->fromRequest($parameters, $filter);
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

    public function get_action(): ?string
    {
        return $this->get_parameter(static::PARAM_ACTION);
    }

    public function get_application(): ?Application
    {
        return $this->getApplicationConfiguration()->getApplication();
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
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

    public function get_level(): int
    {
        $level = 0;
        $application = $this;

        while ($application->get_application() instanceof Application)
        {
            $level ++;
            $application = $application->get_application();
        }

        return $level;
    }

    public function get_parameter(string $name)
    {
        return Parameters::getInstance()->get_parameter($this, $name);
    }

    public function get_parameters(): array
    {
        return Parameters::getInstance()->get_parameters($this);
    }

    /**
     * @deprecated Use get_application() now
     */
    public function get_parent(): ?Application
    {
        return $this->get_application();
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
     * Gets the URL of the current page in the application. Optionally takes an associative array of name/value pairs
     * representing additional query string parameters; these will either be added to the parameters already present,
     * or override them if a value with the same name exists.
     *
     * @param array $parameters
     * @param array $filter
     *
     * @return string
     */
    public function get_url(array $parameters = [], array $filter = []): string
    {
        return $this->getLink(array_merge($this->get_parameters(), $parameters), $filter);
    }

    /**
     * @deprecated Use Application::getUser() now
     */
    public function get_user(): ?User
    {
        return $this->getUser();
    }

    /**
     * @deprecated Use Application::getUser()::getId() now
     */
    public function get_user_id(): ?string
    {
        if ($this->getApplicationConfiguration()->getUser())
        {
            return $this->getUser()->getId();
        }

        return 0;
    }

    public function isAuthorized(string $context, ?string $action = null): bool
    {
        if (!$this->getUser() instanceof User)
        {
            return false;
        }

        return $this->getAuthorizationChecker()->isAuthorized($this->getUser(), $context, $action);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function not_allowed(bool $showLoginForm = true)
    {
        throw new NotAllowedException($showLoginForm);
    }

    public function redirect(array $parameters = [], array $filter = [])
    {
        $response = new RedirectResponse($this->getLink(array_merge($this->get_parameters(), $parameters), $filter));
        $response->send();
        exit;
    }

    /**
     * @param string[] $parameters
     * @param string[] $filter
     */
    public function redirectWithMessage(
        ?string $message = null, bool $errorMessage = false, array $parameters = [], array $filter = []
    )
    {
        if ($message)
        {
            $messageType = (!$errorMessage) ? NotificationMessage::TYPE_INFO : NotificationMessage::TYPE_DANGER;
            $this->getNotificationMessageManager()->addMessage(new NotificationMessage($message, $messageType));
        }

        $this->redirect($parameters, $filter);
    }

    public function renderFooter(): string
    {
        if ($this->get_application())
        {
            return $this->get_application()->render_footer();
        }

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
        if (!$pageTitle)
        {
            $pageTitle = $this->renderPageTitle();
        }

        if ($this->get_application())
        {
            return $this->get_application()->render_header($pageTitle);
        }

        $pageConfiguration = $this->getPageConfiguration();
        $pageConfiguration->setApplication($this);
        $pageConfiguration->setTitle($this->getPageTitle());

        $html = [];

        $html[] = $this->getHeaderRenderer()->render();

        if ($pageConfiguration->isFullPage())
        {
            $html[] = '<div class="row">';

            // If there is an application-wide menu, show it
            if ($this instanceof MenuComponent)
            {
                $html[] = '<div class="col-xs-12 col-md-4 col-lg-3">';
                $html[] = $this->renderApplicationMenu();
                $html[] = '</div>';
                $html[] = '<div class="col-xs-12 col-md-8 col-lg-9">';
            }
            else
            {
                $html[] = '<div class="col-xs-12">';
            }

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
        $breadcrumbTrail = BreadcrumbTrail::getInstance();

        if ($breadcrumbTrail->size() > 0)
        {
            $pageTitle = BreadcrumbTrail::getInstance()->get_last()->getName();

            return '<h3 id="page-title" title="' . htmlentities(strip_tags($pageTitle)) . '">' . $pageTitle . '</h3>';
        }

        return '';
    }

    /**
     * @deprecated Use Application::renderFooter() now
     */
    public function render_footer(): string
    {
        return $this->renderFooter();
    }

    /**
     * @deprecated Use Application::renderHeader() now
     */
    public function render_header(string $pageTitle = ''): string
    {
        return $this->renderHeader($pageTitle);
    }

    public function set_action(?string $action)
    {
        $this->set_parameter(static::PARAM_ACTION, $action);
    }

    public function set_parameter(string $name, $value)
    {
        Parameters::getInstance()->set_parameter($this, $name, $value);
    }
}
