<?php
namespace Chamilo\Libraries\Architecture\Test\PHPUnitGenerator;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Configuration\Service\RegistrationConsulter;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PathBuilder;

/**
 * Generates the global phpunit configuration file for Chamilo
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PHPUnitGenerator implements PHPUnitGeneratorInterface
{

    /**
     *
     * @var \Twig\Environment
     */
    protected $twig;

    /**
     *
     * @var PathBuilder
     */
    protected $pathBuilder;

    /**
     *
     * @var RegistrationConsulter
     */
    protected $registrationConsulter;

    /**
     * The path to the PHPUnit configuration file
     *
     * @var string
     */
    protected $phpUnitFile;

    /**
     * Constructor
     *
     * @param \Twig\Environment $twig
     * @param PathBuilder $pathBuilder
     * @param RegistrationConsulter $registrationConsulter
     */
    public function __construct(\Twig\Environment $twig, PathBuilder $pathBuilder,
        RegistrationConsulter $registrationConsulter)
    {
        $this->twig = $twig;
        $this->pathBuilder = $pathBuilder;
        $this->registrationConsulter = $registrationConsulter;
        $this->phpUnitFile = $pathBuilder->getStoragePath() . '/configuration/phpunit.xml';
    }

    /**
     * Generates the global phpunit configuration file for Chamilo
     *
     * @param boolean $includeSource
     */
    public function generate($includeSource = true)
    {
        $testDirectories = $sourceDirectories = $excludedDirectories = [];

        $registeredPackages = $this->registrationConsulter->getRegistrationContexts();
        foreach ($registeredPackages as $registeredPackage)
        {
            $packagePath = $this->pathBuilder->namespaceToFullPath($registeredPackage);
            $testPath = $packagePath . 'Test';
            $packagePHPUnitConfiguration = $testPath . DIRECTORY_SEPARATOR . 'phpunit.xml';

            if (! file_exists($packagePHPUnitConfiguration))
            {
                continue;
            }

            $domDocument = new \DOMDocument();
            $domDocument->load($packagePHPUnitConfiguration);

            $domXPath = new \DOMXPath($domDocument);
            $domNodeList = $domXPath->query('testsuites/testsuite');

            if ($domNodeList->length == 0)
            {
                continue;
            }

            foreach ($domNodeList as $domElement)
            {
                $testFolder = trim($domElement->textContent);
                if (! $includeSource && $testFolder == 'Source')
                {
                    continue;
                }

                /** @var \DOMElement $domElement */
                $testDirectories[] = $testPath . DIRECTORY_SEPARATOR . $testFolder;
            }

            $sourceDirectories[] = $packagePath;
            $excludedDirectories[] = $testPath;
        }

        $phpunitConfiguration = $this->twig->render(
            'Chamilo\Libraries:PHPUnitGenerator/phpunit.xml.twig',
            array(
                'testDirectories' => $testDirectories,
                'sourceDirectories' => $sourceDirectories,
                'excludedDirectories' => $excludedDirectories));

        file_put_contents($this->phpUnitFile, $phpunitConfiguration);
    }
}
