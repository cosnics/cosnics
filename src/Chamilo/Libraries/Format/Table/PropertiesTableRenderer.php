<?php
namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Utilities\StringUtilities;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PropertiesTableRenderer
{
    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @throws \TableException
     */
    public function render(array $properties): string
    {
        $htmlTable = new HTML_Table(['class' => 'table table-striped table-bordered table-hover table-responsive']);

        if (count($properties) > 0)
        {
            foreach ($properties as $property => $values)
            {
                $contents = [];

                $contents[] = $property;

                if (!is_array($values))
                {
                    $values = [$values];
                }

                if (count($values) > 0)
                {
                    foreach ($values as $value)
                    {
                        $contents[] = $value;
                    }
                }

                $htmlTable->addRow($contents);
            }

            $htmlTable->setColAttributes(0, ['class' => 'header', 'style' => 'vertical-align: middle;']);
        }
        else
        {
            $rowNumber =
                $htmlTable->addRow([$this->getTranslator()->trans('NoResults', [], StringUtilities::LIBRARIES)]);
            $htmlTable->setCellAttributes($rowNumber, 0, 'style="font-style: italic;text-align:center;" colspan=2');
        }

        return $htmlTable->toHtml();
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}

