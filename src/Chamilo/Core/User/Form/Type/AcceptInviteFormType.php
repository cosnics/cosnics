<?php

namespace Chamilo\Core\User\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Chamilo\Core\User\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AcceptInviteFormType extends \Symfony\Component\Form\AbstractType
{
    const ELEMENT_FIRST_NAME = 'first_name';
    const ELEMENT_LAST_NAME = 'last_name';
    const ELEMENT_EMAIL = 'email';
    const ELEMENT_PASSWORD = 'password';

    const TRANSLATION_CONTEXT = 'Chamilo\Core\User';

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * AcceptInviteFormType constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(\Symfony\Component\Translation\Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            self::ELEMENT_FIRST_NAME, TextType::class,
            ['attr' => ['placeholder' => $this->translator->trans('FirstName', [], self::TRANSLATION_CONTEXT)]]
        );

        $builder->add(
            self::ELEMENT_LAST_NAME, TextType::class,
            ['attr' => ['placeholder' => $this->translator->trans('LastName', [], self::TRANSLATION_CONTEXT)]]
        );

        $builder->add(
            self::ELEMENT_EMAIL, TextType::class,
            [
                'attr' => [
                    'placeholder' => $this->translator->trans('Email', [], self::TRANSLATION_CONTEXT),
                    'autocomplete' => 'new-password'
                ]
            ]
        );

        $builder->add(
            self::ELEMENT_PASSWORD, RepeatedType::class,
            [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'placeholder' => $this->translator->trans(
                            'Password', [], self::TRANSLATION_CONTEXT
                        ),
                        'autocomplete' => 'new-password'
                    ]
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => $this->translator->trans(
                            'PasswordRepeat', [], self::TRANSLATION_CONTEXT
                        ),
                        'autocomplete' => 'new-password'
                    ]
                ]
            ]
        );
    }
}