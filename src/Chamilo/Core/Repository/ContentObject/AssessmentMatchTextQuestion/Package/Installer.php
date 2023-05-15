<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Storage\DataClass\AssessmentMatchTextQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentMatchTextQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = AssessmentMatchTextQuestion::CONTEXT;
}
