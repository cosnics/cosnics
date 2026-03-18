<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Chamilo\Libraries\UserInterface\Alert\Architecture\Enum\AlertEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MessageFormType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['messageType'] = $options['messageType']->value;
        $view->vars['message'] = $options['message'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'mapped' => false,
            'message' => '',
            'messageType' => AlertEnum::INFO
        ]);

        $resolver->setNormalizer('messageType', static function (Options $options, $messageType) {
            if (!$messageType instanceof AlertEnum) {
                throw new LogicException('messageType should be an AlertEnum.');
            }

            return $messageType;
        });
    }

    public function getBlockPrefix(): string
    {
        return 'message';
    }
}