<?php

namespace Chamilo\Application\Lti\Form;

use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Storage\Entity\Provider;
use Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @package Chamilo\Application\Lti\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProviderCustomParameterFormType extends AbstractType
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * AddFeedbackFormType constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'required' => true,
                'label' => $this->translator->trans('ProviderParameterName', [], Manager::context()),
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => $this->translator->trans('NotBlank', [], 'Chamilo\Libraries')
                        ]
                    )
                ]
            ]
        );

        $builder->add(
            'value',
            TextType::class,
            [
                'required' => true,
                'label' => $this->translator->trans('ProviderParameterValue', [], Manager::context()),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('NotBlank', [], 'Chamilo\Libraries')
                    ])
                ]
            ]
        );

    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProviderCustomParameter::class
        ]);
    }

}