<?php
namespace Chamilo\Core\Home\Form;

use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\BlockConfiguration;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: home_block_config_form.class.php 227 2009-11-13 14:45:05Z kariboe $
 * 
 * @package home.lib.forms
 */
class BlockConfigurationForm extends FormValidator
{
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';

    /**
     *
     * @var Block
     */
    private $homeblock;

    /**
     *
     * @var multitype
     */
    private $homeblock_config;

    /**
     *
     * @param Block $homeblock
     * @param string $action
     */
    public function __construct($homeblock, $action)
    {
        parent :: __construct('home_block', 'post', $action);
        
        $this->homeblock = $homeblock;
        $this->homeblock_config = $homeblock->parse_settings();
        $this->build_form();
        $this->setDefaults();
    }

    public function build_form()
    {
        $homeblock = $this->homeblock;
        
        $context = $homeblock->get_context();
        $component = $homeblock->get_component();
        
        $homeblock_config = $this->homeblock_config;
        
        if (count($homeblock_config['settings']) > 0)
        {
            $connector_path = Path :: getInstance()->namespaceToFullPath($context) . '/php/lib/connector.class.php';
            
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

    public function update_block_config()
    {
        $values = $this->exportValues();
        $homeblock = $this->homeblock;
        $homeblock_config = $this->homeblock_config;
        
        $problems = 0;
        
        foreach ($homeblock_config['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                if ($setting['locked'] != 'true')
                {
                    $block_config = new BlockConfiguration();
                    $block_config->set_block_id($homeblock->get_id());
                    $block_config->set_variable($name);
                    $block_config->set_value($values[$name]);
                    
                    if (! $block_config->update())
                    {
                        $problems ++;
                    }
                }
            }
        }
        
        if ($problems > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Sets default values.
     * Traditionally, you will want to extend this method so it sets default for your learning
     * object type's additional properties.
     * 
     * @param array $defaults Default values for this form's parameters.
     */
    public function setDefaults($defaults = array ())
    {
        $homeblock_config = $this->homeblock_config;
        $homeblock_current_config = $this->homeblock->get_configuration();
        
        foreach ($homeblock_config['settings'] as $category_name => $settings)
        {
            foreach ($settings as $name => $setting)
            {
                $configuration_value = $homeblock_current_config[$name];
                if (isset($configuration_value))
                {
                    $defaults[$name] = $configuration_value;
                }
                else
                {
                    $defaults[$name] = $setting['default'];
                }
            }
        }
        
        parent :: setDefaults($defaults);
    }
}
