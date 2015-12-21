<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Home\Form
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class ConfigurationForm extends FormValidator
{
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    /**
     *
     * @var \Chamilo\Core\Home\Storage\DataClass\Block
     */
    private $block;

    /**
     *
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param string $action
     */
    public function __construct(Block $block)
    {
        parent :: __construct('block', 'post', '');

        $this->block = $block;
        $this->buildForm();
        $this->setDefaults();
    }

    /**
     *
     * @return \Chamilo\Core\Home\Storage\DataClass\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    public function buildForm()
    {
        $this->addSettings();

        $this->addElement('hidden', Block :: PROPERTY_ID, $this->getBlock()->get_id());

        $buttons[] = $this->createElement(
            'style_submit_button',
            'submit',
            Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'positive'));
        $buttons[] = $this->createElement(
            'style_reset_button',
            'reset',
            Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    abstract function addSettings();

    public function build_form()
    {
        $homeblock = $this->homeblock;

        $context = $homeblock->get_context();
        $component = $homeblock->get_component();

        $homeblock_config = $this->homeblock_config;

        if (count($homeblock_config['settings']) > 0)
        {
            foreach ($homeblock_config['settings'] as $category_name => $settings)
            {
                $this->addElement('html', '<div class="configuration_form">');
                $this->addElement(
                    'html',
                    '<span class="category">' . Translation :: get(
                        (string) StringUtilities :: getInstance()->createString($category_name)->upperCamelize()) .
                         '</span>');

                foreach ($settings as $name => $setting)
                {
                    if ($setting['locked'] == 'true')
                    {
                        $this->addElement(
                            'static',
                            $name,
                            Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($name)->upperCamelize()));
                    }
                    elseif ($setting['field'] == 'text')
                    {
                        $this->add_textfield(
                            $name,
                            Translation :: get(
                                (string) StringUtilities :: getInstance()->createString($name)->upperCamelize()),
                            true);
                    }
                    else
                    {
                        $options_type = $setting['options']['type'];
                        if ($options_type == 'dynamic')
                        {
                            $options_source = $setting['options']['source'];
                            $class = $context . '\Connector';
                            $options = call_user_func(array($class, $options_source));
                        }
                        else
                        {
                            $options = $setting['options']['values'];
                        }

                        if ($setting['field'] == 'radio' || $setting['field'] == 'checkbox')
                        {
                            $group = array();
                            foreach ($options as $option_value => $option_name)
                            {
                                $group[] = & $this->createElement(
                                    $setting['field'],
                                    $name,
                                    null,
                                    Translation :: get(
                                        (string) StringUtilities :: getInstance()->createString($option_name)->upperCamelize()),
                                    $option_value);
                            }
                            $this->addGroup(
                                $group,
                                $name,
                                Translation :: get(
                                    (string) StringUtilities :: getInstance()->createString($name)->upperCamelize()),
                                '<br/>',
                                false);
                        }
                        elseif ($setting['field'] == 'select')
                        {
                            $this->addElement(
                                'select',
                                $name,
                                Translation :: get(
                                    (string) StringUtilities :: getInstance()->createString($name)->upperCamelize()),
                                $options);
                        }
                    }
                }

                $this->addElement('html', '<div style="clear: both;"></div>');
                $this->addElement('html', '</div>');
            }

            $this->addElement('hidden', Block :: PROPERTY_ID, $this->homeblock->get_id());

            // $this->addElement('submit', 'submit', Translation :: get('Ok', null, Utilities :: COMMON_LIBRARIES));
            $buttons[] = $this->createElement(
                'style_submit_button',
                'submit',
                Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES),
                array('class' => 'positive'));
            $buttons[] = $this->createElement(
                'style_reset_button',
                'reset',
                Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES),
                array('class' => 'normal empty'));

            $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        }
        else
        {
            $this->addElement('html', Translation :: get('NoConfigurableSettings'));
        }
    }

//     /**
//      * Sets default values.
//      * Traditionally, you will want to extend this method so it sets default for your learning
//      * object type's additional properties.
//      *
//      * @param array $defaults Default values for this form's parameters.
//      */
//     public function setDefaults($defaults = array ())
//     {
//         $homeblock_config = $this->homeblock_config;
//         $homeblock_current_config = $this->homeblock->get_configuration();

//         foreach ($homeblock_config['settings'] as $category_name => $settings)
//         {
//             foreach ($settings as $name => $setting)
//             {
//                 $configuration_value = $homeblock_current_config[$name];
//                 if (isset($configuration_value))
//                 {
//                     $defaults[$name] = $configuration_value;
//                 }
//                 else
//                 {
//                     $defaults[$name] = $setting['default'];
//                 }
//             }
//         }

//         parent :: setDefaults($defaults);
//     }
}
