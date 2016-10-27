<?php
namespace Chamilo\Libraries\Architecture\Factory;

use \Symfony\Component\HttpFoundation\Request;
use Chamilo\Configuration\Service\FileConfigurationLoader;
use Chamilo\Libraries\Architecture\Bootstrap\Kernel;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\Platform\Translation;

class KernelFactory
{

    /**
     *
     * @var \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    private $fileConfigurationLoader;

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    public function __construct(FileConfigurationLoader $fileConfigurationLoader,
        \Symfony\Component\HttpFoundation\Request $request)
    {
        $this->fileConfigurationLoader = $fileConfigurationLoader;
        $this->request = $request;
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     *
     * @return \Chamilo\Configuration\Service\FileConfigurationLoader
     */
    public function getFileConfigurationLoader()
    {
        return $this->fileConfigurationLoader;
    }

    /**
     *
     * @param \Chamilo\Configuration\Service\FileConfigurationLoader $fileConfigurationLoader
     */
    public function setFileConfigurationLoader(FileConfigurationLoader $fileConfigurationLoader)
    {
        $this->fileConfigurationLoader = $fileConfigurationLoader;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Bootstrap\Kernel
     */
    public function getKernel()
    {
        $applicationFactory = new ApplicationFactory($stringUtilities, $translation);
        $configurationConsulter = new ConfigurationConsulter($dataLoader);
        $translationUtilities = Translation::getInstance();


        return new Kernel(
            $this->getFileConfigurationLoader(),
            $this->getRequest(),
            $applicationFactory,
            $configurationConsulter,
            $translationUtilities,
            $exceptionLogger);
    }
}

