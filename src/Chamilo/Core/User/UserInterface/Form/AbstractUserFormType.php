<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Architecture\Interface\UserPictureProviderInterface;
use Chamilo\Core\User\Architecture\Interface\UserPictureUpdateProviderInterface;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\AuthenticationInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeablePasswordInterface;
use Chamilo\Libraries\Protocol\Authentication\Architecture\Interface\ChangeableUsernameInterface;
use Chamilo\Libraries\Protocol\Authentication\Service\AuthenticationValidator;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException;
use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
    public const string CATEGORY_ACCOUNT = 'category_account';
    public const string CATEGORY_MAIL = 'category_mail';
    public const string CATEGORY_SECURITY = 'category_security';
    public const string PROPERTY_LOCKOUT = 'lockout';
    public const string PROPERTY_PASSWORD_CONFIRM = 'confirm_password';
    public const string PROPERTY_PASSWORD_CURRENT = 'current_password';
    public const string PROPERTY_PASSWORD_GENERATE = 'generate_password';
    public const string PROPERTY_PICTURE_CURRENT = 'current_picture';
    public const string PROPERTY_PICTURE_REMOVE = 'remove_picture';
    public const string PROPERTY_SEND_MAIL = 'send_mail';

    /**
     * @param array<bool> $userRights
     * @param array<bool> $userRequirements
     */
    public function __construct(
        protected FormTypeBuilder $formTypeBuilder, protected FormButtonTypeBuilder $formButtonTypeBuilder,
        protected Translator $translator, protected AuthenticationValidator $authenticationValidator,
        protected UserService $userService, protected UserPictureProviderInterface $userPictureProvider,
        protected array $userRights = [], protected array $userRequirements = []
    )
    {
    }

    public function buildAccountForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $this->formTypeBuilder->createCategory(
                $builder, self::CATEGORY_ACCOUNT, $this->translator->trans('Account', [], Manager::CONTEXT)
            )
        );

        if ($options['isLockoutRisk']) {
            $builder->add(
                $this->formTypeBuilder->createMessage(
                    $builder, self::PROPERTY_LOCKOUT,
                    $this->translator->trans('LockOutWarningMessage', [], Manager::CONTEXT),
                    $this->translator->trans('LockOutWarningLabel', [], Manager::CONTEXT), AlertEnum::WARNING

                )
            );
        }

        $builder->add(
            $this->formTypeBuilder->createCheckbox(
                $builder, User::PROPERTY_ACTIVE, $this->translator->trans('Active', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $this->formTypeBuilder->createCheckbox(
                $builder, User::PROPERTY_PLATFORM_ADMINISTRATOR,
                $this->translator->trans('PlatformAdministrator', [], Manager::CONTEXT)
            )
        );
    }

    public function buildMailForm(FormBuilderInterface $builder): void
    {
        $builder->add(
            $this->formTypeBuilder->createCategory(
                $builder, self::CATEGORY_MAIL, $this->translator->trans('Mail', [], Manager::CONTEXT)
            )
        );

        $builder->add(
            $this->formTypeBuilder->createCheckbox(
                $builder, self::PROPERTY_SEND_MAIL, $this->translator->trans('SendMailToUser', [], Manager::CONTEXT)
            )
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
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         * @var \Chamilo\Core\User\Storage\DataClass\User $executingUser
         */
        $user = $options['user'];
        $executingUser = $options['executingUser'];

        if ($this->isPasswordChangeable($executingUser, $user)) {
            if ($allowedToGeneratePassword) {
                $builder->add(
                    $this->formTypeBuilder->createCheckbox(
                        $builder, self::PROPERTY_PASSWORD_GENERATE,
                        $this->translator->trans('GeneratePassword', [], Manager::CONTEXT)
                    )
                );
            }

            if ($this->isCurrentPasswordRequired($requiresCurrentPassword, $user)) {
                $constraint = new Assert\Callback(callback: [$this, 'validateCurrentPassword'],
                    payload: ['authentication' => $this->getAuthentication($user), 'user' => $user]);

                $builder->add(
                    $this->formTypeBuilder->createPassword(
                        builder: $builder, name: self::PROPERTY_PASSWORD_CURRENT, label: $this->translator->trans(
                        'CurrentPassword', [], Manager::CONTEXT
                    ), constraints: [$constraint]
                    )
                );
            }

            $builder->add(
                $this->formTypeBuilder->createPassword(
                    builder: $builder, name: User::PROPERTY_PASSWORD, label: $this->translator->trans('Password', [],
                    Manager::CONTEXT), validateStrength: true
                )
            );

            if ($requiresPasswordConfirmation) {
                $constraint = new Assert\EqualTo([
                    'propertyPath' => 'parent.all[' . User::PROPERTY_PASSWORD . '].data',
                    'message' => 'The passwords must match',
                ]);

                $builder->add(
                    $this->formTypeBuilder->createPassword(
                        builder: $builder, name: self::PROPERTY_PASSWORD_CONFIRM, label: $this->translator->trans(
                        'PasswordConfirmation', [], Manager::CONTEXT
                    ), validateStrength: true, constraints: [$constraint]
                    )
                );
            }
        }
    }

    public function buildPersonalDetailsForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         * @var \Chamilo\Core\User\Storage\DataClass\User $executingUser
         */
        $user = $options['user'];
        $executingUser = $options['executingUser'];

        // Firstname
        $givenNameLabel = $this->translator->trans('GivenName', [], Manager::CONTEXT);

        if ($this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeGivenName')) {
            $builder->add(
                $this->formTypeBuilder->createText(
                    builder: $builder, name: User::PROPERTY_GIVEN_NAME, label: $givenNameLabel
                )
            );
        }
        else {
            $builder->add(
                $this->formTypeBuilder->createVisualContent(
                    builder: $builder, name: User::PROPERTY_GIVEN_NAME, label: $givenNameLabel
                )
            );
        }

        // Lastname
        $surnameLabel = $this->translator->trans('Surname', [], Manager::CONTEXT);

        if ($this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeSurname')) {
            $builder->add(
                $this->formTypeBuilder->createText(
                    builder: $builder, name: User::PROPERTY_SURNAME, label: $surnameLabel
                )
            );
        }
        else {
            $builder->add(
                $this->formTypeBuilder->createVisualContent(
                    builder: $builder, name: User::PROPERTY_SURNAME, label: $surnameLabel
                )
            );
        }

        // Email
        $emailLabel = $this->translator->trans('Email', [], Manager::CONTEXT);

        if ($this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeEmail')) {
            $builder->add(
                $this->formTypeBuilder->createEmail(
                    builder: $builder, name: User::PROPERTY_EMAIL, label: $emailLabel,
                    required: $this->getUserRequirement(
                        'cosnics.application.user.require.email',
                    ), constraints: [new Assert\Email()]
                )
            );
        }
        else {
            $builder->add(
                $this->formTypeBuilder->createVisualContent(
                    builder: $builder, name: User::PROPERTY_EMAIL, label: $emailLabel
                )
            );
        }

        // Username
        $usernameLabel = $this->translator->trans('Username', [], Manager::CONTEXT);

        if ($this->isUsernameChangeable($executingUser, $user)) {
            $constraint = new Assert\Callback(callback: [$this, 'validateUserName'], payload: ['user' => $user]);

            $builder->add(
                $this->formTypeBuilder->createText(
                    builder: $builder, name: User::PROPERTY_USERNAME, label: $usernameLabel, constraints: [$constraint]
                )
            );
        }
        else {
            $builder->add(
                $this->formTypeBuilder->createVisualContent(
                    builder: $builder, name: User::PROPERTY_USERNAME, label: $usernameLabel
                )
            );
        }

        // Official Code
        $officialCodeLabel = $this->translator->trans('OfficialCode', [], Manager::CONTEXT);

        if ($this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeOfficialCode')) {
            $builder->add(
                $this->formTypeBuilder->createText(
                    builder: $builder, name: User::PROPERTY_OFFICIAL_CODE, label: $officialCodeLabel,
                    required: $this->getUserRequirement('cosnics.application.user.require.officialCode')
                )
            );
        }
        else {
            $builder->add(
                $this->formTypeBuilder->createVisualContent(
                    builder: $builder, name: User::PROPERTY_OFFICIAL_CODE, label: $officialCodeLabel
                )
            );
        }
    }

    public function buildPictureForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         */
        $user = $options['user'];

        if ($this->userPictureProvider instanceof UserPictureUpdateProviderInterface) {
            if ($user instanceof User) {
                $encodedUserPicture = $this->userPictureProvider->getUserPictureAsBase64String(
                    $user, false
                );

                $builder->add(
                    $this->formTypeBuilder->createPicture(
                        builder: $builder, name: self::PROPERTY_PICTURE_CURRENT, label: $this->translator->trans(
                        'CurrentPicture', [], Manager::CONTEXT
                    ), noPictureLabel: $this->translator->trans(
                        'NoCurrentPicture', [], Manager::CONTEXT
                    ), pictureStyles: ['max-height' => '250px']
                    )
                );

                if ($encodedUserPicture) {
                    $builder->add(
                        $this->formTypeBuilder->createCheckbox(
                            $builder, self::PROPERTY_PICTURE_REMOVE,
                            $this->translator->trans('RemoveCurrentPicture', [], Manager::CONTEXT)
                        )
                    );
                }
            }

            $builder->add(
                $this->formTypeBuilder->createFile(
                    builder: $builder, name: User::PROPERTY_PICTURE_URI, label: $this->translator->trans(
                    'AddPicture', [], Manager::CONTEXT
                ), required: false, constraints: [new Assert\Image()]
                )
            );
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildSecurityForm(FormBuilderInterface $builder, array $options, bool $addTokenField = false): void
    {
        $builder->add(
            $this->formTypeBuilder->createCategory(
                $builder, self::CATEGORY_SECURITY, $this->translator->trans('Security', [], Manager::CONTEXT)
            )
        );

        $this->buildPasswordForm($builder, $options, false, true, true);

        if ($addTokenField) {
            $builder->add(
                $this->formTypeBuilder->createVisualContent(
                    $builder, User::PROPERTY_SECURITY_TOKEN,
                    $this->translator->trans('SecurityToken', [], Manager::CONTEXT)
                )
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['user' => null, 'executingUser' => null, 'isLockoutRisk' => false]);

        $resolver->setAllowedTypes('user', [User::class, 'null']);
        $resolver->setAllowedTypes('executingUser', [User::class]);
        $resolver->setAllowedTypes('isLockoutRisk', ['bool']);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function getAuthentication(User $user): AuthenticationInterface
    {
        return $this->authenticationValidator->getAuthenticationByType($user->getAuthenticationSource());
    }

    protected function getUserRequirement(string $variabele): bool
    {
        return $this->userRequirements[$variabele] ?? false;
    }

    protected function hasUserRight(User $executingUser, string $variabele): bool
    {
        return $executingUser->isPlatformAdministrator() || ($this->userRights[$variabele] ?? false);
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    protected function isAnythingChangeable(User $executingUser, ?User $user = null): bool
    {
        if ($user instanceof User) {
            $authentication = $this->getAuthentication($user);
            $changeableUsernameAuthentication = $authentication instanceof ChangeableUsernameInterface;
            $changeablePasswordAuthentication = $authentication instanceof ChangeablePasswordInterface;
        }
        else {
            $changeableUsernameAuthentication =
            $changeablePasswordAuthentication = $executingUser->isPlatformAdministrator();
        }

        $allowedToChangeFirstName =
            $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeGivenName');
        $allowedToChangeLastName = $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeSurname');
        $allowedToChangeUsername =
            $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeUsername') &&
            $changeableUsernameAuthentication;
        $allowedToChangeEmailAddress =
            $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeEmail');
        $allowedToChangeOfficialCode =
            $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeOfficialCode');
        $allowedToChangePassword =
            $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changePassword') &&
            $changeablePasswordAuthentication;

        return $allowedToChangeFirstName || $allowedToChangeLastName || $allowedToChangeUsername ||
            $allowedToChangeEmailAddress || $allowedToChangeOfficialCode || $allowedToChangePassword ||
            $this->isPictureChangeable($executingUser);
    }

    public function isCurrentPasswordRequired(bool $requiresCurrentPassword = false, ?User $user = null): bool
    {
        if ($user instanceof User) {
            return $requiresCurrentPassword;
        }

        return false;
    }

    public function isPasswordChangeable(User $executingUser, ?User $user = null): bool
    {
        $hasPasswordRight = $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changePassword');

        if ($user instanceof User) {
            try {
                $authentication = $this->getAuthentication($user);

                return $hasPasswordRight && $authentication instanceof ChangeablePasswordInterface;
            }
            catch (NoSuchClassException) {
                return false;
            }
        }
        else {
            return $executingUser->isPlatformAdministrator();
        }
    }

    protected function isPictureChangeable(User $executingUser): bool
    {
        return $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeUserPicture') &&
            $this->userPictureProvider instanceof UserPictureUpdateProviderInterface;
    }

    public function isUsernameChangeable(User $executingUser, ?User $user = null): bool
    {
        if ($user instanceof User) {
            $hasUsernameRight = $this->hasUserRight($executingUser, 'cosnics.application.user.rights.changeUsername');

            try {
                $authentication = $this->getAuthentication($user);

                return $hasUsernameRight && $authentication instanceof ChangeableUsernameInterface;
            }
            catch (NoSuchClassException) {
                return false;
            }
        }
        else {
            return $executingUser->isPlatformAdministrator();
        }
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
            $context->buildViolation('CurrentPasswordInvalid')->atPath(self::PROPERTY_PASSWORD_CURRENT)->addViolation();
        }
    }

    public function validateUsername(mixed $value, ExecutionContextInterface $context, mixed $payload): void
    {
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         */
        $user = $payload['user'];

        if (($user instanceof User && !$this->userService->isUsernameAvailableForUser($user, $value)) ||
            !$this->userService->isUsernameAvailable($value)) {
            $context->buildViolation('UsernameInvalid')->atPath(User::PROPERTY_USERNAME)->addViolation();
        }
    }
}