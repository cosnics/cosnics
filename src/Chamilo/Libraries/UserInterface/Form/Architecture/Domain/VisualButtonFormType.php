<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class VisualButtonFormType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['uri'] = $options['uri'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'mapped' => false,
            'uri' => '',
        ]);

        $resolver->setAllowedTypes('uri', ['string']);
    }

    public function getBlockPrefix(): string
    {
        return 'visual_button';
    }
}