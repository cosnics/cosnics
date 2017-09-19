<?php

namespace Chamilo\Core\User\Service\UserImporter;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Core\User\Domain\UserImporter\ImportUserData;
use Chamilo\Core\User\Domain\UserImporter\ImportUserResult;
use Chamilo\Core\User\Domain\UserImporter\UserImporterResult;
use Chamilo\Core\User\Service\UserImporter\ImportParser\ImportParserFactory;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\Repository\UserRepository;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Hashing\HashingUtilities;
use Chamilo\Libraries\Mail\Mailer\MailerInterface;
use Chamilo\Libraries\Mail\ValueObject\Mail;
use Gedmo\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\Translator;

/**
 * Imports users from a given uploaded file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserImporter
{
    /**
     * @var ImportParserFactory
     */
    protected $userImportParserFactory;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var ConfigurationConsulter
     */
    protected $configurationConsulter;

    /**
     * @var HashingUtilities
     */
    protected $hashingUtilities;

    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * UserImporter constructor.
     *
     * @param ImportParserFactory $userImportParserFactory
     * @param UserRepository $userRepository
     * @param ConfigurationConsulter $configurationConsulter
     * @param HashingUtilities $hashingUtilities
     * @param MailerInterface $mailer
     * @param Translator $translator
     */
    public function __construct(
        ImportParserFactory $userImportParserFactory, UserRepository $userRepository,
        ConfigurationConsulter $configurationConsulter, HashingUtilities $hashingUtilities,
        MailerInterface $mailer, Translator $translator
    )
    {
        $this->userImportParserFactory = $userImportParserFactory;
        $this->userRepository = $userRepository;
        $this->configurationConsulter = $configurationConsulter;
        $this->hashingUtilities = $hashingUtilities;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * Imports users from a given uploaded file
     *
     * @param User $currentUser
     * @param UploadedFile $file
     *
     * @param bool $sendMailToNewUsers
     *
     * @return UserImporterResult
     */
    public function importUsersFromFile(User $currentUser, UploadedFile $file, $sendMailToNewUsers = false)
    {
        $userImporterResult = new UserImporterResult();

        $importParser = $this->userImportParserFactory->getImportParserForUploadedFile($file);
        $importUsersData = $importParser->parse($file, $userImporterResult);

        $this->validateAndCompleteImportedUsers($importUsersData);
        $this->createUsers($currentUser, $importUsersData, $userImporterResult, $sendMailToNewUsers);

        return $userImporterResult;
    }

    /**
     * Validates the imported users and completes the data where necessary
     *
     * @param ImportUserData[] $importUsersData
     */
    protected function validateAndCompleteImportedUsers($importUsersData)
    {
        $emailRequired = $this->configurationConsulter->getSetting(['Chamilo\Core\User', 'require_email']);
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
            catch (\Exception $exception)
            {
                continue;
            }
        }
    }

    /**
     * Creates the users
     *
     * @param User $currentUser
     * @param ImportUserData[] $importUsersData
     * @param UserImporterResult $userImporterResult
     * @param bool $sendMailToNewUsers
     */
    protected function createUsers(
        User $currentUser, $importUsersData, UserImporterResult $userImporterResult, $sendMailToNewUsers = false
    )
    {
        foreach ($importUsersData as $importUserData)
        {
            $importUserResult = $importUserData->getImportUserResult();
            if ($importUserResult->isCompleted())
            {
                $userImporterResult->addImportUserResult($importUserResult);
                continue;
            }

            if (!$importUserData->isDelete())
            {
                $importUserData->setPropertiesForUser($this->hashingUtilities);
            }

            $user = $importUserData->getUser();

            if ($importUserData->isNew())
            {
                if (!$this->userRepository->create($user))
                {
                    $importUserResult->addMessage($this->translateMessage('ImportUserCouldNotCreateUserInDatabase'));
                    $importUserResult->setFailed();
                }
                else
                {
                    $importUserResult->setSuccessful();
                    $this->updateLanguageSettingForUser($importUserData);
                    $this->userRepository->triggerImportEvent($currentUser, $user);

                    if($sendMailToNewUsers)
                    {
                        $this->sendEmailToNewUser($importUserData);
                    }
                }
            }

            if ($importUserData->isUpdate())
            {
                if (!$this->userRepository->update($user))
                {
                    $importUserResult->addMessage($this->translateMessage('ImportUserCouldNotUpdateUserInDatabase'));
                    $importUserResult->setFailed();
                }
                else
                {
                    $this->updateLanguageSettingForUser($importUserData);
                    $importUserResult->setSuccessful();
                }
            }

            if ($importUserData->isDelete())
            {
                $user->set_active(false);

                if (!$this->userRepository->update($user))
                {
                    $importUserResult->addMessage($this->translateMessage('ImportUserCouldNotDeleteUserFromDatabase'));
                    $importUserResult->setFailed();
                }
                else
                {
                    $importUserResult->setSuccessful();
                }
            }

            $userImporterResult->addImportUserResult($importUserResult);
        }
    }

    /**
     * Updates the language setting for a given user, identified by his import data. If the language is not set
     * then it will not be updated
     *
     * @param ImportUserData $importUserData
     */
    protected function updateLanguageSettingForUser(ImportUserData $importUserData)
    {
        if (empty($importUserData->getLanguage()))
        {
            return;
        }

        if (!$this->userRepository->createUserSettingForSettingAndUser(
            'Chamilo\Core\Admin', 'platform_language', $importUserData->getUser(), $importUserData->getLanguage()
        ))
        {
            $importUserResult = $importUserData->getImportUserResult();

            $importUserResult->addMessage($this->translateMessage('ImportUserCouldNotChangeLanguage'));
            $importUserResult->setFailed();
        }
    }

    /**
     * Sends an email to a new user, identified by his import data
     *
     * @param ImportUserData $importUserData
     */
    protected function sendEmailToNewUser(ImportUserData $importUserData)
    {
        $user = $importUserData->getUser();

        $options = array();
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $importUserData->getPassword();
        $options['site_name'] = $this->configurationConsulter->getSetting(array('Chamilo\Core\Admin', 'site_name'));
        $options['site_url'] = Path::getInstance()->getBasePath(true);

        $options['admin_firstname'] = $this->configurationConsulter->getSetting(
            array('Chamilo\Core\Admin', 'administrator_firstname')
        );

        $options['admin_surname'] = $this->configurationConsulter->getSetting(
            array('Chamilo\Core\Admin', 'administrator_surname')
        );

        $options['admin_telephone'] = $this->configurationConsulter->getSetting(
            array('Chamilo\Core\Admin', 'administrator_telephone')
        );

        $options['admin_email'] = $this->configurationConsulter->getSetting(
            array('Chamilo\Core\Admin', 'administrator_email')
        );

        $subject = $this->translateMessage('YourRegistrationOn') . ' ' . $options['site_name'];

        $body = $this->configurationConsulter->getSetting(array('Chamilo\Core\User', 'email_template'));
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = new Mail(
            $subject,
            $body,
            $user->get_email(),
            true,
            array(),
            array(),
            $options['admin_firstname'] . ' ' . $options['admin_surname'],
            $options['admin_email']
        );

        $importUserResult = $importUserData->getImportUserResult();

        try
        {
            $this->mailer->sendMail($mail);
            $importUserResult->addMessage($this->translateMessage('ImportUserMailSent'));
        }
        catch (\Exception $ex)
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserMailNotSent'));
        }
    }

    /**
     * @param ImportUserData $importUserData
     * @param ImportUserResult $importUserResult
     */
    protected function validateUsername($importUserData, $importUserResult)
    {
        if (empty($importUserData->getUsername()))
        {
            $importUserResult->setFailed();
            $importUserResult->addMessage($this->translateMessage('ImportUserNoUsernameFound'));

            throw new RuntimeException($this->translateMessage('ImportUserNoUsernameFound'));
        }

        $user = $this->userRepository->findUserByUsername($importUserData->getUsername());

        if($user instanceof User)
        {
            $importUserData->setUser($user);
        }
    }

    /**
     * @param ImportUserData $importUserData
     * @param ImportUserResult $importUserResult
     */
    protected function validateAction($importUserData, $importUserResult)
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

        if($importUserData->isNew())
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

    /**
     * @param ImportUserData $importUserData
     * @param ImportUserResult $importUserResult
     */
    protected function validateStatus($importUserData, $importUserResult)
    {
        if ($importUserData->isNew() && !$importUserData->hasValidStatus())
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserNoValidStatusFound'));
            $importUserData->setStatusToStudent();
        }
    }

    /**
     * @param ImportUserData $importUserData
     * @param ImportUserResult $importUserResult
     */
    protected function validateActive($importUserData, $importUserResult)
    {
        if ($importUserData->isNew() && !$importUserData->isActiveSet())
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserActiveNotFound'));
            $importUserData->setActive(true);
        }
    }

    /**
     * @param ImportUserData $importUserData
     * @param ImportUserResult $importUserResult
     */
    protected function validateAuthSource($importUserData, $importUserResult)
    {
        if ($importUserData->isNew() && empty($importUserData->getAuthSource()))
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserAuthSourceNotFound'));
            $importUserData->setAuthSource('Platform');
        }
    }

    /**
     * @param ImportUserData $importUserData
     * @param ImportUserResult $importUserResult
     * @param string $platformLanguage
     */
    protected function validateLanguage($importUserData, $importUserResult, $platformLanguage)
    {
        if ($importUserData->isNew() && !$importUserData->hasValidLanguage())
        {
            $importUserResult->addMessage($this->translateMessage('ImportUserNoValidLanguageFound'));
            $importUserData->setLanguage($platformLanguage);
        }
    }

    /**
     * @param ImportUserData $importUserData
     * @param ImportUserResult $importUserResult
     * @param bool $emailRequired
     */
    protected function validateEmail($importUserData, $importUserResult, $emailRequired)
    {
        if ($importUserData->isNew() && $emailRequired && empty($importUserData->getEmail()))
        {
            $importUserResult->setFailed();
            $importUserResult->addMessage($this->translateMessage('ImportUserEmailRequiredForNewUsers'));

            throw new RuntimeException($this->translateMessage('ImportUserEmailRequiredForNewUsers'));
        }
    }

    /**
     * Translates a given message, with optionally the given parameters
     *
     * @param string $message
     * @param array $parameters
     *
     * @return string
     */
    protected function translateMessage($message, $parameters = [])
    {
        return $this->translator->trans($message, $parameters, 'Chamilo\\Core\\User');
    }
}