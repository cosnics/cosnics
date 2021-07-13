<?php
namespace Chamilo\Core\Repository\ContentObject\Presence\Form;

use Chamilo\Core\Repository\ContentObject\Presence\Storage\DataClass\Presence;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;

/**
 *
 * @package repository.lib.content_object.presence
 */
/**
 * This class represents a form to create or update presences
 */
class PresenceForm extends ContentObjectForm
{

    // Inherited
    public function create_content_object()
    {
        $object = new Presence();
        $this->set_content_object($object);
        return parent::create_content_object();
    }

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($this->getHtmlEditorOptions(), $in_tab);
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_editing_form($this->getHtmlEditorOptions(), $in_tab);
    }

    protected function getHtmlEditorOptions()
    {
        $htmleditor_options = array();

        $htmleditor_options[FormValidatorHtmlEditorOptions::OPTION_HEIGHT] = '500';

        return $htmleditor_options;
    }
}
