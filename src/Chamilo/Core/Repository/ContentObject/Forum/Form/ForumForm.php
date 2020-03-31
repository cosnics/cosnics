<?php
namespace Chamilo\Core\Repository\ContentObject\Forum\Form;

use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataClass\Forum;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.content_object.forum
 */
class ForumForm extends ContentObjectForm
{

    public function create_content_object()
    {
        $object = new Forum();
        $object->set_locked(false);
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_locked($this->exportValue(Forum::PROPERTY_LOCKED));
        // $this->set_content_object($object);
        return parent::update_content_object();
    }

    public function build_editing_form()
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties', null, Utilities::COMMON_LIBRARIES));
        $this->addElement('checkbox', 'locked', Translation::get('ForumLocked'));
    }

    public function setDefaults($defaults = array())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[Forum::PROPERTY_LOCKED] = $object->get_locked();
        }
        parent::setDefaults($defaults);
    }
}
