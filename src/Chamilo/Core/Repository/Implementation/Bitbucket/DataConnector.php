<?php
namespace Chamilo\Core\Repository\Implementation\Bitbucket;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Setting;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestAuthentication;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestClient;
use Chamilo\Libraries\Protocol\Webservice\Rest\Client\RestData;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;
use ReflectionObject;
use ReflectionProperty;

/**
 *
 * @author magali.gillard
 */
class DataConnector extends \Chamilo\Core\Repository\External\DataConnector
{

    private $bitbucket;

    private $username;
    const BASIC_DOWNLOAD_URL = 'https://bitbucket.org/%s/get/%s.zip';

    public function __construct($external_repository_instance)
    {
        parent::__construct($external_repository_instance);
        $this->username = Setting::get('username', $this->get_external_repository_instance_id());
        $password = Setting::get('password', $this->get_external_repository_instance_id());
        $this->bitbucket = RestClient::factory('https://api.bitbucket.org/1.0/');
        $authentication = RestAuthentication::factory($this->bitbucket, RestAuthentication::TYPE_BASIC);
        $authentication->set_login($this->username);
        $authentication->set_password($password);
        
        $this->bitbucket->set_authentication($authentication);
    }

    public function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $repositories = $this->retrieve_bitbuckets($condition, $order_property, $offset, $count);
        $repositories = array_slice($repositories, $offset, $count);
        $repository_type = Request::get(Manager::PARAM_FOLDER);
        
        $bitbucket_repositories = array();
        foreach ($repositories as $repository)
        {
            $bitbucket_repository = $this->set_bitbucket_object($repository);
            switch ($repository_type)
            {
                case Manager::TYPE_OTHER :
                    $bitbucket_repository->set_id($repository->owner . '/' . $repository->slug);
                    $bitbucket_repository->set_owner_id($repository->owner);
                    break;
                case Manager::TYPE_OWN :
                    $bitbucket_repository->set_id($this->username . '/' . $repository->slug);
                    $bitbucket_repository->set_owner_id($this->username);
                    break;
                default :
                    $bitbucket_repository->set_id($this->username . '/' . $repository->slug);
                    $bitbucket_repository->set_owner_id($this->username);
                    break;
            }
            $bitbucket_repository->set_rights($this->determine_rights($bitbucket_repository));
            $bitbucket_repositories[] = $bitbucket_repository;
        }
        return new ArrayResultSet($bitbucket_repositories);
    }

    public function retrieve_external_repository_object($id)
    {
        // $response = $this->bitbucket->request(BitbucketRestClient :: METHOD_GET, 'repositories/' . $id . '/');
        $endpoint = 'repositories/' . $id . '/';
        $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
        $response = $this->bitbucket->request();
        
        $repository = $response->get_response_content();
        
        $bitbucket_repository = new ExternalObject();
        
        $repository_type = Request::get(Manager::PARAM_FOLDER);
        switch ($repository_type)
        {
            case Manager::TYPE_OTHER :
                $bitbucket_repository->set_id($repository->owner . '/' . $repository->slug);
                $bitbucket_repository->set_owner_id($repository->owner);
                break;
            case Manager::TYPE_OWN :
                $bitbucket_repository->set_id($this->username . '/' . $repository->slug);
                $bitbucket_repository->set_owner_id($this->username);
                break;
            default :
                $bitbucket_repository->set_id($this->username . '/' . $repository->slug);
                $bitbucket_repository->set_owner_id($this->username);
                break;
        }
        
        $bitbucket_repository->set_title($repository->name);
        $bitbucket_repository->set_description($repository->description);
        $bitbucket_repository->set_external_repository_id($this->get_external_repository_instance_id());
        $bitbucket_repository->set_type('public');
        $bitbucket_repository->set_created(strtotime($repository->created_on));
        $bitbucket_repository->set_modified(strtotime($repository->last_updated));
        $bitbucket_repository->set_logo($repository->logo);
        $bitbucket_repository->set_website($repository->website);
        $bitbucket_repository->set_rights($this->determine_rights($bitbucket_repository));
        return $bitbucket_repository;
    }

    public function retrieve_bitbuckets($condition = null, $order_property, $offset, $count)
    {
        $repository_type = Request::get(Manager::PARAM_FOLDER);
        
        switch ($repository_type)
        {
            case Manager::TYPE_OTHER :
                $this->bitbucket->configure(RestClient::METHOD_GET, 'repositories/?name=', $condition);
                $response = $this->bitbucket->request();
                $response = $response->get_response_content();
                $repositories = $response->repositories;
                break;
            case Manager::TYPE_OWN :
                $endpoint = 'users/' . $this->username . '/';
                $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
                $response = $this->bitbucket->request();
                $response = $response->get_response_content();
                $repositories = $response->repositories;
                break;
            default :
                $endpoint = 'users/' . $this->username . '/';
                $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
                $response = $this->bitbucket->request();
                $response = $response->get_response_content();
                $repositories = $response->repositories;
                break;
        }
        return $repositories;
    }

    public function set_bitbucket_object($repository)
    {
        $bitbucket_repository = new ExternalObject();
        $bitbucket_repository->set_title($repository->name);
        $bitbucket_repository->set_description($repository->description);
        $bitbucket_repository->set_external_repository_id($this->get_external_repository_instance_id());
        $bitbucket_repository->set_type('public');
        $bitbucket_repository->set_created(strtotime($repository->created_on));
        $bitbucket_repository->set_modified(strtotime($repository->last_updated));
        $bitbucket_repository->set_logo($repository->logo);
        
        return $bitbucket_repository;
    }

    public function retrieve_tags($id)
    {
        $endpoint = 'repositories/' . $id . '/tags/';
        $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
        $response = $this->bitbucket->request();
        $tags = $response->get_response_content();
        $reflect = new ReflectionObject($tags);
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        
        $bitbucket_tags = array();
        foreach ($properties as $property)
        {
            $name = $property->name;
            $tag = $tags->$name;
            if (! ($property->name == 'tip' && $tag->node == '000000000000'))
            {
                
                $bitbucket_tag = new Tag();
                $bitbucket_tag->set_name($property->name);
                $bitbucket_tag->set_author($tag->raw_author);
                $bitbucket_tag->set_time(strtotime($tag->timestamp));
                $bitbucket_tag->set_branch($tag->branch);
                $bitbucket_tag->set_id($tag->node);
                $bitbucket_tag->set_repository($id);
                
                $bitbucket_tags[] = $bitbucket_tag;
            }
        }
        return $bitbucket_tags;
    }

    public function retrieve_branches($id)
    {
        $endpoint = 'repositories/' . $id . '/branches/';
        $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
        $response = $this->bitbucket->request();
        $branches = $response->get_response_content();
        $reflect = new ReflectionObject($branches);
        $properties = $reflect->getProperties();
        $bitbucket_branches = array();
        foreach ($properties as $property)
        {
            $bitbucket_branches[] = $property->name;
        }
        return $bitbucket_branches;
    }

    public function retrieve_changesets($id, $limit = 5)
    {
        $endpoint = 'repositories/' . $id . '/changesets/?limit=' . $limit;
        $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
        $response = $this->bitbucket->request();
        $changesets = $response->get_response_content();
        $bitbucket_changesets = array();
        foreach ($changesets->changesets as $changeset)
        {
            $bitbucket_changeset = new Changeset();
            $bitbucket_changeset->set_author($changeset->author);
            $bitbucket_changeset->set_time(strtotime($changeset->timestamp));
            $bitbucket_changeset->set_message($changeset->message);
            $bitbucket_changeset->set_branch($changeset->branch);
            $bitbucket_changeset->set_revision($changeset->revision);
            $bitbucket_changeset->set_repository($id);
            $bitbucket_changeset->set_id($changeset->node);
            $bitbucket_changesets[] = $bitbucket_changeset;
        }
        return $bitbucket_changesets;
    }

    public function retrieve_privileges($id)
    {
        $endpoint = 'privileges/' . $id . '/';
        $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
        $response = $this->bitbucket->request();
        $privileges = $response->get_response_content();
        
        $bitbucket_privileges = array();
        foreach ($privileges as $privilege)
        {
            $bitbucket_privilege = new Privilege();
            $bitbucket_privilege->set_username($privilege->user->username);
            $bitbucket_privilege->set_first_name($privilege->user->first_name);
            $bitbucket_privilege->set_last_name($privilege->user->last_name);
            $bitbucket_privilege->set_repository($privilege->repo);
            $bitbucket_privilege->set_privilege($privilege->privilege);
            
            $bitbucket_privileges[] = $bitbucket_privilege;
        }
        return $bitbucket_privileges;
    }

    public function retrieve_groups_privileges($id)
    {
        $endpoint = 'group-privileges/' . $id . '/';
        $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
        $response = $this->bitbucket->request();
        $privileges = $response->get_response_content();
        
        $bitbucket_privileges = array();
        foreach ($privileges as $privilege)
        {
            $bitbucket_privilege = new GroupPrivilege();
            $bitbucket_privilege->set_group($privilege->group->slug);
            $bitbucket_privilege->set_name($privilege->group->name);
            $bitbucket_privilege->set_repository($privilege->repo);
            $bitbucket_privilege->set_privilege($privilege->privilege);
            $bitbucket_privilege->set_owner($privilege->group->owner);
            
            $bitbucket_privileges[] = $bitbucket_privilege;
        }
        return $bitbucket_privileges;
    }

    public function retrieve_groups($id)
    {
        $endpoint = 'groups/' . $id . '/';
        $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint);
        $response = $this->bitbucket->request();
        
        $groups = $response->get_response_content();
        $group_list = array();
        foreach ($groups as $group)
        {
            $bitbucket_group = new Group();
            $bitbucket_group->set_name($group->name);
            $bitbucket_group->set_permission($group->permission);
            $bitbucket_group->set_members($group->members);
            $bitbucket_group->set_slug($group->slug);
            $bitbucket_group->set_owner($group->owner);
            $group_list[] = $bitbucket_group;
        }
        
        return $group_list;
    }

    public function revoke_user_privilege($id, $user)
    {
        if ($user)
        {
            $url = 'privileges/' . $id . '/' . $user;
        }
        else
        {
            $url = 'privileges/' . $id;
        }
        
        $this->bitbucket->configure(RestClient::METHOD_DELETE, $url);
        $response = $this->bitbucket->request();
        if ($response->get_response_code() == 204)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function revoke_group_privilege($id, $group)
    {
        if ($group)
        {
            $url = 'group-privileges/' . $id . '/' . $group;
            $this->bitbucket->configure(RestClient::METHOD_DELETE, $url);
            $response = $this->bitbucket->request();
            if ($response->get_response_code() == 204)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $groups = $this->retrieve_groups_privileges($id);
            foreach ($groups as $group)
            {
                $url = 'group-privileges/' . $id . '/' . $group->get_owner_username() . '/' . $group->get_group();
                $this->bitbucket->configure(RestClient::METHOD_DELETE, $url);
                $response = $this->bitbucket->request();
                if ($response->get_response_code() !== 204)
                {
                    return false;
                }
            }
            return true;
        }
    }

    public function delete_group($group)
    {
        if ($group)
        {
            $url = 'groups/' . $group;
            $this->bitbucket->configure(RestClient::METHOD_DELETE, $url);
            $response = $this->bitbucket->request();
            if ($response->get_response_code() == 204)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function create_group($name, $permission)
    {
        $url = 'groups/' . $this->username;
        $this->bitbucket->configure(
            RestClient::METHOD_POST, 
            $url, 
            array(), 
            RestData::factory(
                RestData::TYPE_URL, 
                array('auto_add' => true, 'permission' => $permission, 'name' => $name)));
        $response = $this->bitbucket->request();
        if ($response->get_response_code() == 200)
        {
            $url = 'groups/' . $this->username . '/' . rawurlencode($name);
            $this->bitbucket->configure(
                RestClient::METHOD_PUT, 
                $url, 
                array(), 
                RestData::factory(
                    RestData::TYPE_FORM, 
                    array('name' => $name, 'permission' => $permission, 'auto_add' => true)));
            $response = $this->bitbucket->request();
            if ($response->get_response_code() == 200)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function create_repository($values)
    {
        $endpoint = 'repositories/';
        $this->bitbucket->configure(
            RestClient::METHOD_POST, 
            $endpoint, 
            array(), 
            RestData::factory(
                RestData::TYPE_FORM, 
                array(
                    'name' => $values['name'], 
                    'website' => $values['website'], 
                    'description' => $values['description'], 
                    'language' => 'php')));
        $response = $this->bitbucket->request();
        if ($response->get_response_code() != 200)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function add_user_to_group($group, $user)
    {
        $endpoint = 'groups/' . $group . '/members/' . $user . '/';
        $this->bitbucket->configure(
            RestClient::METHOD_PUT, 
            $endpoint, 
            array(), 
            RestData::factory(RestData::TYPE_PLAIN, ''));
        $response = $this->bitbucket->request();
        if ($response->get_response_code() != 200)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function grant_user_privilege($id, $users, $privilege)
    {
        $endpoint = 'privileges/' . $id . '/' . $users;
        $this->bitbucket->configure(
            RestClient::METHOD_PUT, 
            $endpoint, 
            array(), 
            RestData::factory(RestData::TYPE_PLAIN, $privilege));
        $response = $this->bitbucket->request();
        if ($response->get_response_code() == 401 || $response->get_response_code() == 403 ||
             $response->get_response_code() == 404)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function grant_group_privileges($id, $group, $privilege)
    {
        $endpoint = 'group-privileges/' . $id . '/' . $group;
        $this->bitbucket->configure(
            RestClient::METHOD_PUT, 
            $endpoint, 
            array(), 
            RestData::factory(RestData::TYPE_PLAIN, $privilege));
        $response = $this->bitbucket->request();
        if ($response->get_response_code() == 401 || $response->get_response_code() == 403 ||
             $response->get_response_code() == 404)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function retrieve_users_from_group($group)
    {
        $endpoint = 'groups/' . $group . '/members/';
        $this->bitbucket->configure(RestClient::METHOD_GET, $endpoint/*, array(), RestData :: factory(RestData :: TYPE_PLAIN, '')*/);
        $response = $this->bitbucket->request();
        if ($response->get_response_code() != 200)
        {
            return false;
        }
        else
        {
            $members = array();
            foreach ($response->get_response_content() as $member)
            {
                $member_object = new Member();
                $member_object->set_username($member->username);
                $members[] = $member_object;
            }
            return $members;
        }
    }

    public function delete_user_from_group($group, $user)
    {
        $endpoint = 'groups/' . $group . '/members/' . $user . '/';
        $this->bitbucket->configure(
            RestClient::METHOD_DELETE, 
            $endpoint, 
            array(), 
            RestData::factory(RestData::TYPE_PLAIN, ''));
        $response = $this->bitbucket->request();
        
        if ($response->get_response_code() != 204)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function update_repository($values)
    {
        $endpoint = 'repositories/' . $values['id'];
        $this->bitbucket->configure(
            RestClient::METHOD_PUT, 
            $endpoint, 
            array(), 
            RestData::factory(
                RestData::TYPE_FORM, 
                array(
                    'name' => $values['name'], 
                    'website' => $values['website'], 
                    'description' => $values['description'], 
                    'language' => 'php')));
        $response = $this->bitbucket->request();
        if ($response->get_response_code() != 200)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function count_external_repository_objects($condition)
    {
        return count($this->retrieve_bitbuckets($condition));
    }

    public function delete_external_repository_object($id)
    {
        $endpoint = 'repositories/' . $id . '/';
        $this->bitbucket->configure(RestClient::METHOD_DELETE, $endpoint);
        $response = $this->bitbucket->request();
        if ($response->get_response_code() == 401 || $response->get_response_code() == 403 ||
             $response->get_response_code() == 404)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function export_external_repository_object($object)
    {
    }

    public function determine_rights($bitbucket)
    {
        $rights = array();
        
        if ($bitbucket->get_owner_id() == $this->username)
        {
            
            $rights[ExternalObject::RIGHT_USE] = true;
            $rights[ExternalObject::RIGHT_EDIT] = true;
            $rights[ExternalObject::RIGHT_DELETE] = true;
            $rights[ExternalObject::RIGHT_DOWNLOAD] = true;
        }
        else
        {
            $rights[ExternalObject::RIGHT_USE] = true;
            $rights[ExternalObject::RIGHT_EDIT] = false;
            $rights[ExternalObject::RIGHT_DELETE] = false;
            $rights[ExternalObject::RIGHT_DOWNLOAD] = false;
        }
        return $rights;
    }

    /**
     *
     * @param $query string
     * @return string
     */
    public static function translate_search_query($query)
    {
        return $query;
    }

    public function create_repository_service($repository_id, $service_configuration)
    {
        $endpoint = 'repositories/' . $repository_id . '/services/';
        $this->bitbucket->configure(
            RestClient::METHOD_POST, 
            $endpoint, 
            array(), 
            RestData::factory(RestData::TYPE_FORM, $service_configuration));
        $response = $this->bitbucket->request();
        if ($response->get_response_code() != 200)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}
