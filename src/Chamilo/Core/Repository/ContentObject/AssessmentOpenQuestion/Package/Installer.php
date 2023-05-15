<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Storage\DataClass\AssessmentOpenQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentOpenQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = AssessmentOpenQuestion::CONTEXT;
}
