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
}