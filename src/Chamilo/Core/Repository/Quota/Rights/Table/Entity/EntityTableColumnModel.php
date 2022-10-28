<?php
namespace Chamilo\Core\Repository\Quota\Rights\Table\Entity;

use Chamilo\Core\Repository\Quota\Rights\Storage\DataClass\RightsLocationEntityRight;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Table\ListTableRenderer;
use Symfony\Component\Translation\Translator;

class EntityTableColumnModel extends RecordTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_ENTITY_GLYPH = 'entity_glyph';
    const PROPERTY_ENTITY_DESCRIPTION = 'entity_description';
    const PROPERTY_ENTITY_TITLE = 'entity_title';
    const PROPERTY_GROUP_NAME = 'group_name';
    const PROPERTY_GROUP_PATH = 'group_path';

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @param \Chamilo\Libraries\Format\Table\ListTableRenderer $table
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function __construct(ListTableRenderer $table, Translator $translator)
    {
        $this->translator = $translator;

        parent::__construct($table);
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
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new SortableStaticTableColumn(
                RightsLocationEntityRight::PROPERTY_ENTITY_TYPE,
                $translator->trans('EntityType', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_ENTITY_TITLE,
                $translator->trans('EntityTitle', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_ENTITY_DESCRIPTION,
                $translator->trans('EntityDescription', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_GROUP_NAME, $translator->trans('Group', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );

        $this->addColumn(
            new SortableStaticTableColumn(
                self::PROPERTY_GROUP_PATH, $translator->trans('Path', [], 'Chamilo\Core\Repository\Quota\Rights')
            )
        );
    }
}
