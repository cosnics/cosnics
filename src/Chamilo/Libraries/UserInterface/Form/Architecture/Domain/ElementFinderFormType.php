<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Format\Form\DataTransformer\ElementFinderDataTransformer;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Translator;

/**
 * Javascript based element finder form type
 *
 * @package Chamilo\Libraries\Format\Form\FormType
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ElementFinderFormType extends AbstractType
{
    public const int DEFAULT_HEIGHT = 300;
    public const int DEFAULT_WIDTH = 292;

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

    /**
     * Adds the configuration json to the form view
     */
    protected function addConfigurationJson(FormView $view, array $options): void
    {
        $configuration_json = '';
        foreach ($options['elementFinderConfiguration'] as $name => $value) {
            $configuration_json .= ' ' . $name . ': ' . $value . ', ';
        }
        $configuration_json = substr($configuration_json, 0, strlen($configuration_json) - 2);

        $view->vars['configurationJson'] = $configuration_json;
    }

    /**
     * Adds the element types to the form view
     *
     * @param \Symfony\Component\Form\FormView $view
     * @param string[] $options
     *
     * @throws \InvalidArgumentException
     */
    protected function addElementTypes(FormView $view, array $options): void
    {
        /**
         * @var \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes $elementTypes
         */
        $elementTypes = $options['elementTypes'];
        $elementTypesArray = [];

        foreach ($elementTypes->getTypes() as $elementType) {
            $elementTypesArray[$elementType->getId()] = $elementType->getName();
        }

        $view->vars['elementTypesSelector'] = $elementTypesArray;
        $view->vars['elementTypes'] = json_encode($elementTypes->asArray());
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ElementFinderDataTransformer());
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $translator = $this->getTranslator();

        $view->vars['translations'] = [
            'show' => $translator->trans('Show', [], StringUtilities::LIBRARIES),
            'hide' => $translator->trans('Hide', [], StringUtilities::LIBRARIES),
            'selectElementType' => $translator->trans('SelectElementType', [], StringUtilities::LIBRARIES)
        ];

        $view->vars['height'] = $options['height'];
        $view->vars['width'] = $options['width'];
        $view->vars['collapsed'] = $options['collapsed'];

        $view->vars['elementFinderPlugin'] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath() . 'Jquery/jquery.advelementfinder.js'
        );

        $this->addElementTypes($view, $options);
        $this->addConfigurationJson($view, $options);
    }

    /**
     *
     * @see \Symfony\Component\Form\AbstractType::setDefaultOptions()
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'height' => self::DEFAULT_HEIGHT,
                'width' => self::DEFAULT_WIDTH,
                'collapsed' => false,
                'elementTypes' => null,
                'elementFinderConfiguration' => [],
                'compound' => false,
                'data_class' => null
            ]
        );

        $resolver->setRequired(['elementTypes']);

        $resolver->setAllowedTypes(
            'elementTypes',
            ['\Chamilo\Libraries\UserInterface\Form\Architecture\Domain\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes']
        );
    }

    public function getBlockPrefix(): string
    {
        return 'element_finder';
    }

    public function getResourceManager(): ResourceManager
    {
        return $this->resourceManager;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }
}