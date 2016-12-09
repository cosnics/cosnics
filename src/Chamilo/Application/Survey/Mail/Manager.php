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
    const ACTION_BROWSE = 'Browser';
    const ACTION_VIEW = 'Viewer';
    const ACTION_SEND_MAIL = 'SendMail';
    const ACTION_TEST_MAIL = 'TestMail';
    const ACTION_DELETE = 'Delete';
    const DEFAULT_ACTION = self::ACTION_BROWSE;
    
    // url
    function get_browse_mail_url($publication_id)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_BROWSE, self::PARAM_PUBLICATION_ID => $publication_id));
    }

    function get_view_mail_url($survey_publication_mail, $tab)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_VIEW, 
                self::PARAM_PUBLICATION_MAIL_ID => $survey_publication_mail->get_id(), 
                DynamicTabsRenderer::PARAM_SELECTED_TAB => $tab));
    }

    function get_send_mail_url($publication_id, $type)
    {
        return $this->get_url(
            array(
                self::PARAM_ACTION => self::ACTION_SEND_MAIL, 
                self::PARAM_PUBLICATION_ID => $publication_id, 
                self::PARAM_TYPE => $type));
    }

    function get_test_mail_url($publication_id)
    {
        return $this->get_url(
            array(self::PARAM_ACTION => self::ACTION_TEST_MAIL, self::PARAM_PUBLICATION_ID => $publication_id));
    }

    public function get_parameters()
    {
        $parameters = parent::get_parameters();
        $parameters[\Chamilo\Application\Survey\Manager::PARAM_PUBLICATION_ID] = $this->getRequest()->get(
            \Chamilo\Application\Survey\Manager::PARAM_PUBLICATION_ID);
        return $parameters;
    }
}