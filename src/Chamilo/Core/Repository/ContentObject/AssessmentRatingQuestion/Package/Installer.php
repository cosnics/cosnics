<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Storage\DataClass\AssessmentRatingQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\AssessmentRatingQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = AssessmentRatingQuestion::CONTEXT;
}
