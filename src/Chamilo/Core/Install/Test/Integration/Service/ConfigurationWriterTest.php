<?php
namespace Chamilo\Core\Install\Test\Integration\Service;

use Chamilo\Core\Install\Configuration;
use Chamilo\Core\Install\Service\ConfigurationWriter;
use Chamilo\Core\Install\Service\Interfaces\ConfigurationWriterInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Test\Test;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * Test the class
 * Chamilo\Core\Install\Service\ConfigurationWriter
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ConfigurationWriterTest extends Test
{

    /**
     *
     * @var ConfigurationWriterInterface
     */
    protected $configurationWriter;

    public function setUp()
    {
        $pathBuilder = new PathBuilder(new ClassnameUtilities(new StringUtilities()));
        $this->configurationWriter = new ConfigurationWriter(
            $pathBuilder->getResourcesPath('Chamilo\Core\Install') . 'Templates/configuration.xml.tpl');
    }

    public function tearDown()
    {
        unset($this->configurationWriter);
    }

    public function testWriteConfiguration()
    {
        $configuration = new Configuration();
        $configuration->set_db_name('chamilo_test_database');

        $tempPath = sys_get_temp_dir() . '/configuration.xml';

        $this->configurationWriter->writeConfiguration($configuration, $tempPath);

        $configurationContents = file_get_contents($tempPath);

        $this->assertContains('chamilo_test_database', $configurationContents);

        Filesystem::remove($tempPath);
    }
}