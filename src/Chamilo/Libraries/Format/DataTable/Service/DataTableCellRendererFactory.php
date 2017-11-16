<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Sven Vanpoucke <sven.vanpoucke@hogent.be>
 */
class DataTableCellRendererFactory
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
     *
     * @param string $dataTableContext
     * @param string $dataTableType
     * @return \Chamilo\Libraries\Format\DataTable\DataTableCellRenderer
     */
    public function getDataTableCellRenderer($dataTableContext, $dataTableType)
    {
        $className = $dataTableContext . '\Ajax\DataTable\Type\\' . $dataTableType . '\\' . $dataTableType .
             'DataTableCellRenderer';

        return new $className(
            $this->getStringUtilities(),
            $this->getTranslationUtilities(),
            $this->getDateTimeUtilities());
    }
}

