<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Storage\DataClass\AssessmentMatchNumericQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchNumericQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = AssessmentMatchNumericQuestion::CONTEXT;
}
