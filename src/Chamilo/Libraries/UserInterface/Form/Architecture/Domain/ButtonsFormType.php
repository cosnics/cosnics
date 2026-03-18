<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ButtonBuilder;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ButtonsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['buttons'] as $button) {
            $builder->add($button);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'buttons' => [],
        ]);

        $resolver->setNormalizer('buttons', static function (Options $options, $buttons) {
            if (!is_array($buttons)) {
                throw new LogicException('Buttons should be an array.');
            }

            foreach ($buttons as $button) {
                if (!$button instanceof ButtonBuilder) {
                    throw new LogicException(
                        'Buttons should be either a ButtonTypeInterface or a SubmitButtonTypeInterface.'
                    );
                }
            }

            return $buttons;
        });
    }
}