<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use InvalidArgumentException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LayoutRowFormType extends AbstractType
{
    protected const array ALLOWED_TYPES = [
        LayoutColumnFormType::class
    ];

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['html'] = $options['html'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
            'inherit_data' => true
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        foreach ($form->all() as $child) {
            $config = $child->getConfig();
            $type = $config->getType()->getInnerType();
            $typeClass = get_class($type);

            if (!in_array($typeClass, self::ALLOWED_TYPES, true)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Form type "%s" (field "%s") is not allowed in %s. Allowed types: %s', $typeClass,
                        $child->getName(), static::class, implode(', ', self::ALLOWED_TYPES)
                    )
                );
            }
        }
    }

    public function getBlockPrefix(): string
    {
        return 'layout_row';
    }
}