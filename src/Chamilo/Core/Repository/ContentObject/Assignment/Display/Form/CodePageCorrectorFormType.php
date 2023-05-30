<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Form;

use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Libraries\Format\Form\FormType\HtmlEditorFormType;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AddFeedbackFormType
 * @package Chamilo\Core\Repository\Feedback\Form
 */
class CodePageCorrectorFormType extends AbstractType
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
            Page::PROPERTY_DESCRIPTION,
            HtmlEditorFormType::class,
            [
                'html_editor_options' => [
                    'collapse_toolbar' => false
                ],
                'required' => true,
                'label' => 'Inhoud ',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('NotBlank', [], StringUtilities::LIBRARIES)
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
        return 'code_page_corrector';
    }
}