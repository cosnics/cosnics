<?php
namespace Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;

/**
 * @package Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass
 */
class Note extends ContentObject implements Versionable, AttachmentSupport
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Note';
}
