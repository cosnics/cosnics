<?php
namespace Chamilo\Application\Survey;

use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Application\Survey\Storage\DataManager;
use Chamilo\Core\Repository\Viewer\Component\ViewerComponent;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

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

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

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

    // publications
    static function is_content_object_editable($object_id)
    {
        return DataManager :: get_instance()->is_content_object_editable($object_id);
    }

    static function content_object_is_published($object_id)
    {
        return DataManager :: get_instance()->content_object_is_published($object_id);
    }

    static function any_content_object_is_published($object_ids)
    {
        return DataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    static function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null,
        $order_property = null)
    {
        return DataManager :: get_instance()->get_content_object_publication_attributes(
            $object_id,
            $type,
            $offset,
            $count,
            $order_property);
    }

    static function get_content_object_publication_attribute($publication_id)
    {
        return DataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

    static function count_publication_attributes($type = null, $condition = null)
    {
        return DataManager :: get_instance()->count_publication_attributes($type, $condition);
    }

    static function delete_content_object_publications($object_id)
    {
        return DataManager :: get_instance()->delete_content_object_publications($object_id);
    }

    static function delete_content_object_publication($publication_id)
    {
        return DataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    static function update_content_object_publication_id($publication_attr)
    {
        return DataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    static function add_publication_attributes_elements($form)
    {
        $form->addElement('category', Translation :: get('PublicationDetails'));
        $form->addElement(
            'checkbox',
            self :: APPLICATION_NAME . '_opt_' . Publication :: PROPERTY_HIDDEN,
            Translation :: get('Hidden'));
        $form->add_select(
            self :: APPLICATION_NAME . '_opt_' . Publication :: PROPERTY_TYPE,
            Translation :: get('SurveyType'),
            Publication :: get_types());
        $form->add_forever_or_timewindow('PublicationPeriod', self :: APPLICATION_NAME . '_opt_');

        $attributes = array();
        $attributes['search_url'] = Path :: getInstance()->getBasePath(true) .
             'common/libraries/php/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);

        $form->add_receivers(
            self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET,
            Translation :: get('PublishFor'),
            $attributes);

        $form->addElement('category');
        $form->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $form->setDefaults($defaults);
    }

    static function get_content_object_publication_locations($content_object)
    {
        // no publication from repository to tool:: has to be evaluated

        // $allowed_types = array(Survey :: get_type_name());
        //
        // $type = $content_object->get_type();
        // if (in_array($type, $allowed_types))
        // {
        // // $categories = DataManager :: get_instance()->retrieve_survey_publication_categories();
        // $locations = array();
        // // while ($category = $categories->next_result())
        // // {
        // // $locations[$category->get_id()] = $category->get_name() . ' - category';
        // // }
        // // $locations[0] = Translation :: get('RootSurveyCategory');
        //
        //
        // $locations[1] = Translation :: get('SurveyApplication');
        // return $locations;
        // }
        return array();
    }

    static function publish_content_object($content_object, $location, $attributes)
    {
        if (! Rights :: is_allowed_in_surveys_subtree(
            Rights :: RIGHT_ADD,
            Rights :: LOCATION_BROWSER,
            Rights :: TYPE_COMPONENT))
        {
            return Translation :: get('NoRightsForPublication');
        }

        $publication = new Publication();
        $publication->set_content_object_id($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        $publication->set_published(time());

        if ($attributes[Publication :: PROPERTY_HIDDEN] == 1)
        {
            $publication->set_hidden(1);
        }
        else
        {
            $publication->set_hidden(0);
        }

        if ($attributes['forever'] == 1)
        {
            $publication->set_from_date(0);
            $publication->set_to_date(0);
        }
        else
        {
            $publication->set_from_date(DatetimeUtilities :: time_from_datepicker($attributes['from_date']));
            $publication->set_to_date(DatetimeUtilities :: time_from_datepicker($attributes['to_date']));
        }

        $publication->set_type($attributes[Publication :: PROPERTY_TYPE]);

        if ($attributes[self :: PARAM_TARGET_OPTION] != 0)
        {
            $user_ids = $attributes[self :: PARAM_TARGET_ELEMENTS]['user'];
            $group_ids = $attributes[self :: PARAM_TARGET_ELEMENTS]['group'];
        }
        else
        {
            $users = \Chamilo\Core\User\Storage\DataManager :: retrieve_users();
            $user_ids = array();
            while ($user = $users->next_result())
            {
                $user_ids[] = $user->get_id();
            }
        }

        $publication->create();

        $locations[] = Rights :: get_location_by_identifier_from_surveys_subtree(
            $publication->get_id(),
            Rights :: TYPE_PUBLICATION);

        foreach ($locations as $location)
        {
            foreach ($user_ids as $user_id)
            {
                Rights :: set_user_right_location_value(Rights :: RIGHT_VIEW, $user_id, $location->get_id(), 1);
            }
            foreach ($group_ids as $group_id)
            {
                Rights :: set_group_right_location_value(Rights :: RIGHT_VIEW, $group_id, $location->get_id(), 1);
            }
        }

        return Translation :: get('PublicationCreated');
    }
}