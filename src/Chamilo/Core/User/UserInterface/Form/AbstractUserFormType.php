<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeablePasswordInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeableUsernameInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @package Chamilo\Core\User\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractUserFormType extends AbstractType
{
    public const string PROPERTY_CONFIRM_PASSWORD = 'confirm_password';
    public const string PROPERTY_CURRENT_PASSWORD = 'current_password';
    public const string PROPERTY_GENERATE_PASSWORD = 'generate_password';
    public const string PROPERTY_SEND_MAIL = 'send_mail';

    protected AuthenticationValidator $authenticationValidator;

    protected FormButtonTypeBuilder $formButtonTypeBuilder;

    protected FormTypeBuilder $formTypeBuilder;

    protected Translator $translator;

    /**
     * @var array<bool>
     */
    protected array $userRequirements;

    /**
     * @var array<bool>
     */
    protected array $userRights;

    protected UserService $userService;

    public function __construct(
        FormTypeBuilder $formTypeBuilder, FormButtonTypeBuilder $formButtonTypeBuilder, Translator $translator,
        AuthenticationValidator $authenticationValidator, UserService $userService, array $userRights,
        array $userRequirements
    )
    {
        $this->translator = $translator;
        $this->formTypeBuilder = $formTypeBuilder;
        $this->formButtonTypeBuilder = $formButtonTypeBuilder;
        $this->authenticationValidator = $authenticationValidator;
        $this->userRights = $userRights;
        $this->userRequirements = $userRequirements;
        $this->userService = $userService;
    }

    public function buildAccountForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilder = $this->getFormTypeBuilder();
        $translator = $this->getTranslator();

        $formTypeBuilder->addCategory(
            $builder, 'category_account', $translator->trans('Account', [], Manager::CONTEXT)
        );

        if ($options['isLockoutRisk']) {
            $formTypeBuilder->addWarning(
                $builder, 'lockout', $translator->trans('LockOutWarningMessage', [], Manager::CONTEXT)
            );
        }

        $formTypeBuilder->addCheckbox(
            $builder, User::PROPERTY_ACTIVE, $translator->trans('Active', [], Manager::CONTEXT)
        );

        $formTypeBuilder->addCheckbox(
            $builder, User::PROPERTY_PLATFORM_ADMINISTRATOR,
            $translator->trans('PlatformAdministrator', [], Manager::CONTEXT)
        );
    }

    public function buildMailForm(FormBuilderInterface $builder): void
    {
        $formTypeBuilder = $this->getFormTypeBuilder();
        $translator = $this->getTranslator();

        $formTypeBuilder->addCategory(
            $builder, 'category_mail', $translator->trans('Mail', [], Manager::CONTEXT)
        );

        $formTypeBuilder->addCheckbox(
            $builder, self::PROPERTY_SEND_MAIL, $translator->trans('SendMailToUser', [], Manager::CONTEXT)
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildPasswordForm(
        FormBuilderInterface $builder, array $options, bool $allowedToGeneratePassword = true,
        bool $requiresCurrentPassword = false, bool $requiresPasswordConfirmation = false
    ): void
    {
        $formTypeBuilder = $this->getFormTypeBuilder();
        $translator = $this->getTranslator();
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         */
        $user = $options['user'];
        $authentication = $this->getAuthentication($user);

        if ($this->hasUserRight('cosnics.application.user.rights.changePassword') &&
            $authentication instanceof ChangeablePasswordInterface) {
            if ($allowedToGeneratePassword) {
                $formTypeBuilder->addCheckbox(
                    $builder, self::PROPERTY_GENERATE_PASSWORD,
                    $translator->trans('GeneratePassword', [], Manager::CONTEXT)
                );
            }

            if ($requiresCurrentPassword) {
                $constraint = new Assert\Callback(callback: [$this, 'validateCurrentPassword'],
                    payload: ['authentication' => $this->getAuthentication($user), 'user' => $user]);

                $formTypeBuilder->addPassword(
                    builder: $builder, name: self::PROPERTY_CURRENT_PASSWORD, label: $translator->trans(
                    'CurrentPassword', [], Manager::CONTEXT
                ), constraints: [$constraint]
                );
            }

            $formTypeBuilder->addPassword(
                builder: $builder, name: User::PROPERTY_PASSWORD, label: $translator->trans('Password', [],
                Manager::CONTEXT), validateStrength: true
            );

            if ($requiresPasswordConfirmation) {
                $constraint = new Assert\EqualTo([
                    'propertyPath' => 'parent.all[' . User::PROPERTY_PASSWORD . '].data',
                    'message' => 'The passwords must match',
                ]);

                $formTypeBuilder->addPassword(
                    builder: $builder, name: self::PROPERTY_CONFIRM_PASSWORD, label: $translator->trans(
                    'PasswordConfirmation', [], Manager::CONTEXT
                ), validateStrength: true, constraints: [$constraint]
                );
            }
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildPersonalDetailsForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilder = $this->getFormTypeBuilder();
        $translator = $this->getTranslator();
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         */
        $user = $options['user'];
        $authentication = $this->getAuthentication($user);

        // Firstname
        $givenNameLabel = $translator->trans('GivenName', [], Manager::CONTEXT);

        if ($this->hasUserRight('cosnics.application.user.rights.changeGivenName')) {
            $formTypeBuilder->addText(builder: $builder, name: User::PROPERTY_GIVEN_NAME, label: $givenNameLabel);
        }
        else {
            $formTypeBuilder->addVisualContent(
                builder: $builder, name: User::PROPERTY_GIVEN_NAME, label: $givenNameLabel
            );
        }

        // Lastname
        $surnameLabel = $translator->trans('Surname', [], Manager::CONTEXT);

        if ($this->hasUserRight('cosnics.application.user.rights.changeSurname')) {
            $formTypeBuilder->addText(builder: $builder, name: User::PROPERTY_SURNAME, label: $surnameLabel);
        }
        else {
            $formTypeBuilder->addVisualContent(
                builder: $builder, name: User::PROPERTY_SURNAME, label: $surnameLabel
            );
        }

        // Email
        $emailLabel = $translator->trans('Email', [], Manager::CONTEXT);

        if ($this->hasUserRight('cosnics.application.user.rights.changeEmail')) {
            $formTypeBuilder->addEmail(
                builder: $builder, name: User::PROPERTY_EMAIL, label: $emailLabel, required: $this->getUserRequirement(
                'cosnics.application.user.require.email',
            ), constraints: [new Assert\Email()]
            );
        }
        else {
            $formTypeBuilder->addVisualContent(
                builder: $builder, name: User::PROPERTY_EMAIL, label: $emailLabel
            );
        }

        // Username
        $usernameLabel = $translator->trans('Username', [], Manager::CONTEXT);

        if ($this->hasUserRight('cosnics.application.user.rights.changeUsername') &&
            $authentication instanceof ChangeableUsernameInterface) {
            $constraint = new Assert\Callback(callback: [$this, 'validateUserName'], payload: ['user' => $user]);

            $formTypeBuilder->addText(
                builder: $builder, name: User::PROPERTY_USERNAME, label: $usernameLabel, constraints: [$constraint]
            );
        }
        else {
            $formTypeBuilder->addVisualContent(
                builder: $builder, name: User::PROPERTY_USERNAME, label: $usernameLabel
            );
        }

        // Official Code
        $officialCodeLabel = $translator->trans('OfficialCode', [], Manager::CONTEXT);

        if ($this->hasUserRight('cosnics.application.user.rights.changeOfficialCode')) {
            $formTypeBuilder->addText(
                builder: $builder, name: User::PROPERTY_OFFICIAL_CODE, label: $officialCodeLabel,
                required: $this->getUserRequirement('cosnics.application.user.require.officialCode')
            );
        }
        else {
            $formTypeBuilder->addVisualContent(
                builder: $builder, name: User::PROPERTY_OFFICIAL_CODE, label: $officialCodeLabel
            );
        }
    }

    public function buildPictureForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilder = $this->getFormTypeBuilder();
        $translator = $this->getTranslator();
        /**
         * @var ?string $encodedUserPicture
         */
        $encodedUserPicture = $options['encodedUserPicture'];

        if ($encodedUserPicture) {
            $html = [];

            $html[] = '<div class="mb-3">';
            $html[] = '<h6>' . $translator->trans('CurrentImage', [], Manager::CONTEXT) . '</h6>';
            $html[] = '<img class="img-thumbnail" src="' . $encodedUserPicture . '" />';
            $html[] = '</div>';

            $formTypeBuilder->addHtml($builder, User::PROPERTY_PICTURE_URI . '_display', implode(PHP_EOL, $html));
        }

        $formTypeBuilder->addFile(
            $builder, User::PROPERTY_PICTURE_URI, $translator->trans('AddPicture', [], Manager::CONTEXT),
            constraints: [new Assert\Image()]
        );
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildSecurityForm(FormBuilderInterface $builder, array $options, bool $addTokenField = false): void
    {
        $formTypeBuilder = $this->getFormTypeBuilder();
        $translator = $this->getTranslator();

        $formTypeBuilder->addCategory(
            $builder, 'category_security', $translator->trans('Security', [], Manager::CONTEXT)
        );

        $this->buildPasswordForm($builder, $options, false, true, true);

        if ($addTokenField) {
            $formTypeBuilder->addVisualContent(
                $builder, User::PROPERTY_SECURITY_TOKEN, $translator->trans('SecurityToken', [], Manager::CONTEXT)
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function canUserChangeAnything(User $user): bool
    {
        $authentication = $this->getAuthentication($user);

        $allowedToChangeFirstName = $this->hasUserRight('cosnics.application.user.rights.changeGivenName');
        $allowedToChangeLastName = $this->hasUserRight('cosnics.application.user.rights.changeSurname');
        $allowedToChangeUsername = $this->hasUserRight('cosnics.application.user.rights.changeUsername') &&
            $authentication instanceof ChangeableUsernameInterface;
        $allowedToChangeEmailAddress = $this->hasUserRight('cosnics.application.user.rights.changeEmail');
        $allowedToChangeOfficialCode = $this->hasUserRight('cosnics.application.user.rights.changeOfficialCode');
        $allowedToChangePassword = $this->hasUserRight('cosnics.application.user.rights.changePassword') &&
            $authentication instanceof ChangeablePasswordInterface;

        return $allowedToChangeFirstName || $allowedToChangeLastName || $allowedToChangeUsername ||
            $allowedToChangeEmailAddress || $allowedToChangeOfficialCode || $allowedToChangePassword;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['user' => null, 'isLockoutRisk' => false, 'encodedUserPicture' => null]);

        $resolver->setNormalizer('user', static function (Options $options, $user) {
            if (!$user instanceof User) {
                throw new LogicException('The user must be an instance of User.');
            }

            return $user;
        });

        $resolver->setNormalizer('encodedUserPicture', static function (Options $options, $encodedUserPicture) {
            if (!is_string($encodedUserPicture) && !is_null($encodedUserPicture)) {
                throw new LogicException('$encodedUserPicture must be a string or null.');
            }

            return $encodedUserPicture;
        });

        $resolver->setNormalizer('isLockoutRisk', static function (Options $options, $isLockoutRisk) {
            if (!is_bool($isLockoutRisk)) {
                throw new LogicException('$isLockoutRisk must be a boolean.');
            }

            return $isLockoutRisk;
        });
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function getAuthentication(User $user): AuthenticationInterface
    {
        return $this->authenticationValidator->getAuthenticationByType($user->getAuthenticationSource());
    }

    public function getAuthenticationValidator(): AuthenticationValidator
    {
        return $this->authenticationValidator;
    }

    public function getFormButtonTypeBuilder(): FormButtonTypeBuilder
    {
        return $this->formButtonTypeBuilder;
    }

    public function getFormTypeBuilder(): FormTypeBuilder
    {
        return $this->formTypeBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function getUserRequirement(string $variabele): bool
    {
        return $this->getUserRequirements()[$variabele] ?? false;
    }

    /**
     * @return array<bool>
     */
    public function getUserRequirements(): array
    {
        return $this->userRequirements;
    }

    /**
     * @return array<bool>
     */
    public function getUserRights(): array
    {
        return $this->userRights;
    }

    public function getUserService(): UserService
    {
        return $this->userService;
    }

    protected function hasUserRight(string $variabele): bool
    {
        return $this->getUserRights()[$variabele] ?? false;
    }

    public function validateCurrentPassword(mixed $value, ExecutionContextInterface $context, mixed $payload): void
    {
        $authentication = $payload['authentication'];
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         */
        $user = $payload['user'];

        if (!$authentication instanceof ChangeablePasswordInterface || !$user instanceof User ||
            !$authentication->verifyPassword($user, $value)) {
            $context->buildViolation('CurrentPasswordInvalid')->atPath(self::PROPERTY_CURRENT_PASSWORD)->addViolation();
        }
    }

    public function validateUsername(mixed $value, ExecutionContextInterface $context, mixed $payload): void
    {
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         */
        $user = $payload['user'];

        if (!$this->getUserService()->isUsernameAvailableForUser($user, $value)) {
            $context->buildViolation('UsernameInvalid')->atPath(User::PROPERTY_USERNAME)->addViolation();
        }
    }
}