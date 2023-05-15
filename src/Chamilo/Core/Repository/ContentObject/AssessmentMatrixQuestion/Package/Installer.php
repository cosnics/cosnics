<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Storage\DataClass\AssessmentMatrixQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatrixQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = AssessmentMatrixQuestion::CONTEXT;
}
