<?php
namespace Chamilo\Libraries\UserInterface\Table\Factory;

use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Table\Architecture\Domain\Column\DataClassPropertyTableColumn;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Table\Factory
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataClassPropertyTableColumnFactory
{
    public function __construct(protected Translator $translator, protected StringUtilities $stringUtilities)
    {
    }

    public function getColumn(
        string $className, string $property, ?string $title = null, bool $sortable = true,
        ?array $headerCssClasses = null, ?array $contentCssClasses = null
    ): DataClassPropertyTableColumn
    {
        if (!$title) {
            $title = $this->translator->trans(
                $this->stringUtilities->createString($property)->upperCamelize()->__toString(), [], $className::CONTEXT
            );
        }

        return new DataClassPropertyTableColumn(
            $className, $property, $title, $sortable, $headerCssClasses, $contentCssClasses
        );
    }
}
