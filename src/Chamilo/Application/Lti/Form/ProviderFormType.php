<?php

namespace Chamilo\Application\Lti\Form;

use Chamilo\Application\Lti\Manager;
use Chamilo\Application\Lti\Storage\Entity\Provider;
use Chamilo\Application\Lti\Storage\Entity\ProviderCustomParameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @package Chamilo\Application\Lti\Form
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ProviderFormType extends AbstractType
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
                'label' => $this->translator->trans('ProviderName', [], Manager::context()),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('NotBlank', [], 'Chamilo\Libraries')
                    ])
                ]
            ]
        );

        $builder->add(
            'launchUrl',
            TextType::class,
            [
                'required' => true,
                'label' => $this->translator->trans('ProviderUrl', [], Manager::context()),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('NotBlank', [], 'Chamilo\Libraries')
                    ])
                ]
            ]
        );

        $builder->add(
            'key',
            TextType::class,
            [
                'required' => false,
                'label' => $this->translator->trans('ConsumerKey', [], Manager::context())
            ]
        );

        $builder->add(
            'secret',
            TextType::class,
            [
                'required' => false,
                'label' => $this->translator->trans('ConsumerSecret', [], Manager::context())
            ]
        );

        $builder->add(
            'customParameters',
            CollectionType::class,
            [
                'entry_type' => ProviderCustomParameterFormType::class,
                'allow_add' => true, 'allow_delete' => true, 'by_reference' => false
            ]
        );
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Provider::class
        ]);
    }

}