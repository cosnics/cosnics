<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\MigrationTrackingParameters;
use Chamilo\Libraries\Architecture\Test\Test;

/**
 * Tests the MigrationTrackingParameters class
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class MigrationTrackingParametersTest extends Test
{
    public function testGetTreeNodeAttemptConditions()
    {
        $migrationTrackingParameters = new MigrationTrackingParameters();
        $this->assertNull($migrationTrackingParameters->getTreeNodeAttemptConditions());
    }
}