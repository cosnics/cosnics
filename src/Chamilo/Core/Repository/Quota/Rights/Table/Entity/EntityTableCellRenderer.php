<?php
namespace Chamilo\Core\Repository\Quota\Rights\Table\Entity;

use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRightGroup;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Utilities\Utilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Table\Entity
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    private $rightsService;

    /**
     * @param $table
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Repository\Quota\Rights\Service\RightsService $rightsService
     *
     * @throws \Exception
     */
    public function __construct($table, Translator $translator, RightsService $rightsService)
    {
        parent::__construct($table);

        $this->translator = $translator;
        $this->rightsService = $rightsService;
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    public function getRightsService(): RightsService
    {
        return $this->rightsService;
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param string[] $record
     *
     * @return string
     */
    public function get_actions($record)
    {
        $toolbar = new Toolbar();

        if ($this->get_component()->getUser()->is_platform_admin())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $this->getTranslator()->trans('Delete', [], Utilities::COMMON_LIBRARIES),
                    new FontAwesomeGlyph('times'), $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID => $record[RightsLocationEntityRightGroup::PROPERTY_ID]
                    )
                ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }

    /**
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param string[] $record
     *
     * @return string
     */
    public function render_cell($column, $record)
    {
        switch ($column->get_name())
        {
            case RightsLocationEntityRight::PROPERTY_ENTITY_TYPE :
                $glyph = $record[EntityTableColumnModel::PROPERTY_ENTITY_GLYPH];

                return $glyph->render();
        }

        return parent::render_cell($column, $record);
    }
}
