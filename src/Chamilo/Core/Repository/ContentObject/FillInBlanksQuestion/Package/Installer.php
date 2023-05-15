<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;

/**
 * @package Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = FillInBlanksQuestion::CONTEXT;
}
