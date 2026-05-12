<?php
namespace Chamilo\Core\User\UserInterface\Form;

use Chamilo\Core\User\Architecture\Enum\ActionEnum;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\Entity\User;
use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\ButtonsFormType;
use Chamilo\Libraries\UserInterface\Form\Service\FormButtonTypeBuilder;
use Chamilo\Libraries\UserInterface\Form\Service\FormTypeBuilder;
use Chamilo\Libraries\UserInterface\Glyph\Architecture\Domain\FontAwesomeGlyph;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package Chamilo\Core\User\UserInterface\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LoginFormType extends AbstractType
{
    public function __construct(
        protected FormTypeBuilder $formTypeBuilder, protected FormButtonTypeBuilder $formButtonTypeBuilder,
        protected Translator $translator, protected UrlGenerator $urlGenerator, protected bool $canRetrievePassword,
        protected bool $canRegister
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $this->formTypeBuilder->createText(
                builder: $builder, name: User::PROPERTY_USERNAME, label: $this->translator->trans('Username', [],
                Manager::CONTEXT)
            )
        );

        $builder->add(
            $this->formTypeBuilder->createPassword(
                builder: $builder, name: User::PROPERTY_PASSWORD, label: $this->translator->trans('Password', [],
                Manager::CONTEXT)
            )
        );

        $builder->add(
            $this->formTypeBuilder->createEmail(
                builder: $builder, name: User::PROPERTY_EMAIL, label: $this->translator->trans('Email', [],
                Manager::CONTEXT), constraints: [new Assert\Email()]
            )
        );

        $buttons = [];

        $loginText = $this->translator->trans('Login', [], Manager::CONTEXT);
        $loginGlyph = new FontAwesomeGlyph('sign-in-alt', ['me-1'], $loginText, 'fas');

        $buttons[] = $this->formButtonTypeBuilder->createSubmitButton(
            builder: $builder, labelText: $loginText, labelGlyph: $loginGlyph
        );

        if (!$this->canRegister) {
            $registrationUri = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::REGISTER->value
                ]
            );

            $registerText = $this->translator->trans('Register', [], Manager::CONTEXT);

            $buttons[] = $this->formButtonTypeBuilder->createVisualButton(
                $builder, ActionEnum::REGISTER->value, $registrationUri, $registerText,
                new FontAwesomeGlyph('user', ['me-1'], $registerText, 'fas')
            );
        }

        if (!$this->canRetrievePassword) {
            $resetPasswordUri = $this->urlGenerator->fromParameters(
                [
                    ApplicationInterface::PARAM_CONTEXT => Manager::CONTEXT,
                    ApplicationInterface::PARAM_ACTION => ActionEnum::RESET_PASSWORD->value
                ]
            );

            $resetPasswordText = $this->translator->trans('ResetPassword', [], Manager::CONTEXT);

            $buttons[] = $this->formButtonTypeBuilder->createVisualButton(
                $builder, ActionEnum::RESET_PASSWORD->value, $resetPasswordUri, $resetPasswordText,
                new FontAwesomeGlyph('question-circle', ['me-1'], $resetPasswordText, 'fas')
            );
        }

        $builder->add('buttons', ButtonsFormType::class, ['buttons' => $buttons]);
    }
}