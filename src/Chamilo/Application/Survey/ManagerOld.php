<?php
namespace Chamilo\Application\Survey\test;

use Chamilo\Core\Repository\Viewer\Component\ViewerComponent;
use Chamilo\Libraries\Architecture\Application\Application;

abstract class ManagerOld extends Application
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

    function getUpdatePublicationUrl($Publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_EDIT,
                self :: PARAM_PUBLICATION_ID => $Publication->getId()));
    }

    function getDeletePublicationUrl($Publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_DELETE,
                self :: PARAM_PUBLICATION_ID => $Publication->get_id()));
    }

    function getBrowsePublicationsUrl()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE), array(self :: PARAM_PUBLICATION_ID));
    }

    function getPublicationViewerUrl($publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_VIEW,
                self :: PARAM_PUBLICATION_ID => $publication->getId()));
    }

    function getPublicationTakerUrl($publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_TAKE,
                self :: PARAM_PUBLICATION_ID => $publication->getId()));
    }

    function getPublicationRightsUrl($publication)
    {
        return $this->get_url(
            array(
                self :: PARAM_ACTION => self :: ACTION_PUBLICATION_RIGHTS,
                self :: PARAM_PUBLICATION_ID => $publication->getId()));
    }
   
}
?>