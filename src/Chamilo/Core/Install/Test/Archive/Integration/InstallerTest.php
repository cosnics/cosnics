<?php
namespace Chamilo\Core\Install\Test\Integration;

use Chamilo\Libraries\Connection;
use Chamilo\Libraries\CoreApplication;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\MDB2;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Filesystem\Filesystem;

class InstallerTest extends PHPUnit_Framework_TestCase
{

    public $CORE_APPS = [
        'core\admin',
        'core\help',
        'core\reporting',
        'core\tracking',
        'core\repository',
        'core\user',
        'core\group',
        'core\rights',
        'core\home',
        'core\menu',
        'core\migration'
    ];

    public $EXTRA_DIRECTORIES = [
        'archive',
        'garbage',
        'repository',
        'temp',
        'userpictures',
        'scorm',
        'logs',
        'hotpotatoes'
    ];

    /**
     * @var string the config file containing all installation options
     */
    private $install_configuration_file;

    /**
     * @var install\Installer
     */
    private $installer;

    /**
     * @var install\Configuration
     */
    private $installer_config;

    public function __construct()
    {
        parent::__construct();
        $this->backupGlobals = false; // MDB needs this
        $this->install_configuration_file = __DIR__ . '/__files/install_config.php';
    }

    private function add_dumb_table_in_db()
    {
        $definition = [
            'id' => ['type' => 'integer', 'unsigned' => 1, 'notnull' => 1, 'default' => 0],
            'name' => ['type' => 'text', 'length' => 255],
            'datetime' => ['type' => 'timestamp']
        ];

        $mdb2->createTable('dumb', $definition);
    }

    private function assertIsNotMDB2Error($mdb2Result)
    {
        if (MDB2::isError($mdb2Result))
        {
            $this->fail("MDB2 shouldn't have returned an error : {$mdb2Result->getMessage()}");
        }
    }

    private function reset_db()
    {
        $cx = $this->installer->get_db_connection();
        if (!is_null($cx))
        {
            $db_name = $cx->getDatabase();
            if ($cx->databaseExists($db_name))
            {
                $cx->loadModule('Manager');
                $tables = $cx->manager->listTables();
                foreach ($tables as $table)
                {
                    $cx->manager->dropTable($table);
                }
            }
        }
    }

    public function setUp(): void
    {
        $this->tune_error_reporting_for_mdb2();
        $this->installer_config = new Configuration();
        $this->installer = new Installer($this->installer_config);

        $this->installer_config->load_config_file($this->install_configuration_file);
    }

    public function tearDown(): void
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $filesystem = new Filesystem();

        $filesystem->remove($systemPathBuilder->getStoragePath() . 'configuration/configuration.php');
        foreach ($this->EXTRA_DIRECTORIES as $directory)
        {
            $filesystem->remove($systemPathBuilder->getStoragePath() . $directory);
        }
        $this->reset_db();
    }

    public function test_installation_should_be_observable()
    {
        $observer1 = new test\Chamilo\DumbObserver();
        $observer2 = new test\Chamilo\DumbObserver();
        $this->installer->add_observer($observer1);
        $this->installer->add_observer($observer2);
        $this->installer->perform_install();
        $this->assertSame($observer1->getEvents(), $observer2->getEvents());
        $expected_array = [
            'before_install()',
            'before_preprod()',
            'preprod_config_file_written(InstallerTestResult : Success? : 1)',
            'preprod_db_created(InstallerTestResult : Success? : 1)',
            'after_preprod()',
            'before_content_objects_install()',
            'after_content_objects_install()',
            'before_core_applications_install()',
            'before_application_install(admin)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_application_install(tracking)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_application_install(repository)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_application_install(user)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_application_install(group)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_application_install(rights)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_application_install(home)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_application_install(menu)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_application_install(reporting)',
            'after_application_install(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(admin)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(tracking)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(repository)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(user)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(group)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(rights)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(home)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(menu)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'before_post_process_for_application(reporting)',
            'after_post_process_for_application(InstallerTestResult : Success? : 1)',
            'after_core_applications_install()',
            'before_optional_applications_install()',
            'after_optional_applications_install()',
            'before_filesystem_prepared()',
            'after_filesystem_prepared(InstallerTestResult : Success? : 1)',
            'after_install()'
        ];

        $this->assertSame($expected_array, $observer1->getEvents());
    }

    public function test_installation_should_create_config_file()
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $this->installer_config->set_db_overwrite(false); // faster
        $this->assertFileNotExists($systemPathBuilder->getStoragePath() . 'configuration/configuration.php');
        $this->installer->perform_install();
        $this->assertFileExists($systemPathBuilder->getStoragePath() . 'configuration/configuration.php');
    }

    public function test_installation_should_create_db_if_nonexistent()
    {
        $cx = $this->installer->get_or_create_db_connection();
        $db_name = $this->installer_config->get_db_name();
        $dropResult = $cx->dropDatabase($db_name);
        $this->assertIsNotMDB2Error($dropResult);
        $this->installer->perform_install();
        $this->assertTrue($cx->databaseExists($db_name));
    }

    public function test_installation_should_create_extra_directories()
    {
        $systemPathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);

        $this->installer_config->set_db_overwrite(false); // faster
        foreach ($this->EXTRA_DIRECTORIES as $directory)
        {
            $this->assertFileNotExists($systemPathBuilder->getStoragePath() . $directory);
        }
        $this->installer->perform_install();
        foreach ($this->EXTRA_DIRECTORIES as $directory)
        {
            $this->assertFileExists($systemPathBuilder->getStoragePath() . $directory);
        }
    }

    public function test_installation_should_fail_when_cannot_connect_the_db()
    {
        $this->installer_config->set_db_host('nonexistinghost');
        $this->installer_config->set_db_username('nobody');
        $this->installer_config->set_db_password('nothing');
        $this->setExpectedException('Exception');
        $this->installer->perform_install();
    }

    public function test_installation_should_install_all_core_applications()
    {
        $this->installer->perform_install();
        foreach ($this->CORE_APPS as $application_name)
        {
            $this->assertTrue(CoreApplication::exists($application_name));
            $application = CoreApplication::factory($application_name);
            $this->assertTrue($application->is_active());
        }
    }

    public function test_installation_should_install_specified_extra_application()
    {
        // TODO : don't know how to automatic test without creating dependencies
    }

    public function test_installation_should_keep_db_when_specified()
    {
        $this->add_dumb_table_in_db();
        $this->installer_config->set_db_overwrite(false);
        $this->installer->perform_install();
        $table_list = Connection::getInstance()->get_connection()->listTables();
        $this->assertContains('dumb', $table_list);
    }

    public function test_installation_should_overwrite_db_when_specified()
    {
        $this->add_dumb_table_in_db();
        $this->installer_config->set_db_overwrite(true);
        $this->installer->perform_install();
        $table_list = Connection::getInstance()->get_connection()->listTables();
        $this->assertNotContains('dumb', $table_list);
    }

    public function test_installation_should_register_all_content_objects()
    {
        // TODO : don't know how to automatic test without creating dependencies
    }

    private function tune_error_reporting_for_mdb2()
    {
        ini_set('error_reporting', E_ALL);
    }
}

namespace Chamilo\Install\Test;

class DumbObserver implements InstallerObserver
{

    private $events = [];

    public function after_application_install(StepResult $result)
    {
        $this->events[] = __FUNCTION__ . "({$result})";
    }

    public function after_content_object_install(StepResult $result)
    {
        $this->events[] = __FUNCTION__ . "({$result})";
    }

    public function after_content_objects_install()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function after_core_applications_install()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function after_filesystem_prepared(StepResult $result)
    {
        $this->events[] = __FUNCTION__ . "({$result})";
    }

    public function after_install()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function after_optional_applications_install()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function after_post_process()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function after_post_process_for_application(StepResult $result)
    {
        $this->events[] = __FUNCTION__ . "({$result})";
    }

    public function after_preprod()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function before_application_install($application)
    {
        $this->events[] = __FUNCTION__ . "({$application})";
    }

    public function before_content_object_install($content_object)
    {
        $this->events[] = __FUNCTION__ . "({$content_object})";
    }

    public function before_content_objects_install()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function before_core_applications_install()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function before_filesystem_prepared()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function before_install()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function before_optional_applications_install()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function before_post_process()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function before_post_process_for_application($application)
    {
        $this->events[] = __FUNCTION__ . "({$application})";
    }

    public function before_preprod()
    {
        $this->events[] = __FUNCTION__ . '()';
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function preprod_config_file_written(StepResult $result)
    {
        $this->events[] = __FUNCTION__ . "({$result})";
    }

    public function preprod_db_created(StepResult $result)
    {
        $this->events[] = __FUNCTION__ . "({$result})";
    }
}
