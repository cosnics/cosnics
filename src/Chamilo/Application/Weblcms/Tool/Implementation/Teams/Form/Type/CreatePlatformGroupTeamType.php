<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type;

use Chamilo\Libraries\Format\Validator\Constraint\Length;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Teams\Form\Type
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreatePlatformGroupTeamType extends AbstractType
{
    const ELEMENT_NAME = 'name';
    const ELEMENT_PLATFORM_GROUPS = 'platform_groups';

    const TRANSLATION_CONTEXT = 'Chamilo\Application\Weblcms\Tool\Implementation\Teams';

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
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
            self::ELEMENT_NAME, TextType::class,
            [
                'label' => $this->translator->trans('PlatformGroupTeamName', [], self::TRANSLATION_CONTEXT),
                'attr' => [
                    'minlength' => 3
                ],
                'constraints' => new Length(['min' => 3]),
            ]
        );

        $builder->add(
            self::ELEMENT_PLATFORM_GROUPS, HiddenType::class,
            [
                'constraints' => new Callback([$this, 'validatePlatformGroups'])
            ]
        );
    }

    /**
     * @param string $value
     * @param \Symfony\Component\Validator\Context\ExecutionContextInterface $context
     */
    public function validatePlatformGroups($value, ExecutionContextInterface $context)
    {
        if (empty($value))
        {
            $valid = false;
        }
        else
        {
            $platformGroups = json_decode($value);
            $valid = is_array($platformGroups) && count($platformGroups) > 0;
        }

        if (!$valid)
        {
            $context->buildViolation($this->translator->trans('InvalidPlatformGroups', [], self::TRANSLATION_CONTEXT))
                ->addViolation();
        }
    }
}