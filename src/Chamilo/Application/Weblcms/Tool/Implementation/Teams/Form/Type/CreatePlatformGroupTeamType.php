<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type;

use Chamilo\Libraries\Format\Validator\Constraint\Length;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreatePlatformGroupTeamType extends PlatformGroupTeamType
{
    const ELEMENT_TYPE = 'type';

    const TYPE_STANDARD = 1;
    const TYPE_CLASS = 2;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            self::ELEMENT_TYPE, ChoiceType::class,
            [
                'label' => $this->translator->trans('PlatformGroupTeamType', [], self::TRANSLATION_CONTEXT),
                'choices' => [
                    $this->translator->trans('ClassTeam', [], self::TRANSLATION_CONTEXT) => self::TYPE_CLASS,
                    $this->translator->trans('StandardTeam', [], self::TRANSLATION_CONTEXT) => self::TYPE_STANDARD
                ],
                'expanded' => true,
                'data' => self::TYPE_CLASS
            ]
        );
    }
}
