<?php
namespace Chamilo\Core\Repository\Implementation\Photobucket;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use Chamilo\Libraries\Utilities\StringUtilities;
use PBAPI;

// require_once 'OAuth/Request.php';
require_once Path :: getInstance()->getPluginPath(__NAMESPACE__) . 'PBAPI/PBAPI.php';
/**
 *
 * @author magali.gillard key : 149830482 secret : 410277f61d5fc4b01a9b9e763bf2e97b
 */
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    private $photobucket;

    private $consumer;

    private $key;

    private $secret;

    private $photobucket_session;

    public function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $this->key = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'consumer_key',
            $this->get_external_repository_instance_id());
        $this->secret = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'consumer_secret',
            $this->get_external_repository_instance_id());
        $url = \Chamilo\Core\Repository\Instance\Storage\DataClass\Setting :: get(
            'url',
            $this->get_external_repository_instance_id());
        $this->login();
    }

    public function login()
    {
        $this->consumer = new PBAPI($this->key, $this->secret);
        $this->consumer->setResponseParser('simplexmlarray');

        $this->photobucket_session = unserialize(
            Setting :: get('session', $this->get_external_repository_instance_id()));
        $oauth_access_token = $this->photobucket_session['photobucket_access_token'];

        $oauth_request_token = Session :: retrieve('photobucket_request_token');
        if (! $oauth_access_token)
        {
            if (! $oauth_request_token)
            {
                $this->consumer->login('request')->post()->loadTokenFromResponse();
                Session :: register('photobucket_request_token', serialize($this->consumer->getOauthToken()));

                $redirect = new Redirect();
                $currentUrl = $redirect->getCurrentUrl();
                $this->consumer->goRedirect('login', $currentUrl);
            }
            else
            {
                $oauth_request_token = unserialize($oauth_request_token);
                $this->consumer->setOAuthToken($oauth_request_token->getKey(), $oauth_request_token->getSecret());

                $this->consumer->login('access')->post()->loadTokenFromResponse();

                $session_array = array();
                $session_array['photobucket_access_token'] = $this->consumer->getOAuthToken();
                $session_array['photobucket_username'] = $this->consumer->getUsername();
                $session_array['photobucket_subdomain'] = $this->consumer->getSubdomain();
                $session_array = serialize($session_array);

                $setting = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_setting_from_variable_name(
                    'session',
                    $this->get_external_repository_instance_id());
                $user_setting = new Setting();
                $user_setting->set_setting_id($setting->get_id());
                $user_setting->set_user_id(Session :: get_user_id());
                $user_setting->set_value($session_array);
                if ($user_setting->create())
                {
                    Session :: unregister('photobucket_request_token');
                }
            }
        }
        else
        {
            $username = $this->photobucket_session['photobucket_username'];
            $subdomain = $this->photobucket_session['photobucket_subdomain'];

            $this->consumer->setOAuthToken($oauth_access_token->getKey(), $oauth_access_token->getSecret(), $username);
            $this->consumer->setSubdomain($subdomain);
        }
    }

    // Only image for the moment
    public function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $response = $this->retrieve_photos($condition, $order_property, $offset, $count);
        $objects = array();

        foreach ($response['media'] as $media)
        {
            $objects[] = $this->set_photo_object($media);
        }
        return new ArrayResultSet($objects);
    }

    public function retrieve_photos($condition, $order_property, $offset, $count)
    {
        $feed_type = Request :: get(Manager :: PARAM_FEED_TYPE);

        $offset = (($offset - ($offset % $count)) / $count) + 1;
        if (is_null($condition))
        {
            $condition = '';
        }

        switch ($feed_type)
        {
            case Manager :: FEED_TYPE_GENERAL :
                $response = $this->consumer->search(
                    $condition,
                    array('num' => $count, 'perpage' => $count, 'page' => $offset, 'secondaryperpage' => 1))->get()->getParsedResponse(
                    true);
                if ($condition)
                {
                    $response = $response['result']['primary'];
                }
                else
                {
                    $response = $response['result'];
                }

                break;
            case Manager :: FEED_TYPE_MY_PHOTOS :
                if ($condition)
                {
                    $response = $this->consumer->search(
                        $condition,
                        array('num' => $count, 'perpage' => $count, 'page' => $offset, 'secondaryperpage' => 1))->get()->getParsedResponse(
                        true);
                    $response = $response['result']['primary'];
                }
                else
                {
                    $response = $this->consumer->user($this->photobucket_session['photobucket_username'])->search(
                        $condition,
                        array('perpage' => $count, 'page' => $offset, 'type' => 'image'))->get()->getParsedResponse(true);
                    if ($response['_attribs']['totalresults'] == 1)
                    {
                        $response['media'] = array($response['media']);
                    }
                }
                break;
            default :
                if ($condition)
                {
                    $response = $this->consumer->search(
                        $condition,
                        array('num' => $count, 'perpage' => $count, 'page' => $offset, 'secondaryperpage' => 1))->get()->getParsedResponse(
                        true);
                    $response = $response['result']['primary'];
                }
                else
                {
                    $response = $this->consumer->user($this->photobucket_session['photobucket_username'])->search(
                        $condition,
                        array('perpage' => $count, 'page' => $offset, 'type' => 'image'))->get()->getParsedResponse(true);
                    if ($response['_attribs']['totalresults'] == 1)
                    {
                        $response['media'] = array($response['media']);
                    }
                }
                break;
        }
        return $response;
    }

    public function retrieve_external_repository_object($id)
    {
        $data = $this->consumer->media(urldecode($id))->get()->getParsedResponse(true);

        return $this->set_photo_object($data['media']);
    }

    public function set_photo_object($data)
    {
        $object = new ExternalObject();
        $object->set_id(urlencode($data['url']));
        $object->set_title($data['title']);
        $object->set_description($data['description']);
        $object->set_url($data['url']);
        $object->set_thumbnail($data['thumb']);

        $object->set_owner_id($data['_attribs']['username']);
        $object->set_created($data['_attribs']['uploaddate']);
        $object->set_modified($data['_attribs']['uploaddate']);
        $object->set_type(
            (string) StringUtilities :: getInstance()->createString($data['_attribs']['type'])->underscored());
        $object->set_rights($this->determine_rights($data));

        $tags = array();
        if (array_key_exists('_attribs', $data['tag']))
        {
            $data['tag'] = array($data['tag']);
        }

        if (count($data['tag']) > 0)
        {
            foreach ($data['tag'] as $tag)
            {
                $tags[] = $tag['_attribs']['tag'];
            }
        }

        $object->set_tags($tags);

        return $object;
    }

    public function count_external_repository_objects($condition)
    {
        $feed_type = Request :: get(Manager :: PARAM_FEED_TYPE);

        if (is_null($condition))
        {
            $condition = '';
        }

        switch ($feed_type)
        {
            case Manager :: FEED_TYPE_GENERAL :

                if ($condition)
                {
                    $response = $this->consumer->search(
                        $condition,
                        array('num' => 1, 'perpage' => 1, 'page' => 1, 'secondaryperpage' => 1))->get()->getParsedResponse(
                        true);
                    return $response['result']['_attribs']['totalresults'];
                }
                else
                {
                    return 900;
                }

                break;
            case Manager :: FEED_TYPE_MY_PHOTOS :
                if ($condition)
                {
                    $response = $this->consumer->search(
                        $condition,
                        array('num' => 1, 'perpage' => 1, 'page' => 1, 'secondaryperpage' => 1))->get()->getParsedResponse(
                        true);
                    return $response['result']['_attribs']['totalresults'];
                }
                else
                {
                    $response = $this->consumer->user($this->photobucket_session['photobucket_username'])->get()->getParsedResponse(
                        true);
                    return $response['total_pictures'];
                }

                break;
            default :
                if ($condition)
                {
                    $response = $this->consumer->search(
                        $condition,
                        array('num' => 1, 'perpage' => 1, 'page' => 1, 'secondaryperpage' => 1))->get()->getParsedResponse(
                        true);
                    return $response['result']['_attribs']['totalresults'];
                }
                else
                {
                    $response = $this->consumer->user($this->photobucket_session['photobucket_username'])->get()->getParsedResponse(
                        true);
                    return $response['total_pictures'];
                }
                break;
        }
    }

    /**
     *
     * @param $values array
     * @return boolean
     */
    public function update_external_repository_object($values)
    {
        while ($data = $this->consumer->media(urldecode($values[ExternalObject :: PROPERTY_ID]))->tag()->get()->getParsedResponse(
            true))
        {
            $response = $this->consumer->media(urldecode($values[ExternalObject :: PROPERTY_ID]))->tag($data['tagid'])->delete()->getParsedResponse(
                true);
        }

        $response = $this->consumer->media(urldecode($values[ExternalObject :: PROPERTY_ID]))->title(
            array('title' => $values[ExternalObject :: PROPERTY_TITLE]))->put()->getParsedResponse(true);
        if (! $response)
        {
            return false;
        }
        else
        {
            $response = $this->consumer->media(urldecode($values[ExternalObject :: PROPERTY_ID]))->description(
                array('description' => $values[ExternalObject :: PROPERTY_DESCRIPTION]))->put()->getParsedResponse(true);
            if (! $response)
            {
                return false;
            }
        }
        if ($values[ExternalObject :: PROPERTY_TAGS])
        {
            $tags = explode(',', $values[ExternalObject :: PROPERTY_TAGS]);

            foreach ($tags as $tag)
            {
                $response = $this->consumer->media(urldecode($values[ExternalObject :: PROPERTY_ID]))->tag(
                    array('tag' => $tag, 'topleftx' => 0, 'toplefty' => 0, 'bottomrightx' => 0, 'bottomrighty' => 0))->post()->getParsedResponse(
                    true);
                if (! $response)
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function delete_external_repository_object($id)
    {
        $response = $this->consumer->media($id)->delete()->getParsedResponse(true);
        if ($response['deleted'] == 1)
        {
            return true;
        }
        return false;
    }

    public function create_external_repository_object($values, $file)
    {
        $photo = base64_encode(file_get_contents($file['tmp_name']));
        $tags = explode(',', $values[ExternalObject :: PROPERTY_TAGS]);
        $session = unserialize(
            Setting :: get('session', $this->get_external_repository_instance_id(), Session :: get_user_id()));
        $response = $this->consumer->album($session['photobucket_username'])->upload(
            array(
                'type' => 'base64',
                'filename' => $file['name'],
                'uploadfile' => $photo,
                'title' => $values[ExternalObject :: PROPERTY_TITLE],
                'description' => $values[ExternalObject :: PROPERTY_DESCRIPTION]))->post()->getParsedResponse(true);

        foreach ($tags as $tag)
        {
            $this->consumer->media(urlencode($response['url']))->tag(
                array('tag' => $tag, 'topleftx' => 0, 'toplefty' => 0, 'bottomrightx' => 0, 'bottomrighty' => 0))->post()->getParsedResponse(
                true);
        }
        return urlencode($response['url']);
    }

    public function export_external_repository_object($object)
    {
        $photo = base64_encode(file_get_contents($object->get_full_path()));

        $response = $this->consumer->album($this->photobucket_session['photobucket_username'])->upload(
            array(
                'type' => 'base64',
                'filename' => $object->get_filename(),
                'uploadfile' => $photo,
                'title' => $object->get_title(),
                'description' => $object->get_description()))->post()->getParsedResponse(true);

        return urlencode($response['url']);
    }

    public function determine_rights($photo)
    {
        $rights = array();
        if ($this->photobucket_session['photobucket_username'] == $photo['_attribs']['username'])
        {

            $rights[ExternalObject :: RIGHT_USE] = true;
            $rights[ExternalObject :: RIGHT_EDIT] = true;
            $rights[ExternalObject :: RIGHT_DELETE] = true;
            $rights[ExternalObject :: RIGHT_DOWNLOAD] = true;
        }
        else
        {
            $rights[ExternalObject :: RIGHT_USE] = true;
            $rights[ExternalObject :: RIGHT_EDIT] = false;
            $rights[ExternalObject :: RIGHT_DELETE] = false;
            $rights[ExternalObject :: RIGHT_DOWNLOAD] = false;
        }
        return $rights;
    }

    /**
     *
     * @param $query string
     * @return string
     */
    static public

    function translate_search_query($query)
    {
        return $query;
    }
}
