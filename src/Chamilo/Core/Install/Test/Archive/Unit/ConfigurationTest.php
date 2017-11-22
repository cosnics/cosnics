<?php
namespace Chamilo\Core\Install\Test\Unit;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{

    private $config;

    public function setUp()
    {
        $this->config = new Configuration();
    }

    public function test_as_values_array_should_contains_all_db_slots()
    {
        $db_slots = array(
            'database_driver',
            'database_host',
            'database_name',
            'database_overwrite',
            'database_username',
            'database_password');

        $values = $this->config->as_values_array();
        $all_keys = \Chamilo\ArrayKeys($values);

        $this->assertEquals(array(), array_diff($db_slots, $all_keys));
    }

    public function test_as_values_array_should_contains_all_admin_slots()
    {
        $admin_slots = array(
            'admin_email',
            'admin_surname',
            'admin_firstname',
            'admin_phone',
            'admin_username',
            'admin_password');

        $values = $this->config->as_values_array();
        $all_keys = \Chamilo\ArrayKeys($values);

        $this->assertEquals(array(), array_diff($admin_slots, $all_keys));
    }

    public function test_as_values_array_should_contains_all_platform_slots()
    {
        $platform_slots = array(
            'platform_language',
            'site_name',
            'organization_name',
            'organization_url',
            'self_reg',
            'hashing_algorithm');

        $values = $this->config->as_values_array();
        $all_keys = \Chamilo\ArrayKeys($values);

        $this->assertEquals(array(), array_diff($platform_slots, $all_keys));
    }

    public function test_load_file_should_be_able_to_load_a_install_config_file()
    {
        $this->config->load_config_file(__DIR__ . '/__files/config_ok.php');

        $expected = array();
        $expected['storage_type'] = 'mdb2';
        $expected['database_driver'] = 'mysqli';
        $expected['database_host'] = 'hostname';
        $expected['database_name'] = 'db_name';
        $expected['database_overwrite'] = true;
        $expected['database_username'] = 'db_user';
        $expected['database_password'] = 'db_password';
        $expected['platform_language'] = 'english';
        $expected['admin_email'] = 'admin@localhost';
        $expected['admin_surname'] = 'Doe';
        $expected['admin_firstname'] = 'Jack';
        $expected['admin_phone'] = '0123456789';
        $expected['admin_username'] = 'admin_login';
        $expected['admin_password'] = 'admin_password';
        $expected['site_name'] = 'Testing Platform';
        $expected['organization_name'] = 'Automatic Test Technology';
        $expected['organization_url'] = 'www.chamilo.org';
        $expected['self_reg'] = '0';
        $expected['hashing_algorithm'] = 'sha1';

        $this->assertEquals($expected, $this->config->as_values_array());
    }
}
