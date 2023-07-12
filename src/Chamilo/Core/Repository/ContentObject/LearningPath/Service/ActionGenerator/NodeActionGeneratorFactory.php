<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator;

use Chamilo\Configuration\Service\Consulter\RegistrationConsulter;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;

/**
 * Factory class to create the NodeActionGenerator
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NodeActionGeneratorFactory
{
    /**
     * @var array
     */
    protected $baseParameters;

    /**
     * @var ClassnameUtilities
     */
    protected $classNameUtilities;

    /**
     * @var RegistrationConsulter
     */
    protected $registrationConsulter;

    /**
     * @var Translation
     */
    protected $translator;

    /**
     * NodeActionGeneratorFactory constructor.
     *
     * @param Translation $translator
     * @param RegistrationConsulter $registrationConsulter
     * @param ClassnameUtilities $classnameUtilities
     * @param array $baseParameters
     */
    public function __construct(
        Translation $translator, RegistrationConsulter $registrationConsulter, ClassnameUtilities $classnameUtilities,
        array $baseParameters
    )
    {
        $this->translator = $translator;
        $this->registrationConsulter = $registrationConsulter;
        $this->classNameUtilities = $classnameUtilities;
        $this->baseParameters = $baseParameters;
    }

    /**
     * Creates the NodeActionGenerator instance
     *
     * @return NodeActionGenerator
     */
    public function createNodeActionGenerator()
    {
        $contentObjectTypeNodeActionGenerators = $this->getContentObjectTypeNodeActionGenerators();

        return new NodeBaseActionGenerator(
            $this->translator, $this->baseParameters, $contentObjectTypeNodeActionGenerators
        );
    }

    /**
     * Returns the action generators for specific content object types
     *
     * @return array
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function getContentObjectTypeNodeActionGenerators()
    {
        $nodeActionGenerators = [];

        $integrationPackages = $this->registrationConsulter->getIntegrationRegistrations(LearningPath::CONTEXT);

        foreach ($integrationPackages as $integrationPackage)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $integrationPackage[Registration::PROPERTY_CONTEXT], 6
            );

            $contentObjectType = $namespace . '\Storage\DataClass\\' .
                ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);

            $contentObjectNodeActionGenerator =
                $integrationPackage[Registration::PROPERTY_CONTEXT] . '\Display\Service\NodeActionGenerator';

            if (class_exists($contentObjectNodeActionGenerator))
            {
                $nodeActionGenerators[$contentObjectType] =
                    new $contentObjectNodeActionGenerator($this->translator, $this->baseParameters);
            }
        }

        return $nodeActionGenerators;
    }
}