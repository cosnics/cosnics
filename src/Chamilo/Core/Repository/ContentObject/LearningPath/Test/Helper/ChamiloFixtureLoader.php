<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper;

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
                PropertyAccess::createPropertyAccessorBuilder()
                    ->enableMagicCall()
                    ->getPropertyAccessor()
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
}