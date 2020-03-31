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
     * Build the creation form to create a ForumTopic.
     *
     * @param array $htmleditor_options
     * @param bool $in_tab
     */
    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form();
    }

    /**
     * Build the editing form to edit a ForumTopic.
     *
     * @param array $htmleditor_options
     * @param bool $in_tab
     *
     * @throws \HTML_QuickForm_Error
     */
    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($htmleditor_options, $in_tab);
        $this->addElement('category', Translation::get('Properties', null, Utilities::COMMON_LIBRARIES));
        $this->addElement(
            'checkbox', 'locked', Translation::get('Locked', null, 'Chamilo\Core\Repository\ContentObject\Forum')
        );
    }

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

    public function setDefaults($defaults = array())
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[ForumTopic::PROPERTY_LOCKED] = $object->get_locked();
        }
        parent::setDefaults($defaults);
    }

    /**
     * Updates a ForumTopic.
     *
     * @return ForumTopic
     * @throws \Exception
     */
    public function update_content_object()
    {
        $object = $this->get_content_object();
        $object->set_locked($this->exportValue(ForumTopic::PROPERTY_LOCKED));

        return parent::update_content_object();
    }
}
