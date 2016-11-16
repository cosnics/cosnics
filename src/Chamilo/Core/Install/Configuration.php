<?php
namespace Chamilo\Core\Install;

class Configuration
{

    private $storage_type = 'mdb2';

    private $db_driver = 'mysql';

    private $db_host = 'localhost';

    private $db_name = 'chamilo';

    private $db_overwrite = true;

    private $db_username = '';

    private $db_password = '';

    private $packages = array();

    private $content_objects = array();

    private $external_instances = array();

    private $platform_language = 'english';

    private $base_url = 'http://localhost/chamilo/web';

    private $archive_path;

    private $cache_path;

    private $garbage_path;

    private $hotpotatoes_path;

    private $logs_path;

    private $repository_path;

    private $scorm_path;

    private $temp_path;

    private $userpictures_path;

    private $url_append = '/chamilo/web';

    private $admin_email = '';

    private $admin_lastname = '';

    private $admin_firstname = '';

    private $admin_phone = '';

    private $admin_login = 'admin';

    private $admin_password = 'admin';

    private $institution_name = '';

    private $campus_name = 'Chamilo Campus';

    private $institution_webpage = 'http://www.chamilo.org';

    private $self_register = false;

    private $crypt_algorithm = 'sha1';

    private $server_type = 'production';

    public function __construct()
    {
    }

    /**
     *
     * @deprecated , used for retrocompatibility with application installer
     */
    public function as_values_array()
    {
        $values = array();
        $values['storage_type'] = $this->get_storage_type();
        $values['database_driver'] = $this->get_db_driver();
        $values['database_host'] = $this->get_db_host();
        $values['database_name'] = $this->get_db_name();
        $values['database_overwrite'] = $this->get_db_overwrite();
        $values['database_username'] = $this->get_db_username();
        $values['database_password'] = $this->get_db_password();
        
        $values['platform_language'] = $this->get_platform_language();
        $values['platform_url'] = $this->get_base_url();
        $values['archive_path'] = $this->get_archive_path();
        $values['cache_path'] = $this->get_cache_path();
        $values['garbage_path'] = $this->get_garbage_path();
        $values['hotpotatoes_path'] = $this->get_hotpotatoes_path();
        $values['logs_path'] = $this->get_logs_path();
        $values['repository_path'] = $this->get_repository_path();
        $values['scorm_path'] = $this->get_scorm_path();
        $values['temp_path'] = $this->get_temp_path();
        $values['userpictures_path'] = $this->get_userpictures_path();
        $values['url_append'] = $this->get_url_append();
        
        $values['admin_email'] = $this->get_admin_email();
        $values['admin_surname'] = $this->get_admin_lastname();
        $values['admin_firstname'] = $this->get_admin_firstname();
        $values['admin_phone'] = $this->get_admin_phone();
        $values['admin_username'] = $this->get_admin_login();
        $values['admin_password'] = $this->get_admin_password();
        
        $values['platform_name'] = $this->get_campus_name();
        $values['organization_name'] = $this->get_institution_name();
        $values['organization_url'] = $this->get_institution_webpage();
        
        $values['self_reg'] = $this->get_self_register();
        
        $values['hashing_algorithm'] = $this->get_crypt_algorithm();
        
        $values['server_type'] = $this->get_server_type();
        
        foreach ($this->packages as $package)
        {
            $values['install'][$package] = '1';
        }
        
        foreach ($this->content_objects as $content_object)
        {
            $values['content_object'][$content_object] = '1';
        }
        
        foreach ($this->external_instances as $external_instance)
        {
            $values['external_instance'][$external_instance] = '1';
        }
        return $values;
    }

    public function load_config_file($file)
    {
        if (! (is_file($file)))
            throw new \Exception("Install config file {$file} not found !");
        if (! (is_readable($file)))
            throw new \Exception("Install config file {$file} not readable !");
        
        global $values;
        unset($values);
        $values = array();
        include $file;
        $this->load_array($values);
        unset($values);
    }

    public function load_array($values)
    {
        if (isset($values['storage_type']))
            $this->set_storage_type($values['storage_type']);
        if (isset($values['database_driver']))
            $this->set_db_driver($values['database_driver']);
        if (isset($values['database_host']))
            $this->set_db_host($values['database_host']);
        if (isset($values['database_name']))
            $this->set_db_name($values['database_name']);
        if (isset($values['database_overwrite']))
            $this->set_db_overwrite($values['database_overwrite']);
        if (isset($values['database_username']))
            $this->set_db_username($values['database_username']);
        if (isset($values['database_password']))
            $this->set_db_password($values['database_password']);
        
        if (isset($values['platform_language']))
            $this->set_platform_language($values['platform_language']);
        if (isset($values['platform_url']))
            $this->set_base_url($values['platform_url']);
        if (isset($values['url_append']))
            $this->set_url_append($values['url_append']);
        
        if (isset($values['archive_path']))
            $this->set_archive_path($values['archive_path']);
        if (isset($values['cache_path']))
            $this->set_cache_path($values['cache_path']);
        if (isset($values['garbage_path']))
            $this->set_garbage_path($values['garbage_path']);
        if (isset($values['hotpotatoes_path']))
            $this->set_hotpotatoes_path($values['hotpotatoes_path']);
        if (isset($values['logs_path']))
            $this->set_logs_path($values['logs_path']);
        if (isset($values['repository_path']))
            $this->set_repository_path($values['repository_path']);
        if (isset($values['scorm_path']))
            $this->set_scorm_path($values['scorm_path']);
        if (isset($values['temp_path']))
            $this->set_temp_path($values['temp_path']);
        if (isset($values['userpictures_path']))
            $this->set_userpictures_path($values['userpictures_path']);
        
        if (isset($values['admin_email']))
            $this->set_admin_email($values['admin_email']);
        if (isset($values['admin_surname']))
            $this->set_admin_lastname($values['admin_surname']);
        if (isset($values['admin_firstname']))
            $this->set_admin_firstname($values['admin_firstname']);
        if (isset($values['admin_phone']))
            $this->set_admin_phone($values['admin_phone']);
        if (isset($values['admin_username']))
            $this->set_admin_login($values['admin_username']);
        if (isset($values['admin_password']))
            $this->set_admin_password($values['admin_password']);
        
        if (isset($values['platform_name']))
            $this->set_campus_name($values['platform_name']);
        if (isset($values['organization_name']))
            $this->set_institution_name($values['organization_name']);
        if (isset($values['organization_url']))
            $this->set_institution_webpage($values['organization_url']);
        
        if (isset($values['self_reg']))
            $this->set_self_register($values['self_reg']);
        if (isset($values['hashing_algorithm']))
            $this->set_crypt_algorithm($values['hashing_algorithm']);
        if (isset($values['server_type']))
            $this->set_server_type($values['server_type']);
        
        $this->set_packages(array_keys((array) $values['install']));
    }

    public function get_properties()
    {
        array_keys(get_object_vars($this));
    }

    public function get_storage_type()
    {
        return $this->storage_type;
    }

    public function set_storage_type($storage_type)
    {
        $this->storage_type = $storage_type;
    }

    public function get_db_driver()
    {
        return $this->db_driver;
    }

    public function set_db_driver($db_driver)
    {
        $this->db_driver = $db_driver;
    }

    public function get_db_host()
    {
        return $this->db_host;
    }

    public function set_db_host($db_host)
    {
        $this->db_host = $db_host;
    }

    public function get_db_name()
    {
        return $this->db_name;
    }

    public function set_db_name($db_name)
    {
        $this->db_name = $db_name;
    }

    public function get_db_overwrite()
    {
        return $this->db_overwrite;
    }

    public function set_db_overwrite($db_overwrite)
    {
        $this->db_overwrite = $db_overwrite;
    }

    public function get_db_username()
    {
        return $this->db_username;
    }

    public function set_db_username($db_username)
    {
        $this->db_username = $db_username;
    }

    public function get_db_password()
    {
        return $this->db_password;
    }

    public function set_db_password($db_password)
    {
        $this->db_password = $db_password;
    }

    public function get_packages()
    {
        return $this->packages;
    }

    public function set_packages($package_list)
    {
        $this->packages = $package_list;
    }

    public function get_platform_language()
    {
        return $this->platform_language;
    }

    public function set_platform_language($platform_language)
    {
        $this->platform_language = $platform_language;
    }

    public function get_base_url()
    {
        return $this->base_url;
    }

    public function set_base_url($base_url)
    {
        $this->base_url = $base_url;
    }

    public function get_archive_path()
    {
        return $this->archive_path;
    }

    public function set_archive_path($archive_path)
    {
        $this->archive_path = $archive_path;
    }

    public function get_cache_path()
    {
        return $this->cache_path;
    }

    public function set_cache_path($cache_path)
    {
        $this->cache_path = $cache_path;
    }

    public function get_garbage_path()
    {
        return $this->garbage_path;
    }

    public function set_garbage_path($garbage_path)
    {
        $this->garbage_path = $garbage_path;
    }

    public function get_hotpotatoes_path()
    {
        return $this->hotpotatoes_path;
    }

    public function set_hotpotatoes_path($hotpotatoes_path)
    {
        $this->hotpotatoes_path = $hotpotatoes_path;
    }

    public function get_logs_path()
    {
        return $this->logs_path;
    }

    public function set_logs_path($logs_path)
    {
        $this->logs_path = $logs_path;
    }

    public function get_repository_path()
    {
        return $this->repository_path;
    }

    public function set_repository_path($repository_path)
    {
        $this->repository_path = $repository_path;
    }

    public function get_scorm_path()
    {
        return $this->scorm_path;
    }

    public function set_scorm_path($scorm_path)
    {
        $this->scorm_path = $scorm_path;
    }

    public function get_temp_path()
    {
        return $this->temp_path;
    }

    public function set_temp_path($temp_path)
    {
        $this->temp_path = $temp_path;
    }

    public function get_userpictures_path()
    {
        return $this->userpictures_path;
    }

    public function set_userpictures_path($userpictures_path)
    {
        $this->userpictures_path = $userpictures_path;
    }

    public function get_admin_email()
    {
        return $this->admin_email;
    }

    public function set_admin_email($admin_email)
    {
        $this->admin_email = $admin_email;
    }

    public function get_admin_lastname()
    {
        return $this->admin_lastname;
    }

    public function set_admin_lastname($admin_lastname)
    {
        $this->admin_lastname = $admin_lastname;
    }

    public function get_admin_firstname()
    {
        return $this->admin_firstname;
    }

    public function set_admin_firstname($admin_firstname)
    {
        $this->admin_firstname = $admin_firstname;
    }

    public function get_admin_phone()
    {
        return $this->admin_phone;
    }

    public function set_admin_phone($admin_phone)
    {
        $this->admin_phone = $admin_phone;
    }

    public function get_admin_login()
    {
        return $this->admin_login;
    }

    public function set_admin_login($admin_login)
    {
        $this->admin_login = $admin_login;
    }

    public function get_admin_password()
    {
        return $this->admin_password;
    }

    public function set_admin_password($admin_password)
    {
        $this->admin_password = $admin_password;
    }

    public function get_institution_name()
    {
        return $this->institution_name;
    }

    public function set_institution_name($institution_name)
    {
        $this->institution_name = $institution_name;
    }

    public function get_campus_name()
    {
        return $this->campus_name;
    }

    public function set_campus_name($campus_name)
    {
        $this->campus_name = $campus_name;
    }

    public function get_institution_webpage()
    {
        return $this->institution_webpage;
    }

    public function set_institution_webpage($institution_webpage)
    {
        $this->institution_webpage = $institution_webpage;
    }

    public function get_self_register()
    {
        return $this->self_register;
    }

    public function set_self_register($self_register)
    {
        $this->self_register = $self_register;
    }

    public function get_crypt_algorithm()
    {
        return $this->crypt_algorithm;
    }

    public function set_crypt_algorithm($crypt_algorithm)
    {
        $this->crypt_algorithm = $crypt_algorithm;
    }

    public function get_server_type()
    {
        return $this->server_type;
    }

    public function set_server_type($server_type)
    {
        $this->server_type = $server_type;
    }

    public function get_url_append()
    {
        return $this->url_append;
    }

    public function set_url_append($url_append)
    {
        $this->url_append = $url_append;
    }
}
