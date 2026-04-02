<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PictureFormType extends AbstractType
{
    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['noPictureLabel'] = $options['noPictureLabel'];

        if (count($options['pictureStyles']) > 0) {
            $view->vars['pictureStyles'] = $options['pictureStyles'];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $translator = $this->getTranslator();

        $resolver->setDefaults([
            'compound' => false,
            'label' => $translator->trans('Picture', [], StringUtilities::LIBRARIES),
            'label_html' => true,
            'noPictureLabel' => $translator->trans('noPicture', [], StringUtilities::LIBRARIES),
            'pictureStyles' => []
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'picture';
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}