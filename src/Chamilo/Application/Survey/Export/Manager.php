<?php
namespace Chamilo\Application\Survey\Export;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'action';
    const PARAM_EXPORT_REGISTRATION_ID = 'export_registration_id';
    const PARAM_EXPORT_TEMPLATE_ID = 'export_template_id';
    const PARAM_EXPORT_ID = 'export_id';
    const ACTION_BROWSE = 'Browser';
    const ACTION_CREATE = 'Creator';
    const ACTION_EDIT = 'Editor';
    const ACTION_EDIT_EXPORT_RIGHTS = 'RightsEditor';
    const ACTION_DELETE = 'Deleter';
    const ACTION_EXPORT = 'Export';
    const ACTION_DELETE_EXPORT = 'ExportDeleter';
    const ACTION_CONVERT_ANSWERS = 'ConvertAnswers';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    // url
    function get_browse_export_templates_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function get_export_url($export_template)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EXPORT,
                self :: PARAM_EXPORT_TEMPLATE_ID => $export_template->get_id()));
    }

    function get_export_template_create_url($export_registration)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_CREATE,
                self :: PARAM_EXPORT_REGISTRATION_ID => $export_registration->get_id()));
    }

    function get_export_template_delete_url($export_template)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE,
                self :: PARAM_EXPORT_TEMPLATE_ID => $export_template->get_id()));
    }

    function get_export_template_edit_url($export_template)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT,
                self :: PARAM_EXPORT_TEMPLATE_ID => $export_template->get_id()));
    }

    function get_export_template_rights_editor_url($export_template)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_EXPORT_RIGHTS,
                self :: PARAM_EXPORT_TEMPLATE_ID => $export_template->get_id()));
    }

    function get_export_tracker_delete_url($export_tracker)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_EXPORT_TRACKER,
                self :: PARAM_EXPORT_TRACKER_ID => $export_tracker->get_id()));
    }

    function get_convert_answers_url($publication_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_CONVERT_ANSWERS,
                \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID => $publication_id));
    }
}