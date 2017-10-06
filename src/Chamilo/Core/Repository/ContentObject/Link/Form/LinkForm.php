<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Form;

use Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package repository.lib.content_object.link
 */
class LinkForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(Link::PROPERTY_URL, Translation::get('URL'), true, array('size' => '100'));
        $this->addElement('checkbox', Link::PROPERTY_SHOW_IN_IFRAME, Translation::get('ShowInIFrame'));
        $this->addElement('category');

        $this->addFormRule(array($this, 'check_https_compliance'));
    }

    protected function build_editing_form()
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(Link::PROPERTY_URL, Translation::get('URL'), true, array('size' => '100'));
        $this->addElement('checkbox', Link::PROPERTY_SHOW_IN_IFRAME, Translation::get('ShowInIFrame'));
        $this->addElement('category');

        $this->addFormRule(array($this, 'check_https_compliance'));
    }

    public function setDefaults($defaults = array())
    {
        $co = $this->get_content_object();
        $co_id = $co->get_id();
        if (isset($co_id))
        {
            $defaults[Link::PROPERTY_URL] = $co->get_url();
            $defaults[Link::PROPERTY_SHOW_IN_IFRAME] = $co->get_show_in_iframe();
        }
        else
        {
            $defaults[Link::PROPERTY_URL] = 'http://';
            $defaults[Link::PROPERTY_SHOW_IN_IFRAME] = false;
        }
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $object = new Link();

        // TODO: Cleaner and more generalized solution to check URL validity
        $url = $this->exportValue(Link::PROPERTY_URL);
        $url = str_replace('http://http://', 'http://', $url);

        $object->set_url($url);
        $object->set_show_in_iframe($this->exportValue(Link::PROPERTY_SHOW_IN_IFRAME));
        $this->set_content_object($object);

        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();

        // TODO: Cleaner and more generalized solution to check URL validity
        $url = $this->exportValue(Link::PROPERTY_URL);
        $url = str_replace('http://http://', 'http://', $url);

        $object->set_url($url);
        $object->set_show_in_iframe($this->exportValue(Link::PROPERTY_SHOW_IN_IFRAME));

        return parent::update_content_object();
    }

    /**
     * Checks if the link is compliant with HTTPS and can be shown in iframe
     *
     * @param $fields
     * @return bool
     */
    protected function check_https_compliance($fields)
    {
        $errors = array();

        if (! isset($_SERVER['HTTPS']))
        {
            return true;
        }

        $link = $fields[Link::PROPERTY_URL];
        $show_in_iframe = $fields[LINK::PROPERTY_SHOW_IN_IFRAME];

        if (strpos(strtolower($link), 'https') !== 0 && $show_in_iframe)
        {
            $errors['show_in_iframe'] = Translation::get('IFrameNotAllowed');
        }

        if (count($errors) == 0)
        {
            return true;
        }

        return $errors;
    }
}
