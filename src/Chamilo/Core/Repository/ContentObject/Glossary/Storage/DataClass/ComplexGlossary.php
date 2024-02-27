<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassVirtualExtensionInterface;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass
 */
class ComplexGlossary extends ComplexContentObjectItem implements DataClassVirtualExtensionInterface
{
    public const CONTEXT = Glossary::CONTEXT;

    public function get_allowed_types(): array
    {
        return [GlossaryItem::class];
    }
}
