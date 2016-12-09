<?php
namespace Chamilo\Core\Repository\Implementation\Office365Video;

use Chamilo\Core\Repository\External\Infrastructure\Service\MicrosoftSharePointClientSettingsProvider;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Protocol\MicrosoftClient\MicrosoftClientService;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

/**
 * Data connector for Microsoft Video API.
 * Tips:
 * - Microsoft Azure admin site: https://manage.windowsazure.com
 * - Tutorial on registering a web application at Microsoft Azure:
 * https://msdn.microsoft.com/office/office365/howto/add-common-consent-manually#bk_RegisterWebApp
 * - Tutorials for activating a SharePoint site:
 * https://msdn.microsoft.com/en-us/library/office/fp179924.aspx
 * https://msdn.microsoft.com/en-us/library/office/fp142379.aspx
 */
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    /**
     *
     * @var MicrosoftClientService;
     */
    private $microsoftClientService;

    /**
     * Caches the return value of function getChannels().
     * 
     * @var array
     */
    private $channels;

    /**
     * Caches the return value of function getChannelId().
     * 
     * @var string
     */
    private $channelId;

    /**
     * Caches the return value of function getVideos().
     * 
     * @var array
     */
    private $videos;
    const DOCUMENTS_SHARED = 'shared_with_me';
    const DOCUMENTS_RECENT = 'recent';

    /**
     * DataConnector constructor.
     * 
     * @param Instance $externalRepositoryInstance
     */
    public function __construct($externalRepositoryInstance)
    {
        parent::__construct($externalRepositoryInstance);
        
        $user = new User();
        $user->setId(Session::get_user_id());
        
        $this->microsoftClientService = new MicrosoftClientService(
            new MicrosoftSharePointClientSettingsProvider($externalRepositoryInstance, $user));
    }

    /**
     * Logs on to the Office 365.
     */
    public function login()
    {
        $error = Request::get('error');
        if (! is_null($error))
        {
            return false;
        }
        
        $replyParameters = array();
        $replyParameters[Application::PARAM_CONTEXT] = Manager::context();
        $replyParameters[Manager::PARAM_ACTION] = Manager::ACTION_LOGIN;
        $replyParameters[Manager::PARAM_EXTERNAL_REPOSITORY] = $this->get_external_repository_instance_id();
        
        $code = Request::get('code');
        return $this->microsoftClientService->login($replyParameters, $code);
    }

    public static function translate_search_query($query)
    {
        return $query;
    }

    public function getChannels()
    {
        if (! isset($this->channels))
        {
            $request = $this->microsoftClientService->createRequest('GET', 'VideoService/Channels');
            $responseXml = simplexml_load_string($this->microsoftClientService->sendRequest($request, false));
            $namespaces = $responseXml->getNamespaces(true);
            
            $this->channels = array();
            
            foreach ($responseXml->entry as $entry)
            {
                $properties = $entry->content->children($namespaces['m'])->properties->children($namespaces['d']);
                $this->channels[(string) $properties->Id] = (string) $properties->Title;
            }
        }
        
        return $this->channels;
    }

    public function retrieve_external_repository_objects($condition, $orderProperty, $offset, $count)
    {
        $videos = $this->getVideos($condition, $orderProperty);
        
        $objects = array();
        $end = min(count($videos), $offset + $count);
        for ($i = $offset; $i < $end; ++ $i)
        {
            $objects[] = $this->parseExternalRepositoryObject($videos[$i], $this->videoNamespaces);
        }
        
        return new ArrayResultSet($objects);
    }

    public function count_external_repository_objects($condition)
    {
        return count($this->getVideos($condition));
    }

    public function retrieve_external_repository_object($id)
    {
        if ($this->isUserLoggedIn())
        {
            try
            {
                $request = $this->microsoftClientService->createRequest(
                    'GET', 
                    'VideoService/Channels(guid\'' . ExternalObject::getChannelId($id) . '\')/Videos(guid\'' .
                         ExternalObject::getVideoId($id) . '\')');
                $responseXml = simplexml_load_string($this->microsoftClientService->sendRequest($request, false));
                $namespaces = $responseXml->getNamespaces(true);
                $videoProperties = $responseXml->content->children($namespaces['m'])->properties->children(
                    $namespaces['d']);
                return $this->parseExternalRepositoryObject($videoProperties);
            }
            catch (\Exception $exception)
            {
                return null;
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns whether current user has an access token.
     */
    public function isUserLoggedIn()
    {
        return $this->microsoftClientService->isUserLoggedIn();
    }

    /**
     * Returns the embed HTML code for given video ID.
     */
    public function getVideoEmbedCode($id, $width = 600, $height = 480)
    {
        $request = $this->microsoftClientService->createRequest(
            'GET', 
            'VideoService/Channels(guid\'' . ExternalObject::getChannelId($id) . '\')/Videos(guid\'' .
                 ExternalObject::getVideoId($id) . '\')/GetVideoEmbedCode?width=' . $width . '&height=' . $height .
                 '&autoplay=false&showinfo=true');
        $responseXml = simplexml_load_string($this->microsoftClientService->sendRequest($request, false));
        return (string) $responseXml;
    }

    /**
     * Returns value of PARAM_CHANNEL_ID.
     * 
     * @return If value of PARAM_CHANNEL_ID is null, returns the ID of the first channel.
     */
    private function getChannelId()
    {
        if (! isset($this->channelId))
        {
            $this->channelId = Request::get(Manager::PARAM_CHANNEL_ID);
        }
        
        return $this->channelId;
    }

    /**
     * \brief Retrieves all videos in current channel for given $searchString.
     * \return array
     */
    private function getVideos($searchString = null, $orderProperty = null)
    {
        if (! isset($this->videos))
        {
            $this->videos = array();
            
            $channelId = $this->getChannelId();
            if (! empty($channelId))
            {
                $endpoint = 'VideoService/';
                if (empty($searchString))
                {
                    $endpoint .= 'Channels(guid\'' . $channelId . '\')/Videos';
                }
                else
                {
                    $endpoint .= 'Search/Query?querytext=\'' . $searchString . '\'&itemLimit=100';
                }
                
                $request = $this->microsoftClientService->createRequest('GET', $endpoint);
                $responseXml = simplexml_load_string($this->microsoftClientService->sendRequest($request, false));
                $namespaces = $responseXml->getNamespaces(true);
                foreach ($responseXml->entry as $entry)
                {
                    $this->videos[] = $entry->content->children($namespaces['m'])->properties->children(
                        $namespaces['d']);
                }
            }
        }
        
        $this->sortVideos($this->videos, $orderProperty);
        
        return $this->videos;
    }

    /**
     * Parses given XML entry and returns ExternalObject instance containing sparsed values.
     */
    private function parseExternalRepositoryObject($videoProperties)
    {
        $object = new ExternalObject();
        
        $object->setVideoAndChannelId((string) $videoProperties->ID, (string) $videoProperties->ChannelID);
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title((string) $videoProperties->Title);
        $object->set_description(nl2br((string) $videoProperties->Description));
        $object->set_owner_id((string) $videoProperties->ChannelID);
        
        $splitOwnerName = explode('#', (string) $videoProperties->OwnerName);
        if (count($splitOwnerName) > 1)
        {
            $object->set_owner_name($splitOwnerName[0]);
        }
        else
        {
            $object->set_owner_name((string) $videoProperties->OwnerName);
        }
        
        $object->set_created(strtotime((string) $videoProperties->CreatedDate));
        $object->set_modified(strtotime((string) $videoProperties->CreatedDate));
        $object->set_duration((string) $videoProperties->VideoDurationInSeconds);
        $object->set_url((string) $videoProperties->VideoDownloadUrl);
        $object->set_thumbnail((string) $videoProperties->ThumbnailUrl);
        $object->set_status((int) $videoProperties->VideoProcessingStatus);
        $object->set_rights($this->determineRights($object));
        
        return $object;
    }

    /**
     * Returns video rights.
     */
    public function determineRights($videoEntry)
    {
        $rights = array();
        $rights[ExternalObject::RIGHT_USE] = true;
        $rights[ExternalObject::RIGHT_EDIT] = false;
        $rights[ExternalObject::RIGHT_DELETE] = false;
        $rights[ExternalObject::RIGHT_DOWNLOAD] = true;
        return $rights;
    }

    public function delete_external_repository_object($id)
    {
        return false;
    }

    public function export_external_repository_object($object)
    {
        return false;
    }

    private function sortVideos(&$videos, $orderProperty)
    {
        if (! empty($orderProperty))
        {
            switch ($orderProperty[0]->get_property()->get_property())
            {
                case ExternalObject::PROPERTY_TITLE :
                    $compareFunctionName = 'compareVideoTitles';
                    break;
                
                case ExternalObject::PROPERTY_DESCRIPTION :
                    $compareFunctionName = 'compareVideoDescriptions';
                    break;
                
                case ExternalObject::PROPERTY_CREATED :
                    $compareFunctionName = 'compareVideoCreationDates';
                    break;
                
                default :
                    $compareFunctionName = null;
                    break;
            }
            
            $direction = 'Desc';
            if ($orderProperty[0]->get_direction() == SORT_ASC)
            {
                $direction = 'Asc';
            }
            
            if (! is_null($compareFunctionName))
            {
                usort($videos, array(self, $compareFunctionName . $direction));
            }
        }
    }

    public static function compareVideoTitlesAsc($a, $b)
    {
        return strcmp($a->Title, $b->Title);
    }

    public static function compareDriveItemTitlesDesc($a, $b)
    {
        return strcmp($b->Title, $a->Title);
    }

    public static function compareVideoDescriptionsAsc($a, $b)
    {
        return strcmp($a->Description, $b->Description);
    }

    public static function compareVideoDescriptionsDesc($a, $b)
    {
        return strcmp($b->Description, $a->Description);
    }

    public static function compareVideoCreationDatesAsc($a, $b)
    {
        return strtotime($a->CreatedDate) > strtotime($b->CreatedDate);
    }

    public static function compareVideoCreationDatesDesc($a, $b)
    {
        return strtotime($b->CreatedDate) > strtotime($a->CreatedDate);
    }
}
