<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Domain;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\MigrationTrackingParameters;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the MigrationTrackingParameters class
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class MigrationTrackingParametersTest extends ChamiloTestCase
{
    public function testGetTreeNodeAttemptConditions()
    {
        $migrationTrackingParameters = new MigrationTrackingParameters();
        $this->assertNull($migrationTrackingParameters->getTreeNodeAttemptConditions());
    }
}