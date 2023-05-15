<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Package;

use Chamilo\Core\Repository\Common\Action\ContentObjectInstaller;
use Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass\Glossary;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Package
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class Installer extends ContentObjectInstaller
{
    public const CONTEXT = Glossary::CONTEXT;
}
