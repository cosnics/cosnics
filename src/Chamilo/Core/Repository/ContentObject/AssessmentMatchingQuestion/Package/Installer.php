<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Storage\DataClass\AssessmentMatchingQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchingQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = AssessmentMatchingQuestion::CONTEXT;
}
