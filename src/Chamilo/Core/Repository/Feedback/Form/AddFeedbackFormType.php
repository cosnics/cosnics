<?php
namespace Chamilo\Core\Repository\Feedback\Form;

use Chamilo\Core\Repository\Feedback\Storage\DataClass\Feedback;
use Chamilo\Libraries\Format\Form\FormType\HtmlEditorFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AddFeedbackFormType
 * @package Chamilo\Core\Repository\Feedback\Form
 */
class AddFeedbackFormType extends AbstractType
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
            Feedback::PROPERTY_COMMENT,
            HtmlEditorFormType::class,
            [
                'html_editor_options' => [
                    'collapse_toolbar' => true,
                    'height' => 120,
                    'resize_enabled' => false,
                    'render_resource_inline' => false
                ],
                'required' => true,
                'label' => 'Voeg feedback toe: ',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('NotBlank', [], 'Chamilo\Libraries')
                    ])
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getBlockPrefix()
    {
        return 'add_feedback';
    }
}