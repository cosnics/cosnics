<?php

namespace Chamilo\Core\Install\Service;

use Chamilo\Core\Install\Configuration;
use Chamilo\Core\Install\Service\Interfaces\ConfigurationWriterInterface;
use Chamilo\Libraries\File\Filesystem;
use InvalidArgumentException;

/**
 * Writes the installer configuration to a configuration file
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ConfigurationWriter implements ConfigurationWriterInterface
{
    /**
     * @var string
     */
    protected $configurationTemplatePath;

    /**
     * XmlConfigurationWriter constructor.
     *
     * @param string $configurationTemplatePath
     */
    public function __construct($configurationTemplatePath)
    {
        if (!file_exists($configurationTemplatePath) || !is_readable($configurationTemplatePath))
        {
            throw new InvalidArgumentException(
                'The given configuration template path does not exist or is not readable for the system'
            );
        }

        $this->configurationTemplatePath = $configurationTemplatePath;
    }

    /**
     * Writes the installer configuration to a configuration file
     *
     * @param Configuration $configuration
     * @param $outputFile
     */
    public function writeConfiguration(Configuration $configuration, $outputFile)
    {
        $templateContent = $this->readTemplate();
        $parameters = $this->initializeTemplateParameters($configuration);
        $templateContent = $this->substituteParameters($templateContent, $parameters);



        $this->writeConfigurationFile($templateContent, $outputFile);
    }

    /**
     * Reads the content of the configuration template file
     */
    protected function readTemplate()
    {
        return file_get_contents($this->configurationTemplatePath);
    }

    /**
     * Initializes the template parameters based on the given configuration
     *
     * @param Configuration $configuration
     *
     * @return array
     */
    protected function initializeTemplateParameters(Configuration $configuration)
    {
        $parameters = array();

        $parameters['chamilo.configuration.general.security_key'] = md5(uniqid(rand() . time()));
        $parameters['chamilo.configuration.general.hashing_algorithm'] = $configuration->get_crypt_algorithm();
        $parameters['chamilo.configuration.general.install_date'] = time();
        $parameters['chamilo.configuration.database.driver'] = $configuration->get_db_driver();
        $parameters['chamilo.configuration.database.username'] = $configuration->get_db_username();
        $parameters['chamilo.configuration.database.password'] = $configuration->get_db_password();
        $parameters['chamilo.configuration.database.host'] = $configuration->get_db_host();
        $parameters['chamilo.configuration.database.name'] = $configuration->get_db_name();
        $parameters['chamilo.configuration.debug.show_errors'] = false;
        $parameters['chamilo.configuration.debug.enable_query_cache'] = true;
        $parameters['chamilo.configuration.storage.archive_path'] = $configuration->get_archive_path();
        $parameters['chamilo.configuration.storage.cache_path'] = $configuration->get_cache_path();
        $parameters['chamilo.configuration.storage.garbage_path'] = $configuration->get_garbage_path();
        $parameters['chamilo.configuration.storage.hotpotatoes_path'] = $configuration->get_hotpotatoes_path();
        $parameters['chamilo.configuration.storage.logs_path'] = $configuration->get_logs_path();
        $parameters['chamilo.configuration.storage.repository_path'] = $configuration->get_repository_path();
        $parameters['chamilo.configuration.storage.scorm_path'] = $configuration->get_scorm_path();
        $parameters['chamilo.configuration.storage.temp_path'] = $configuration->get_temp_path();
        $parameters['chamilo.configuration.storage.userpictures_path'] = $configuration->get_userpictures_path();

        return $parameters;
    }

    /**
     * Substitutes the given parameters in the given configuration content
     *
     * @param string $configurationContent
     * @param array $parameters
     *
     * @return string
     */
    protected function substituteParameters($configurationContent, $parameters = array())
    {
        foreach($parameters as $variable => $value)
        {
            $configurationContent = str_replace('{' . $variable . '}', $value, $configurationContent);
        }

        return $configurationContent;
    }

    /**
     * Writes the content of the configuration to the given output file
     *
     * @param string $configurationContent
     * @param string $outputFile
     */
    protected function writeConfigurationFile($configurationContent, $outputFile)
    {
        if (!is_dir(dirname($outputFile)))
        {
            Filesystem::create_dir($outputFile);
        }

        if (!is_writable(dirname($outputFile)))
        {
            throw new InvalidArgumentException(
                'The system is not allowed to write to the given directory for the output file'
            );
        }

        file_put_contents($outputFile, $configurationContent);
    }
}