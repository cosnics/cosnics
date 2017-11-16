<?php
namespace Chamilo\Libraries\Format\DataTable;

use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataClass\DataClass;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
abstract class DataTableCellRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Utilities\StringUtilities
     */
    private $stringUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Platform\Translation
     */
    private $translationUtilities;

    /**
     *
     * @var \Chamilo\Libraries\Utilities\DatetimeUtilities
     */
    private $dateTimeUtilities;

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     * @param \Chamilo\Libraries\Platform\Translation $translationUtilities
     * @param \Chamilo\Libraries\Utilities\DatetimeUtilities $dateTimeUtilities
     */
    public function __construct(StringUtilities $stringUtilities, Translation $translationUtilities,
        DatetimeUtilities $dateTimeUtilities)
    {
        $this->stringUtilities = $stringUtilities;
        $this->translationUtilities = $translationUtilities;
        $this->dateTimeUtilities = $dateTimeUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\StringUtilities
     */
    public function getStringUtilities()
    {
        return $this->stringUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\StringUtilities $stringUtilities
     */
    public function setStringUtilities(StringUtilities $stringUtilities)
    {
        $this->stringUtilities = $stringUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Platform\Translation
     */
    public function getTranslationUtilities()
    {
        return $this->translationUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Platform\Translation $translationUtilities
     */
    public function setTranslationUtilities(Translation $translationUtilities)
    {
        $this->translationUtilities = $translationUtilities;
    }

    /**
     *
     * @return \Chamilo\Libraries\Utilities\DatetimeUtilities
     */
    public function getDateTimeUtilities()
    {
        return $this->dateTimeUtilities;
    }

    /**
     *
     * @param \Chamilo\Libraries\Utilities\DatetimeUtilities $dateTimeUtilities
     */
    public function setDateTimeUtilities(DatetimeUtilities $dateTimeUtilities)
    {
        $this->dateTimeUtilities = $dateTimeUtilities;
    }

    /**
     * Renders a single cell
     *
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     * @return string
     */
    public function renderCell($column, DataClass $dataClass)
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->getActions($dataClass);
        }

        return $dataClass->get_default_property($column->getName());
    }

    /**
     * Renders the identifier for the given dataclass
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass $dataClass
     *
     * @return int
     */
    public function renderDataIdentifier(DataClass $dataClass)
    {
        return $dataClass->getId();
    }

    /**
     *
     * @param string $property
     * @param string $className
     * @return string
     */
    public function determineColumnName($property, $className = null)
    {
        if (is_null($className))
        {
            return $property;
        }

        $classNameSlug = $this->getStringUtilities()->createString($className)->replace('\\', '_')->__toString();
        return $classNameSlug . ':' . $property;
    }
}
