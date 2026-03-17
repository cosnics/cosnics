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
class HtmlEditorType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['javascript'] = $options['javascript'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'javascript' => ''
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'html_editor';
    }
}