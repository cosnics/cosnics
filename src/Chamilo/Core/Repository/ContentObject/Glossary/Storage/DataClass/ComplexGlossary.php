<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\GlossaryItem\Storage\DataClass\GlossaryItem;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Storage\DataClass\Interfaces\CompositeDataClassVirtualExtensionInterface;

/**
 * @package repository.lib.content_object.glossary
 */
class ComplexGlossary extends ComplexContentObjectItem implements CompositeDataClassVirtualExtensionInterface
{
    public const CONTEXT = Glossary::CONTEXT;

    public function get_allowed_types(): array
    {
        return [GlossaryItem::class];
    }
}
