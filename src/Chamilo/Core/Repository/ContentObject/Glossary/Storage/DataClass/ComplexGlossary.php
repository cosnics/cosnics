<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass
 */
class ComplexGlossary extends ComplexContentObjectItem
{
    public const CONTEXT = Glossary::CONTEXT;

    public function get_allowed_types(): array
    {
        return [GlossaryItem::class];
    }
}
