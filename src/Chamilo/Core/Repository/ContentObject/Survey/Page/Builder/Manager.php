<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Builder;

/**
 *
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 * @author Hans De Bisschop
 */
abstract class Manager extends \Chamilo\Core\Repository\Builder\Manager
{
    // Actions
    const ACTION_MERGE_SURVEY_PAGE = 'Merger';
    const ACTION_SELECT_QUESTIONS = 'QuestionSelecter';
    const ACTION_CONFIGURE_PAGE = 'Configure';
    const ACTION_CHANGE_QUESTION_VISIBILITY = 'VisibilityChanger';
    const ACTION_CONFIGURE_QUESTION = 'ConfigureQuestion';
    const ACTION_DELETE_CONFIG = 'ConfigDeleter';
    const ACTION_UPDATE_CONFIG = 'ConfigUpdater';

    // Parameters
    const PARAM_QUESTION_ID = 'question';
    const PARAM_SURVEY_PAGE_ID = 'survey_page';
    const PARAM_COMPLEX_QUESTION_ITEM_ID = 'complex_question_item_id';
    const PARAM_CONFIG_ID = 'config_id';

    function get_configure_url($selected_cloi)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PAGE,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                self :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_cloi,
                self :: PARAM_SURVEY_PAGE_ID => $selected_cloi->get_ref()));
    }

    function get_config_delete_url($config_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_CONFIG,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                self :: PARAM_CONFIG_ID => $config_id));
    }

    function get_config_update_url($config_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_UPDATE_CONFIG,
                self :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
                self :: PARAM_CONFIG_ID => $config_id));
    }

    function get_change_question_visibility_url($complex_question_item)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_CHANGE_QUESTION_VISIBILITY,
                self :: PARAM_COMPLEX_QUESTION_ITEM_ID => $complex_question_item->get_id()));
    }

    function get_configure_question_url($complex_question_item)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_CONFIGURE_QUESTION,
                self :: PARAM_COMPLEX_QUESTION_ITEM_ID => $complex_question_item->get_id()));
    }
}
