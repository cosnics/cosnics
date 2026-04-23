<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Domain;

use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\Service\Resource\ResourceManager;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Form\Service\ElementFinderDataTransformer;
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

    public function __construct(
        protected ResourceManager $resourceManager, protected WebPathBuilder $webPathBuilder,
        protected Translator $translator
    )
    {
    }

    /**
     * Adds the configuration json to the form view
     */
    protected function addConfigurationJson(FormView $view, array $options): void
    {
        $configurationJson = '';
        foreach ($options['elementFinderConfiguration'] as $name => $value) {
            $configurationJson .= ' ' . $name . ': ' . $value . ', ';
        }

        $view->vars['configurationJson'] = substr($configurationJson, 0, strlen($configurationJson) - 2);
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
         * @var \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\AdvancedElementFinder\AdvancedElementFinderElementTypes $elementTypes
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
        $view->vars['translations'] = [
            'show' => $this->translator->trans('Show', [], StringUtilities::LIBRARIES),
            'hide' => $this->translator->trans('Hide', [], StringUtilities::LIBRARIES),
            'add' => $this->translator->trans('AddToSelection', [], StringUtilities::LIBRARIES),
            'remove' => $this->translator->trans('RemoveFromSelection', [], StringUtilities::LIBRARIES),
            'selectElementType' => $this->translator->trans('SelectElementType', [], StringUtilities::LIBRARIES)
        ];

        $view->vars['height'] = $options['height'];
        $view->vars['collapsed'] = $options['collapsed'];

        $view->vars['elementFinderPlugin'] = $this->resourceManager->getResourceHtml(
            $this->webPathBuilder->getJavascriptPath() . 'Jquery/jquery.advelementfinder.js'
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
            ['\Chamilo\Libraries\UserInterface\Form\Architecture\Domain\AdvancedElementFinder\AdvancedElementFinderElementTypes']
        );
    }

    public function getBlockPrefix(): string
    {
        return 'element_finder';
    }
}