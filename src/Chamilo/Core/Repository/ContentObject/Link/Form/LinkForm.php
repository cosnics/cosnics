<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Form;

use Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;

/**
 * $Id: link_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.content_object.link
 */
class LinkForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->add_textfield(Link :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->addElement('checkbox', Link :: PROPERTY_SHOW_IN_IFRAME, Translation :: get('ShowInIFrame'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->add_textfield(Link :: PROPERTY_URL, Translation :: get('URL'), true, array('size' => '100'));
        $this->addElement('checkbox', Link :: PROPERTY_SHOW_IN_IFRAME, Translation :: get('ShowInIFrame'));
        $this->addElement('category');
    }

    public function setDefaults($defaults = array ())
    {
        $co = $this->get_content_object();
        $co_id = $co->get_id();
        if (isset($co_id))
        {
            $defaults[Link :: PROPERTY_URL] = $co->get_url();
            $defaults[Link :: PROPERTY_SHOW_IN_IFRAME] = $co->get_show_in_iframe();
        }
        else
        {
            $defaults[Link :: PROPERTY_URL] = 'http://';
            $defaults[Link :: PROPERTY_SHOW_IN_IFRAME] = false;
        }
        parent :: setDefaults($defaults);
    }

    public function create_content_object()
    {
        $object = new Link();
        
        // TODO: Cleaner and more generalized solution to check URL validity
        $url = $this->exportValue(Link :: PROPERTY_URL);
        $url = str_replace('http://http://', 'http://', $url);
        
        $object->set_url($url);
        $object->set_show_in_iframe($this->exportValue(Link :: PROPERTY_SHOW_IN_IFRAME));
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        
        // TODO: Cleaner and more generalized solution to check URL validity
        $url = $this->exportValue(Link :: PROPERTY_URL);
        $url = str_replace('http://http://', 'http://', $url);
        
        $object->set_url($url);
        $object->set_show_in_iframe($this->exportValue(Link :: PROPERTY_SHOW_IN_IFRAME));
        return parent :: update_content_object();
    }
}
