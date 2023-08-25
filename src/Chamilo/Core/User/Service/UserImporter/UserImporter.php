<?php
namespace Chamilo\Core\User\Service\UserImporter;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Chamilo\Core\User\Domain\UserImporter\ImportUserResult;
use Chamilo\Core\User\Domain\UserImporter\UserImporterResult;
use Chamilo\Core\User\Service\UserImporter\ImportParser\ImportParserFactory;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\WebPathBuilder;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\Translator;

/**
 * Imports users from a given uploaded file
 *
 * @package Chamilo\Core\User\Service\UserImporter
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserImporter
{

    protected ConfigurationConsulter $configurationConsulter;

    protected HashingUtilities $hashingUtilities;

    protected MailerInterface $mailer;

    protected Translator $translator;

    protected ImportParserFactory $userImportParserFactory;

    protected UserService $userService;

    protected UserSettingService $userSettingService;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(
        ImportParserFactory $userImportParserFactory, UserService $userService,
        ConfigurationConsulter $configurationConsulter, HashingUtilities $hashingUtilities, MailerInterface $mailer,
        Translator $translator, WebPathBuilder $webPathBuilder, UserSettingService $userSettingService
    )
    {
        $this->userImportParserFactory = $userImportParserFactory;
        $this->userService = $userService;
        $this->configurationConsulter = $configurationConsulter;
        $this->hashingUtilities = $hashingUtilities;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->webPathBuilder = $webPathBuilder;
        $this->userSettingService = $userSettingService;
    }

    /**
     * Creates the users
     *
     * @param \Chamilo\Core\User\Domain\UserImporter\ImportUserData[] $importUsersData
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function createUsers(
        User $currentUser, array $importUsersData, UserImporterResult $userImporterResult,
        bool $sendMailToNewUsers = false
    )
    {
        foreach ($importUsersData as $importUserData)
        {
            $importUserResult = $importUserData->getImportUserResult();
            if ($importUserResult->isCompleted())
            {
                $userImporterResult->addImportDataResult($importUserResult);
                continue;
            }

            if (!$importUserData->isDelete())
            {
                $importUserData->setPropertiesForUser($this->hashingUtilities);
            }

            $user = $importUserData->getUser();

            if ($importUserData->isNew())
            {
                if (!$this->getUserService()->createUser($user))
                {
                    $importUserResult->addMessage($this->translateMessage('ImportUserCouldNotCreateUserInDatabase'));
                    $importUserResult->setFailed();
                }
                else
                {
                    $importUserResult->setSuccessful();
                    $this->updateLanguageSettingForUser($importUserData);
                    $this->getUserService()->triggerImportEvent($currentUser, $user);

                    if ($sendMailToNewUsers)
                    {
                        $this->sendEmailToNewUser($importUserData);
                    }
                }
            }

            if ($importUserData->isUpdate())
            {
                if (!$this->getUserService()->updateUser($user))
                {
                    $importUserResult->addMessage($this->translateMessage('ImportUserCouldNotUpdateUserInDatabase'));
                    $importUserResult->setFailed();
                }
                else
                {
                    $importUserResult->setSuccessful();
                    $this->updateLanguageSettingForUser($importUserData);

                    if ($sendMailToNewUsers && $importUserData->mustNotifyUser())
                    {
                        $this->sendEmailToNewUser($importUserData);
                    }
                }
            }

            if ($importUserData->isDelete())
            {
                $user->set_active(false);

                if (!$this->getUserService()->updateUser($user))
                {
                    $importUserResult->addMessage($this->translateMessage('ImportUserCouldNotDeleteUserFromDatabase'));
                    $importUserResult->setFailed();
                }
                else
                {
                    $importUserResult->setSuccessful();
                }
            }

            $userImporterResult->addImportDataResult($importUserResult);
        }
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    /**
     * Imports users from a given uploaded file
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function importUsersFromFile(User $currentUser, UploadedFile $file, bool $sendMailToNewUsers = false
    ): UserImporterResult
    {
        $userImporterResult = new UserImporterResult();

        $importParser = $this->userImportParserFactory->getImportParserForUploadedFile($file);
        $importUsersData = $importParser->parse($file, $userImporterResult);

        $this->validateAndCompleteImportedUsers($importUsersData);
        $this->createUsers($currentUser, $importUsersData, $userImporterResult, $sendMailToNewUsers);

        return $userImporterResult;
    }

    /**
     * Sends an email to a new user, identified by his import data
     *
     * @param ImportUserData $importUserData
     */
    protected function sendEmailToNewUser(ImportUserData $importUserData)
    {
        $user = $importUserData->getUser();

        $options = [];
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $importUserData->getPassword();
        $options['site_name'] = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'site_name']);
        $options['site_url'] = $this->getWebPathBuilder()->getBasePath();

        $options['admin_firstname'] = $this->configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_firstname']
        );

        $options['admin_surname'] = $this->configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_surname']
        );

        $options['admin_telephone'] = $this->configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_telephone']
        );

        $options['admin_email'] = $this->configurationConsulter->getSetting(
            ['Chamilo\Core\Admin', 'administrator_email']
        );

        $subject = $this->translateMessage('YourRegistrationOn') . ' ' . $options['site_name'];

        $body = $this->configurationConsulter->getSetting(['Chamilo\Core\User', 'email_template']);
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = new Mail(
            $subject, $body, $user->get_email(), true, [], [],
            $options['admin_firstname'] . ' ' . $options['admin_surname'], $options['admin_email']
        );

        $importUserResult = $importUserData->getImportUserResult();

        try
        {
            $this->mailer->sendMail($mail);
            $importUserResult->addMessage($this->translateMessage('ImportUserMailSent'));
        }
        catch (Exception $ex)
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserMailNotSent'));
        }
    }

    /**
     * Translates a given message, with optionally the given parameters
     *
     * @param string[] $parameters
     */
    protected function translateMessage(string $message, array $parameters = []): string
    {
        return $this->translator->trans($message, $parameters, 'Chamilo\\Core\\User');
    }

    /**
     * Updates the language setting for a given user, identified by his import data.
     * If the language is not set
     * then it will not be updated
     *
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    protected function updateLanguageSettingForUser(ImportUserData $importUserData)
    {
        if (empty($importUserData->getLanguage()))
        {
            return;
        }

        if (!$this->getUserSettingService()->saveUserSettingForSettingContextVariableAndUser(
            'Chamilo\Core\Admin', 'platform_language', $importUserData->getUser(), $importUserData->getLanguage()
        ))
        {
            $importUserResult = $importUserData->getImportUserResult();

            $importUserResult->addMessage($this->translateMessage('ImportUserCouldNotChangeLanguage'));
            $importUserResult->setFailed();
        }
    }

    protected function validateAction(ImportUserData $importUserData, ImportUserResult $importUserResult)
    {
        $user = $importUserData->getUser();
        $userExists = $user instanceof User;

        if (!$importUserData->hasValidAction())
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserNoValidActionFound'));
            $importUserResult->setFailed();
            throw new RuntimeException($this->translateMessage('ImportUserNoValidActionFound'));
        }

        if ($importUserData->isNewOrUpdate())
        {
            if ($userExists)
            {
                $importUserData->setActionToUpdate();
                $importUserResult->addMessage($this->translateMessage('ImportUserUserFoundUpdating'));
            }
            else
            {
                $importUserData->setActionToNew();
                $importUserResult->addMessage($this->translateMessage('ImportUserUserNotFoundCreating'));
            }
        }

        if ($importUserData->isNew() && $userExists)
        {
            $importUserResult->setFailed();
            $importUserResult->addMessage($this->translateMessage('ImportUserUserAlreadyExists'));

            throw new RuntimeException($this->translateMessage('ImportUserUserAlreadyExists'));
        }

        if ($importUserData->isNew())
        {
            $importUserData->setUser(new User());
        }

        if ($importUserData->isUpdate() && !$userExists)
        {
            $importUserResult->setFailed();
            $importUserResult->addMessage($this->translateMessage('ImportUserUserDoesNotExist'));

            throw new RuntimeException($this->translateMessage('ImportUserUserDoesNotExist'));
        }

        if ($importUserData->isDelete() && !$userExists)
        {
            $importUserResult->setSuccessful();
            $importUserResult->addMessage($this->translateMessage('ImportUserUserAlreadyRemoved'));
        }
    }

    protected function validateActive(ImportUserData $importUserData, ImportUserResult $importUserResult)
    {
        if ($importUserData->isNew() && !$importUserData->isActiveSet())
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserActiveNotFound'));
            $importUserData->setActive(true);
        }
    }

    /**
     * Validates the imported users and completes the data where necessary
     *
     * @param \Chamilo\Core\User\Domain\UserImporter\ImportUserData[] $importUsersData
     */
    protected function validateAndCompleteImportedUsers(array $importUsersData)
    {
        $emailRequired = (bool) $this->configurationConsulter->getSetting(['Chamilo\Core\User', 'require_email']);
        $platformLanguage = $this->configurationConsulter->getSetting(['Chamilo\Core\Admin', 'platform_language']);

        foreach ($importUsersData as $importUserData)
        {
            $importUserResult = new ImportUserResult($importUserData);

            try
            {
                $this->validateUsername($importUserData, $importUserResult);
                $this->validateAction($importUserData, $importUserResult);
                $this->validateStatus($importUserData, $importUserResult);
                $this->validateActive($importUserData, $importUserResult);
                $this->validateAuthSource($importUserData, $importUserResult);
                $this->validateLanguage($importUserData, $importUserResult, $platformLanguage);
                $this->validateEmail($importUserData, $importUserResult, $emailRequired);
            }
            catch (Exception $exception)
            {
                continue;
            }
        }
    }

    protected function validateAuthSource(ImportUserData $importUserData, ImportUserResult $importUserResult)
    {
        if ($importUserData->isNew() && empty($importUserData->getAuthSource()))
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserAuthSourceNotFound'));
            $importUserData->setAuthSource('Platform');
        }
    }

    protected function validateEmail(
        ImportUserData $importUserData, ImportUserResult $importUserResult, bool $emailRequired
    )
    {
        if ($importUserData->isNew() && $emailRequired && empty($importUserData->getEmail()))
        {
            $importUserResult->setFailed();
            $importUserResult->addMessage($this->translateMessage('ImportUserEmailRequiredForNewUsers'));

            throw new RuntimeException($this->translateMessage('ImportUserEmailRequiredForNewUsers'));
        }
    }

    protected function validateLanguage(
        ImportUserData $importUserData, ImportUserResult $importUserResult, string $platformLanguage
    )
    {
        if ($importUserData->isNew() && !$importUserData->hasValidLanguage())
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserNoValidLanguageFound'));
            $importUserData->setLanguage($platformLanguage);
        }
    }

    protected function validateStatus(ImportUserData $importUserData, ImportUserResult $importUserResult)
    {
        if ($importUserData->isNew() && !$importUserData->hasValidStatus())
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserNoValidStatusFound'));
            $importUserData->setStatusToStudent();
        }
    }

    protected function validateUsername(ImportUserData $importUserData, ImportUserResult $importUserResult)
    {
        if (empty($importUserData->getUsername()))
        {
            $importUserResult->setFailed();
            $importUserResult->addMessage($this->translateMessage('ImportUserNoUsernameFound'));

            throw new RuntimeException($this->translateMessage('ImportUserNoUsernameFound'));
        }

        $user = $this->getUserService()->findUserByUsername($importUserData->getUsername());

        if ($user instanceof User)
        {
            $importUserData->setUser($user);
        }
    }
}