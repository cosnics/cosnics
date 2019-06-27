<?php

namespace Chamilo\Core\User\Form\Type;

use Chamilo\Libraries\Format\Validator\Constraint\Length;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Chamilo\Core\User\Form\Type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class InviteFormType extends AbstractType
{
    const ELEMENT_EMAIL = 'email';
    const ELEMENT_PERSONAL_MESSAGE = 'personal_message';

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
            self::ELEMENT_EMAIL, EmailType::class,
            [
                'attr' => [
                    'placeholder' => $this->translator->trans('Email', [], self::TRANSLATION_CONTEXT),
                    'autocomplete' => 'new-password',
                    'minlength' => 3
                ],
                'constraints' => new Length(['min' => 3])
            ]
        );

        $builder->add(
            self::ELEMENT_PERSONAL_MESSAGE, TextareaType::class,
            [
                'attr' => [
                    'placeholder' => $this->translator->trans('PersonalMessage', [], self::TRANSLATION_CONTEXT),
                    'rows' => 5
                ],
                'required' => false
            ]
        );
    }

}