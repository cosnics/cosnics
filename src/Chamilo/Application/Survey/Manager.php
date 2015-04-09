<?php
namespace Chamilo\Application\Survey;

use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\Component\ViewerComponent;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class Manager extends Application
{
    const APPLICATION_NAME = 'survey';
    const PARAM_PUBLICATION_ID = 'publication_id';
    const PARAM_SURVEY_ID = 'survey_id';
    const PARAM_PARTICIPANT_ID = 'participant_id';
    const PARAM_INVITEE_ID = 'invitee_id';
    const PARAM_USER_ID = 'user_id';
    const PARAM_GROUP_ID = 'group_id';
    const PARAM_SURVEY_PAGE_ID = 'page_id';
    const PARAM_SURVEY_QUESTION_ID = 'question_id';
    const PARAM_MAIL_ID = 'mail_id';
    const ACTION_DELETE = 'Deleter';
    const ACTION_PUBLICATION_RIGHTS = 'PublicationRights';
    const ACTION_APPLICATION_RIGHTS = 'ApplicationRights';
    const ACTION_EDIT = 'Editor';
    const ACTION_PUBLISH = 'Publisher';
    const ACTION_BROWSE = 'Browser';
    const ACTION_TAKE = 'Taker';
    const ACTION_VIEW = 'Viewer';
    const ACTION_REPORTING_FILTER = 'ReportingFilter';
    const ACTION_REPORTING = 'Reporting';
    const ACTION_EXPORT = 'Exporter';
    const ACTION_SUBSCRIBE_EMAIL = 'SubscribeEmail';
    const ACTION_INVITE_USER = 'Inviter';
    const ACTION_INVITE_TEMPLATE_USER = 'SubscribeTemplateUser';
    const ACTION_BROWSE_PARTICIPANTS = 'ParticipantBrowser';
    const ACTION_CANCEL_INVITATION = 'InvitationCanceler';
    const ACTION_EXPORT_RESULTS = 'ResultsExporter';
    const ACTION_MAIL_INVITEES = 'Mail';
    const ACTION_INVITE_EXTERNAL_USERS = 'Inviter';
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

    function get_survey_publication_viewer_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VIEW,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id(),
                self :: PARAM_SURVEY_ID => $survey_publication->get_content_object_id(),
                self :: PARAM_INVITEE_ID => $this->get_user_id()));
    }

    function get_survey_publication_taker_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_TAKE,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_reporting_filter_survey_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING_FILTER));
    }

    function get_reporting_survey_publication_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_REPORTING,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_question_reporting_url($question)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_QUESTION_REPORTING,
                self :: PARAM_SURVEY_QUESTION_ID => $question->get_id()));
    }

    function get_results_exporter_url($tracker_id)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EXPORT_RESULTS, 'tid' => $tracker_id));
    }

    function get_mail_survey_participant_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_MAIL_INVITEES,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_survey_publication_export_excel_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EXPORT,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_browse_survey_participants_url($survey_publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE_PARTICIPANTS,
                self :: PARAM_PUBLICATION_ID => $survey_publication->get_id()));
    }

    function get_survey_participant_publication_viewer_url($survey_participant_tracker)
    {
        $survey_id = DataManager :: retrieve_by_id(
            Publication :: class_name(),
            $survey_participant_tracker->get_survey_publication_id())->get_content_object_id();
        return $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_VIEW,
                Manager :: PARAM_PUBLICATION_ID => $survey_participant_tracker->get_survey_publication_id(),
                Manager :: PARAM_INVITEE_ID => $survey_participant_tracker->get_user_id(),
                self :: PARAM_SURVEY_ID => $survey_id));
    }

    function get_survey_participant_delete_url($survey_participant_tracker)
    {
        return $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_DELETE_PARTICIPANT,
                self :: PARAM_PARTICIPANT_ID => $survey_participant_tracker->get_id()));
    }

    function get_survey_invitee_publication_viewer_url($publication_id, $user_id)
    {
        $survey_id = DataManager :: retrieve_by_id(Publication :: class_name(), $publication_id)->get_content_object_id();
        return $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_VIEW,
                Manager :: PARAM_PUBLICATION_ID => $publication_id,
                Manager :: PARAM_INVITEE_ID => $user_id,
                self :: PARAM_SURVEY_ID => $survey_id));
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

    function get_survey_cancel_invitation_url($survey_publication_id, $invitee)
    {
        return $this->get_url(
            array(
                Manager :: PARAM_ACTION => Manager :: ACTION_CANCEL_INVITATION,
                Manager :: PARAM_INVITEE_ID => $survey_publication_id . '|' . $invitee));
    }

    function get_subscribe_email_url($publication_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_EMAIL,
                self :: PARAM_PUBLICATION_ID => $publication_id));
    }

    function get_invite_user_url($publication_id)
    {
        return $this->get_url(
            array(self :: PARAM_ACTION => self :: ACTION_INVITE_USER, self :: PARAM_PUBLICATION_ID => $publication_id));
    }

    function get_invite_template_user_url($publication_id)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_INVITE_TEMPLATE_USER,
                self :: PARAM_PUBLICATION_ID => $publication_id));
    }
   
}
?>