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
        if (!$title) {
            $title = $this->getTranslator()->trans(
                $this->getStringUtilities()->createString($property)->upperCamelize()->__toString(), [],
                $className::CONTEXT
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
