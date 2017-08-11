<?php

namespace Chamilo\Libraries\Format\Form\FormType;

use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Form\DataTransformer\ElementFinderDataTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Javascript based element finder form type
 *
 * @package common\libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ElementFinderFormType extends AbstractType
{
    const DEFAULT_HEIGHT = 300;
    const DEFAULT_WIDTH = 292;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ElementFinderDataTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'height' => self::DEFAULT_HEIGHT,
                'width' => self::DEFAULT_WIDTH,
                'collapsed' => false,
                'element_types' => null,
                'element_finder_configuration' => array(),
                'compound' => false,
                'data_class' => null,

            )
        );

        $resolver->setRequired(
            array(
                'element_types'
            )
        );

        $resolver->setAllowedTypes(
            array(
                'element_types' => array(
                    '\Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElementTypes'
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['translations'] = array(
            'show' => Translation::get('Show'),
            'hide' => Translation::get('Hide'),
            'select_element_type' => Translation::get('SelectElementType')
        );

        $view->vars['height'] = $options['height'];
        $view->vars['width'] = $options['width'];
        $view->vars['collapsed'] = $options['collapsed'];

        $view->vars['element_finder_plugin'] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getPluginPath(null, true) . 'jquery/jquery.advelementfinder.js'
        );

        $this->add_element_types($view, $options);
        $this->add_configuration_json($view, $options);
    }

    /**
     * Adds the element types to the form view
     *
     * @param FormView $view
     * @param array $options
     *
     * @throws \InvalidArgumentException
     */
    protected function add_element_types(FormView $view, array $options)
    {
        $element_types = $options['element_types'];
        $element_types_array = array();

        foreach ($element_types->get_types() as $element_type)
        {
            $element_types_array[$element_type->get_id()] = $element_type->get_name();
        }

        $view->vars['element_types_selector'] = $element_types_array;
        $view->vars['element_types'] = json_encode($element_types->as_array());
    }

    /**
     * Adds the configuration json to the form view
     *
     * @param FormView $view
     * @param array $options
     */
    protected function add_configuration_json(FormView $view, array $options)
    {
        $configuration_json = '';
        foreach ($options['element_finder_configuration'] as $name => $value)
        {
            $configuration_json .= ' ' . $name . ': ' . $value . ', ';
        }
        $configuration_json = substr($configuration_json, 0, strlen($configuration_json) - 2);

        $view->vars['configuration_json'] = $configuration_json;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'element_finder';
    }

}