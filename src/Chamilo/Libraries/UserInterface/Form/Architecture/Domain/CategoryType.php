<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CategoryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'mapped' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'category';
    }
}