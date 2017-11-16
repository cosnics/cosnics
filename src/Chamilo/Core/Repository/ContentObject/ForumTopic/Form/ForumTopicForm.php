<?php
namespace Chamilo\Core\Repository\ContentObject\ForumTopic\Form;

use Chamilo\Core\Repository\ContentObject\ForumTopic\Storage\DataClass\ForumTopic;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.content_object.forum_topic
 */
class ForumTopicForm extends ContentObjectForm
{

    /**
     * Creates a new ForumTopic.
     *
     * @return ForumTopic
     */
    public function create_content_object()
    {
        $object = new ForumTopic();
        $object->set_locked(false);
        $this->set_content_object($object);

        return parent::create_content_object();
    }

    /**
     * Updates a ForumTopic.
     *
     * @return ForumTopic
     */
    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_locked($this->exportValue(ForumTopic::PROPERTY_LOCKED));

        return parent::update_content_object();
    }

    /**
     * Build the creation form to create a ForumTopic.
     *
     * @param type $default_content_object
     */
    public function build_creation_form($default_content_object = null)
    {
        parent::build_creation_form();
    }

    /**
     * Build the editing form to edit a ForumTopic.
     *
     * @param type $object
     */
    public function build_editing_form($object)
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties', null, Utilities::COMMON_LIBRARIES));
        $this->addElement(
            'checkbox',
            'locked',
            Translation::get('Locked', null, 'Chamilo\Core\Repository\ContentObject\Forum'));
        $this->addElement('category');
    }

    public function setDefaults($defaults = array())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[ForumTopic::PROPERTY_LOCKED] = $object->get_locked();
        }
        parent::setDefaults($defaults);
    }
}
