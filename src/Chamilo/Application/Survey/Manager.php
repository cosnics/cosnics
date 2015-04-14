<?php
namespace Chamilo\Application\Survey;

use Chamilo\Core\Repository\Viewer\Component\ViewerComponent;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const APPLICATION_NAME = 'survey';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_PARTICIPANT_ID = 'participant_id';
    const PARAM_INVITEE_ID = 'invitee_id';
    const PARAM_USER_ID = 'user_id';
    const PARAM_GROUP_ID = 'group_id';
    const PARAM_MAIL_ID = 'mail_id';
    
    const ACTION_DELETE = 'Deleter';
    const ACTION_PUBLICATION_RIGHTS = 'PublicationRights';
    const ACTION_APPLICATION_RIGHTS = 'ApplicationRights';
    const ACTION_EDIT = 'Editor';
    const ACTION_PUBLISH = 'Publisher';
    const ACTION_BROWSE = 'Browser';
    const ACTION_TAKE = 'Taker';
    const ACTION_VIEW = 'Viewer';
    const ACTION_EXPORT = 'Exporter';
    const ACTION_SUBSCRIBE_EMAIL = 'SubscribeEmail';
    const ACTION_BROWSE_PARTICIPANTS = 'ParticipantBrowser';
    const ACTION_MAIL_INVITEES = 'Mail';
    const ACTION_DELETE_PARTICIPANT = 'ParticipantDeleter';
    
    const DEFAULT_ACTION = self :: ACTION_BROWSE;


    // Url Creation
    function get_create_survey_publication_url()
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_PUBLISH,
                ViewerComponent :: PARAM_ACTION => ViewerComponent :: ACTION_BROWSER));
    }

    function get_update_survey_publication_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_delete_survey_publication_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_browse_survey_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE), array(self :: PARAM_PUBLICATION_ID));
    }

    function get_survey_publication_viewer_url($survey_publication_id, $user_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VIEW,
                self :: PARAM_PUBLICATION_ID => $survey_publication_id,
                self :: PARAM_INVITEE_ID => $user_id));
    }

    function get_survey_publication_taker_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_TAKE,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_survey_publication_export_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EXPORT,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }
    
    function get_mail_survey_participant_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MAIL_INVITEES,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_browse_survey_participants_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_PARTICIPANTS,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

   
    
 

    function get_publication_rights_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_PUBLICATION_RIGHTS,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_application_rights_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPLICATION_RIGHTS));
    }
   

    function get_subscribe_email_url($publication_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_EMAIL,
                self :: PARAM_PUBLICATION_ID => $publication_id));
    }
  
   
}
?>