<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator;

use Chamilo\Configuration\Configuration;
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
     * @var Translation
     */
    protected $translator;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var ClassnameUtilities
     */
    protected $classNameUtilities;

    /**
     * @var array
     */
    protected $baseParameters;

    /**
     * NodeActionGeneratorFactory constructor.
     *
     * @param Translation $translator
     * @param Configuration $configuration
     * @param ClassnameUtilities $classnameUtilities
     * @param array $baseParameters
     */
    public function __construct(
        Translation $translator, Configuration $configuration, ClassnameUtilities $classnameUtilities,
        array $baseParameters
    )
    {
        $this->translator = $translator;
        $this->configuration = $configuration;
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
     */
    protected function getContentObjectTypeNodeActionGenerators()
    {
        $nodeActionGenerators = array();

        $integrationPackages = $this->configuration->getIntegrationRegistrations(LearningPath::package());

        foreach ($integrationPackages as $integrationPackage)
        {
            $namespace = ClassnameUtilities::getInstance()->getNamespaceParent(
                $integrationPackage[Registration::PROPERTY_CONTEXT],
                6
            );

            $contentObjectType = $namespace . '\Storage\DataClass\\' .
                ClassnameUtilities::getInstance()->getPackageNameFromNamespace($namespace);

            $contentObjectNodeActionGenerator = $integrationPackage[Registration::PROPERTY_CONTEXT]
                . '\Display\Service\NodeActionGenerator';

            if (class_exists($contentObjectNodeActionGenerator))
            {
                $nodeActionGenerators[$contentObjectType] =
                    new $contentObjectNodeActionGenerator($this->translator, $this->baseParameters);
            }
        }

        return $nodeActionGenerators;
    }
}