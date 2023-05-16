<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service\ActionGenerator;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeActionGeneratorFactory;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator\NodeBaseActionGenerator;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Configuration\Configuration;

/**
 * Tests the NodeActionGeneratorFactory class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class NodeActionGeneratorFactoryTest extends ChamiloTestCase
{
    /**
     * @var NodeActionGeneratorFactory
     */
    protected $nodeActionGeneratorFactory;

    /**
     * @var Translation | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translatorMock;

    /**
     * @var Configuration | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationMock;

    /**
     * @var ClassnameUtilities
     */
    protected $classNameUtilities;

    /**
     * @var array
     */
    protected $baseParameters;

    /**
     * Setup before each test
     */
    public function setUp(): void
    {
        $this->translatorMock = $this->getMockBuilder(Translation::class)->disableOriginalConstructor()->getMock();
        $this->configurationMock = $this->getMockBuilder(Configuration::class)->disableOriginalConstructor()->getMock();
        $this->classNameUtilities = ClassnameUtilities::getInstance();
        $this->baseParameters = ['testParameter1' => 'testValue1'];

        $this->nodeActionGeneratorFactory = new NodeActionGeneratorFactory(
            $this->translatorMock, $this->configurationMock, $this->classNameUtilities, $this->baseParameters
        );
    }

    public function tearDown(): void
    {
        unset($this->nodeActionGeneratorFactory);
        unset($this->baseParameters);
        unset($this->classNameUtilities);
        unset($this->configurationMock);
        unset($this->translatorMock);
    }

    public function testCreateNodeActionGenerator()
    {
        $this->assertInstanceOf(
            NodeBaseActionGenerator::class, $this->nodeActionGeneratorFactory->createNodeActionGenerator()
        );
    }

    public function testCreateNodeActionGeneratorSetsTranslator()
    {
        $nodeActionGenerator = $this->nodeActionGeneratorFactory->createNodeActionGenerator();
        $this->assertEquals($this->translatorMock, $this->get_property_value($nodeActionGenerator, 'translator'));
    }

    public function testCreateNodeActionGeneratorSetsBaseParameters()
    {
        $nodeActionGenerator = $this->nodeActionGeneratorFactory->createNodeActionGenerator();
        $this->assertEquals($this->baseParameters, $this->get_property_value($nodeActionGenerator, 'baseParameters'));
    }

    public function testCreateNodeActionGeneratorAddsContentObjectTypeNodeActionGenerators()
    {
        $integrationPackages = [
            [Registration::PROPERTY_CONTEXT => 'Chamilo\Core\Repository\ContentObject\Assessment\Integration\Chamilo\Core\Repository\ContentObject\LearningPath']
        ];

        $this->configurationMock->expects($this->once())
            ->method('getIntegrationRegistrations')
            ->with(LearningPath::CONTEXT)
            ->will($this->returnValue($integrationPackages));

        $nodeActionGenerator = $this->nodeActionGeneratorFactory->createNodeActionGenerator();

        $this->assertArrayHasKey(
            'Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment',
            $this->get_property_value($nodeActionGenerator, 'contentObjectTypeNodeActionGenerators')
        );
    }

}