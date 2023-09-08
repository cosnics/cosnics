<?php
namespace Chamilo\Core\Install\Service;

use Chamilo\Core\Install\Architecture\Interfaces\ConfigurationWriterInterface;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Writes the installer configuration to a configuration file
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConfigurationWriter implements ConfigurationWriterInterface
{
    protected string $configurationTemplatePath;

    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem, string $configurationTemplatePath)
    {
        if (!file_exists($configurationTemplatePath) || !is_readable($configurationTemplatePath))
        {
            throw new InvalidArgumentException(
                'The given configuration template path does not exist or is not readable for the system'
            );
        }

        $this->filesystem = $filesystem;
        $this->configurationTemplatePath = $configurationTemplatePath;
    }

    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * Initializes the template parameters based on the given configuration
     *
     * @param string[][] $configuration
     */
    protected function initializeTemplateParameters(array $configuration): array
    {
        $parameters = [];

        $parameters['chamilo.configuration.general.security_key'] = md5(uniqid(rand() . time()));
        $parameters['chamilo.configuration.general.hashing_algorithm'] = $configuration['hashing_algorithm'];
        $parameters['chamilo.configuration.general.install_date'] = time();
        $parameters['chamilo.configuration.database.driver'] = $configuration['database']['driver'];
        $parameters['chamilo.configuration.database.username'] = $configuration['database']['username'];
        $parameters['chamilo.configuration.database.password'] = $configuration['database']['password'];
        $parameters['chamilo.configuration.database.host'] = $configuration['database']['host'];
        $parameters['chamilo.configuration.database.name'] = $configuration['database']['name'];
        $parameters['chamilo.configuration.database.charset'] = $configuration['database']['charset'];
        $parameters['chamilo.configuration.debug.show_errors'] = false;
        $parameters['chamilo.configuration.debug.enable_query_cache'] = true;
        $parameters['chamilo.configuration.storage.archive_path'] = $configuration['path']['archive_path'];
        $parameters['chamilo.configuration.storage.cache_path'] = $configuration['path']['cache_path'];
        $parameters['chamilo.configuration.storage.garbage_path'] = $configuration['path']['garbage_path'];
        $parameters['chamilo.configuration.storage.hotpotatoes_path'] = $configuration['path']['hotpotatoes_path'];
        $parameters['chamilo.configuration.storage.logs_path'] = $configuration['path']['logs_path'];
        $parameters['chamilo.configuration.storage.repository_path'] = $configuration['path']['repository_path'];
        $parameters['chamilo.configuration.storage.scorm_path'] = $configuration['path']['scorm_path'];
        $parameters['chamilo.configuration.storage.temp_path'] = $configuration['path']['temp_path'];
        $parameters['chamilo.configuration.storage.userpictures_path'] = $configuration['path']['userpictures_path'];

        return $parameters;
    }

    /**
     * Reads the content of the configuration template file
     */
    protected function readTemplate(): string
    {
        return file_get_contents($this->configurationTemplatePath);
    }

    /**
     * Substitutes the given parameters in the given configuration content
     */
    protected function substituteParameters(string $configurationContent, array $parameters = []): string
    {
        foreach ($parameters as $variable => $value)
        {
            $configurationContent = str_replace('{' . $variable . '}', $value, $configurationContent);
        }

        return $configurationContent;
    }

    /**
     * Writes the installer configuration to a configuration file
     */
    public function writeConfiguration(array $configurationValues, string $outputFile): void
    {
        $templateContent = $this->readTemplate();
        $parameters = $this->initializeTemplateParameters($configurationValues);
        $templateContent = $this->substituteParameters($templateContent, $parameters);

        $this->writeConfigurationFile($templateContent, $outputFile);
    }

    /**
     * Writes the content of the configuration to the given output file
     */
    protected function writeConfigurationFile(string $configurationContent, string $outputFile): void
    {
        if (!is_dir(dirname($outputFile)))
        {
            $this->getFilesystem()->mkdir($outputFile);
        }

        if (!is_writable(dirname($outputFile)))
        {
            throw new InvalidArgumentException(
                'The system is not allowed to write to the given directory for the output file'
            );
        }

        $this->getFilesystem()->dumpFile($outputFile, $configurationContent);
    }
}