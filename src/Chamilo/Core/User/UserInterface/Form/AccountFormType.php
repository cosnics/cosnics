<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Manager;
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
class AccountFormType extends AbstractType
{
    public const string PROPERTY_CONFIRM_PASSWORD = 'confirm_password';
    public const string PROPERTY_CURRENT_PASSWORD = 'current_password';
    public const string PROPERTY_GENERATE_PASSWORD = 'generate_password';

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

    public function __construct(
        FormTypeBuilder $formTypeBuilder, FormButtonTypeBuilder $formButtonTypeBuilder, Translator $translator,
        AuthenticationValidator $authenticationValidator, array $userRights, array $userRequirements
    )
    {
        $this->translator = $translator;
        $this->formTypeBuilder = $formTypeBuilder;
        $this->formButtonTypeBuilder = $formButtonTypeBuilder;
        $this->authenticationValidator = $authenticationValidator;
        $this->userRights = $userRights;
        $this->userRequirements = $userRequirements;
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilder = $this->getFormTypeBuilder();
        $translator = $this->getTranslator();
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         */
        $user = $options['user'];

        $this->buildPersonalDetailsForm($builder, $options);
        $this->buildSecurityForm($builder, $options);

        if ($this->canUserChangeAnything($options['user'])) {
            $this->getFormButtonTypeBuilder()->addSaveAndResetButton($builder);
        }
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
        $user = $options['user'];

        if ($this->hasUserRight('cosnics.application.user.rights.changePassword')) {
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

        // Firstname
        $givenNameLabel = $translator->trans('GivenName', [], Manager::CONTEXT);

        if ($this->hasUserRight('cosnics.application.user.rights.changeGivenName')) {
            $formTypeBuilder->addText(builder: $builder, name: User::PROPERTY_GIVEN_NAME, label: $givenNameLabel);
            //            $this->applyFilter(User::PROPERTY_GIVEN_NAME, 'stripslashes');
            //            $this->applyFilter(User::PROPERTY_GIVEN_NAME, 'trim');
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
            //            $this->applyFilter(User::PROPERTY_GIVEN_NAME, 'stripslashes');
            //            $this->applyFilter(User::PROPERTY_GIVEN_NAME, 'trim');
        }
        else {
            $formTypeBuilder->addVisualContent(
                builder: $builder, name: User::PROPERTY_SURNAME, label: $surnameLabel
            );
        }

        // Email
        $emailLabel = $translator->trans('Email', [], Manager::CONTEXT);

        if ($this->hasUserRight('cosnics.application.user.rights.changeEmail')) {
            $formTypeBuilder->addText(builder: $builder, name: User::PROPERTY_EMAIL, label: $emailLabel, required: $this->getUserRequirement('cosnics.application.user.require.email'));
            //            $this->addRule(User::PROPERTY_EMAIL, $translator->trans('EmailWrong', [], Manager::CONTEXT),
            //                HTML_QuickForm_Rule_Email::class);
            //            $this->applyFilter(User::PROPERTY_EMAIL, 'stripslashes');
            //            $this->applyFilter(User::PROPERTY_EMAIL, 'trim');
        }
        else {
            $formTypeBuilder->addVisualContent(
                builder: $builder, name: User::PROPERTY_EMAIL, label: $emailLabel
            );
        }

        // Username
        //        $this->addTextfield(
        //            User::PROPERTY_USERNAME, $translator->trans('Username', [], Manager::CONTEXT), $allowedToChangeUsername
        //        );
        //
        //        if (!$allowedToChangeUsername) {
        //            $this->freeze(User::PROPERTY_USERNAME);
        //            $this->getRenderer()->setElementTemplate($this->getFrozenElementTemplate(), User::PROPERTY_USERNAME);
        //        }
        //        else {
        //            $this->applyFilter(User::PROPERTY_USERNAME, 'stripslashes');
        //            $this->applyFilter(User::PROPERTY_USERNAME, 'trim');
        //            $this->addRule(
        //                User::PROPERTY_USERNAME, $translator->trans('UsernameWrong', [], Manager::CONTEXT),
        //                HTML_QuickForm_Rule_Username::class
        //            );
        //        }

        // Official Code
        //        $this->addTextfield(
        //            User::PROPERTY_OFFICIAL_CODE, $translator->trans('OfficialCode', [], Manager::CONTEXT),
        //            $allowedToChangeOfficialCode && $requiresOfficialCode
        //        );

        //        if (!$allowedToChangeOfficialCode) {
        //            $this->freeze(User::PROPERTY_OFFICIAL_CODE);
        //            $this->getRenderer()->setElementTemplate($this->getFrozenElementTemplate(), User::PROPERTY_OFFICIAL_CODE);
        //        }
        //        else {
        //            $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'stripslashes');
        //            $this->applyFilter(User::PROPERTY_OFFICIAL_CODE, 'trim');
        //        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function buildSecurityForm(FormBuilderInterface $builder, array $options): void
    {
        $formTypeBuilder = $this->getFormTypeBuilder();
        $translator = $this->getTranslator();
        /**
         * @var \Chamilo\Core\User\Storage\DataClass\User $user
         */
        $user = $options['user'];

        $formTypeBuilder->addCategory(
            $builder, 'category_security', $translator->trans('Security', [], Manager::CONTEXT)
        );

        $this->buildPasswordForm($builder, $options, false, true, true);

        $formTypeBuilder->addVisualContent(
            $builder, User::PROPERTY_SECURITY_TOKEN, $translator->trans('SecurityToken', [], Manager::CONTEXT)
        );
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
        $resolver->setDefaults(['user' => null]);

        $resolver->setNormalizer('user', static function (Options $options, $user) {
            if (!$user instanceof User) {
                throw new LogicException('The user must be an instance of User.');
            }

            return $user;
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

    protected function hasUserRight(string $variabele): bool
    {
        return $this->getUserRights()[$variabele] ?? false;
    }

    public static function validateCurrentPassword(mixed $value, ExecutionContextInterface $context, mixed $payload
    ): void
    {
        $authentication = $payload['authentication'];
        $user = $payload['user'];

        if (!$authentication instanceof ChangeablePasswordInterface || !$user instanceof User ||
            !$authentication->verifyPassword($user, $value)) {
            $context->buildViolation('CurrentPasswordInvalid')->atPath(self::PROPERTY_CURRENT_PASSWORD)->addViolation();
        }
    }
}