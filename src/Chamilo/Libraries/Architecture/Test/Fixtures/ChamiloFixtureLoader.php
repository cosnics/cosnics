<?php
namespace Chamilo\Libraries\Architecture\Test\Fixtures;

use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\SystemPathBuilder;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\PropertyAccess\StdPropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Extension on the Fixture Loader to provide a custom property accessor
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ChamiloFixtureLoader extends NativeLoader
{
    protected function createPropertyAccessor(): PropertyAccessorInterface
    {
        return new StdPropertyAccessor(
            new ChamiloPropertyAccessor(
                PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor()
            )
        );
    }

    /**
     * Seed used to generate random data. The seed is passed to the random number generator, so calling the a script
     * twice with the same seed produces the same results.
     *
     * @return int|null
     */
    protected function getSeed()
    {
        return 100;
    }

    protected function getSystemPathBuilder(): SystemPathBuilder
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(SystemPathBuilder::class);
    }

    /**
     * Loads fixture files based on an array of packages and fixture definitions.
     * e.g. ['Chamilo\Core\Repository\ContentObject\LearningPath' => ['TreeNodeData']]
     *
     * @param string[][] $packagesFixtureFiles
     *
     * @return array
     */
    public function loadFixturesFromPackages(array $packagesFixtureFiles = [])
    {
        $chamiloFixtureLoader = new ChamiloFixtureLoader();

        $createdObjects = [];

        foreach ($packagesFixtureFiles as $context => $fixtureFiles)
        {
            $basePath = $this->getSystemPathBuilder()->namespaceToFullPath($context . '\Test') . 'Fixtures/';

            foreach ($fixtureFiles as $fixtureFile)
            {
                if (strpos($fixtureFile, '.yml') === false)
                {
                    $fixtureFile = $basePath . $fixtureFile . '.yml';
                }

                $loadedObjects = $chamiloFixtureLoader->loadFile($fixtureFile, [], $createdObjects);
                $createdObjects = array_merge($createdObjects, $loadedObjects->getObjects());
            }
        }

        return $createdObjects;
    }
}