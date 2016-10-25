<?php
namespace Chamilo\Application\Survey\Storage\DataClass;

use Chamilo\Libraries\Storage\DataClass\DataClass;

class SelectQuestionAnswer extends DataClass
{
    const PROPERTY_SURVEY_PARTICIPANT_ID = 'survey_participant_id';
    const PROPERTY_CONTEXT_ID = 'context_id';
    const PROPERTY_COMPLEX_QUESTION_ID = 'complex_question_id';
    const PROPERTY_OPTION_ID = 'option_id';
    const PROPERTY_CONTEXT_PATH = 'context_path';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_CONTEXT_TEMPLATE_ID = 'context_template_id';

    /**
     * Inherited
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(
                self :: PROPERTY_SURVEY_PARTICIPANT_ID,
                self :: PROPERTY_COMPLEX_QUESTION_ID,
                self :: PROPERTY_OPTION_ID,
                self :: PROPERTY_CONTEXT_PATH,
                self :: PROPERTY_PUBLICATION_ID,
                self :: PROPERTY_USER_ID,
                self :: PROPERTY_CONTEXT_TEMPLATE_ID,
                self :: PROPERTY_CONTEXT_ID));
    }

    function get_survey_participant_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_PARTICIPANT_ID);
    }

    function set_survey_participant_id($survey_participant_id)
    {
        $this->set_default_property(self :: PROPERTY_SURVEY_PARTICIPANT_ID, $survey_participant_id);
    }

    function get_context_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_ID);
    }

    function set_context_id($context_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_ID, $context_id);
    }

    function get_context_path()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_PATH);
    }

    function set_context_path($context_path)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_PATH, $context_path);
    }

    function get_complex_question_id()
    {
        return $this->get_default_property(self :: PROPERTY_COMPLEX_QUESTION_ID);
    }

    function set_complex_question_id($complex_question_id)
    {
        $this->set_default_property(self :: PROPERTY_COMPLEX_QUESTION_ID, $complex_question_id);
    }

    function get_option_id()
    {
        return $this->get_default_property(self :: PROPERTY_OPTION_ID);
    }

    function set_option_id($option_id)
    {
        $this->set_default_property(self :: PROPERTY_OPTION_ID, $option_id);
    }

    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_context_template_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    }

    function set_context_template_id($context_template_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID, $context_template_id);
    }
}
?>