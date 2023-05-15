<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Storage\DataClass\AssessmentSelectQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentSelectQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = AssessmentSelectQuestion::CONTEXT;
}
