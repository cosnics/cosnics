<?php
namespace Chamilo\Application\Survey\Mail;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'action';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_PUBLICATION_MAIL_ID = 'mail_id';
    const PARAM_MAIL_TRACKER_ID = 'mail_tracker_id';
    const PARAM_TYPE = 'mail_type';

    // used for import
    const PARAM_MESSAGE = 'message';
    const PARAM_WARNING_MESSAGE = 'warning_message';
    const PARAM_ERROR_MESSAGE = 'error_message';
    const ACTION_BROWSE = 'browser';
    const ACTION_VIEW = 'viewer';
    const ACTION_SEND_MAIL = 'send_mail';
    const ACTION_TEST_MAIL = 'test_mail';
    const ACTION_DELETE = 'delete';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    // url
    function get_browse_mail_url($publication_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_BROWSE, self :: PARAM_PUBLICATION_ID => $publication_id));
    }

    function get_view_mail_url($survey_publication_mail, $tab)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VIEW,
                self :: PARAM_PUBLICATION_MAIL_ID => $survey_publication_mail->get_id(),
                DynamicTabsRenderer :: PARAM_SELECTED_TAB => $tab));
    }

    function get_send_mail_url($publication_id, $type)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_SEND_MAIL,
                self :: PARAM_PUBLICATION_ID => $publication_id,
                self :: PARAM_TYPE => $type));
    }

    function get_test_mail_url($publication_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_TEST_MAIL, self :: PARAM_PUBLICATION_ID => $publication_id));
    }
}