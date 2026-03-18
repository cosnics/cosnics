<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class HtmlEditorFormType extends TextareaType
{
    protected ResourceManager $resourceManager;

    protected Translator $translator;

    protected WebPathBuilder $webPathBuilder;

    public function __construct(ResourceManager $resourceManager, WebPathBuilder $webPathBuilder, Translator $translator
    )
    {
        $this->resourceManager = $resourceManager;
        $this->webPathBuilder = $webPathBuilder;
        $this->translator = $translator;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['javascriptSourceCode'] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getPluginPath(StringUtilities::LIBRARIES) . 'TinyMCE/tinymce.min.js'
        );

        $view->vars['height'] = $options['height'];
        $view->vars['max_height'] = $options['max_height'];
        $view->vars['plugins'] = $options['plugins'];
        $view->vars['toolbar'] = $options['toolbar'];
        $view->vars['statusbar'] = $options['statusbar'];

        $view->vars['language'] = $options['language'];
        $view->vars['languagePath'] =
            $this->getWebPathBuilder()->getPluginPath(StringUtilities::LIBRARIES) . 'TinyMCE/langs/' .
            $options['language'] . '.js';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label_html' => true,
            'height' => 250,
            'max_height' => 500,
            'language' => $this->getTranslator()->getLocale(),
            'plugins' => [
                'advlist',
                'autolink',
                'lists',
                'link',
                'charmap',
                'preview',
                'anchor',
                'searchreplace',
                'visualblocks',
                'code',
                'fullscreen',
                'insertdatetime',
                'table',
                'help',
                'wordcount'
            ],
            'toolbar' => 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            'statusbar' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'html_editor';
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    protected function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }
}