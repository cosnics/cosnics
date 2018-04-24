<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Form;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Score;
use Chamilo\Libraries\Format\Form\FormType\PercentInputFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * Class AddFeedbackFormType
 * @package Chamilo\Core\Repository\Feedback\Form
 */
class ScoreFormType extends AbstractType
{

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * ScoreFormType constructor.
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
            Score::PROPERTY_SCORE,
            PercentInputFormType::class/*,
            [
                'constraints' => new LessThanOrEqual(
                    [
                        "value" => 100,
                        'message' => $this->translator->trans('LessThanOrEqual', ['{VALUE}' => 100 ], 'Chamilo\Libraries')
                    ]
                ),
            ]
   */

        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'score';
    }
}