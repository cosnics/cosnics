<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupportInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass
 */
class Glossary extends ContentObject implements ComplexContentObjectSupportInterface
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Glossary';

    public function get_allowed_types(): array
    {
        return [GlossaryItem::class];
    }
}
