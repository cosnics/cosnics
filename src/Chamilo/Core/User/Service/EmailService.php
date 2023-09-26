<?php
namespace Chamilo\Core\User\Service;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Form\EmailForm;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessage;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageManager;
use Chamilo\Libraries\Format\NotificationMessage\NotificationMessageRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\User\Email
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EmailService
{
    protected MailerInterface $activeMailer;

    protected ConfigurationConsulter $configurationConsulter;

    protected NotificationMessageManager $notificationMessageManager;

    protected NotificationMessageRenderer $notificationMessageRenderer;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    protected UserService $userService;

    public function __construct(
        MailerInterface $activeMailer, ConfigurationConsulter $configurationConsulter,
        NotificationMessageManager $notificationMessageManager,
        NotificationMessageRenderer $notificationMessageRenderer, Translator $translator, UrlGenerator $urlGenerator,
        UserService $userService
    )
    {
        $this->activeMailer = $activeMailer;
        $this->configurationConsulter = $configurationConsulter;
        $this->notificationMessageManager = $notificationMessageManager;
        $this->notificationMessageRenderer = $notificationMessageRenderer;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->userService = $userService;
    }

    /**
     * @param string[] $targetUserIdentifiers
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function displayTargets(array $targetUserIdentifiers): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default">';

        $html[] = '<div class="panel-heading">';
        $html[] =
            '<h3 class="panel-title">' . $this->getTranslator()->trans('SelectedUsers', [], Manager::CONTEXT) . '</h3>';
        $html[] = '</div>';

        $html[] = '<ul class="list-group">';

        $glyph = new FontAwesomeGlyph('users');

        foreach ($this->getUserService()->findUsersByIdentifiers($targetUserIdentifiers) as $targetUser)
        {
            $targetUser = $targetUser->get_fullname() . ' &lt;' . $targetUser->get_email() . '&gt;';

            $html[] = '<li class="list-group-item">' . $glyph->render() . ' ' . $targetUser . '</li>';
        }

        $html[] = '</ul>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function execute(Application $executingApplication, User $executingUser, array $targetUserIdentifiers
    ): Response
    {
        if ($this->getConfigurationConsulter()->getSetting(['Chamilo\Core\Admin', 'active_online_email_editor']) == 0 ||
            !$executingUser->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $translator = $this->getTranslator();

        if (count($targetUserIdentifiers) > 0)
        {
            $emailForm = $this->getEmailForm($targetUserIdentifiers);

            if ($emailForm->validate())
            {

                try
                {
                    $values = $emailForm->exportValues();

                    $targetsEmailAddresses =
                        $this->getUserService()->findEmailAddressesForUserIdentifiers($targetUserIdentifiers);

                    $mail = new Mail(
                        $values['title'], $values['message'], $targetsEmailAddresses, false,
                        [$executingUser->get_email()], [], $executingUser->get_fullname(), $executingUser->get_email()
                    );

                    $this->getActiveMailer()->sendMail($mail);

                    $this->getNotificationMessageManager()->addMessage(
                        new NotificationMessage(NotificationMessage::TYPE_INFO, $translator->trans('EmailSent'))
                    );
                }
                catch (Exception)
                {
                    $this->getNotificationMessageManager()->addMessage(
                        new NotificationMessage(NotificationMessage::TYPE_DANGER, $translator->trans('EmailNotSent'))
                    );
                }

                return new RedirectResponse(
                    $this->getUrlGenerator()->fromParameters($executingApplication->get_parameters())
                );
            }
            else
            {
                $html = [];

                $html[] = $executingApplication->renderHeader();
                $html[] = $this->displayTargets($targetUserIdentifiers);
                $html[] = $emailForm->render();
                $html[] = $executingApplication->renderFooter();

                return new Response(implode(PHP_EOL, $html));
            }
        }
        else
        {
            $message = new NotificationMessage(
                $translator->trans(
                    'NoObjectSelected', ['OBJECT' => $translator->trans('User', [], Manager::CONTEXT)],
                    StringUtilities::LIBRARIES
                ), NotificationMessage::TYPE_DANGER
            );

            $html = [];

            $html[] = $executingApplication->renderHeader();
            $html[] = $this->getNotificationMessageRenderer()->renderOne($message);
            $html[] = $executingApplication->renderFooter();

            return new Response(implode(PHP_EOL, $html));
        }
    }

    public function getActiveMailer(): MailerInterface
    {
        return $this->activeMailer;
    }

    public function getConfigurationConsulter(): ConfigurationConsulter
    {
        return $this->configurationConsulter;
    }

    /**
     * @param string[] $targetUserIdentifier
     *
     * @throws \QuickformException
     */
    public function getEmailForm(array $targetUserIdentifier): EmailForm
    {
        return new EmailForm(
            $this->getUrlGenerator()->fromRequest([Manager::PARAM_USER_USER_ID => $targetUserIdentifier])
        );
    }

    public function getNotificationMessageManager(): NotificationMessageManager
    {
        return $this->notificationMessageManager;
    }

    public function getNotificationMessageRenderer(): NotificationMessageRenderer
    {
        return $this->notificationMessageRenderer;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }
}