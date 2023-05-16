<?php
namespace Chamilo\Libraries\Format\Table\Column;

use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Table\Column
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataClassPropertyTableColumnFactory
{
    protected StringUtilities $stringUtilities;

    protected Translator $translator;

    public function __construct(Translator $translator, StringUtilities $stringUtilities)
    {
        $this->translator = $translator;
        $this->stringUtilities = $stringUtilities;
    }

    public function getColumn(
        string $className, string $property, ?string $title = null, bool $sortable = true,
        ?array $headerCssClasses = null, ?array $contentCssClasses = null
    ): DataClassPropertyTableColumn
    {
        if (!$title)
        {
            $context = $className::context();

            $title = $this->getTranslator()->trans(
                $this->getStringUtilities()->createString($property)->upperCamelize()->__toString(), [], $context
            );
        }

        return new DataClassPropertyTableColumn(
            $className, $property, $title, $sortable, $headerCssClasses, $contentCssClasses
        );
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}
