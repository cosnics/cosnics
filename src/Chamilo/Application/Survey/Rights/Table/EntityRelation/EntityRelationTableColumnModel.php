<?php
namespace Chamilo\Application\Survey\Rights\Table\EntityRelation;

use Chamilo\Application\Survey\Service\RightsService;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

class EntityRelationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const COLUMN_ENTITY = 'entity';

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableColumnModel::initialize_columns()
     */
    public function initialize_columns()
    {
        $publication = $this->get_component()->getCurrentPublication();
        
        $this->add_column(new StaticTableColumn(self :: COLUMN_ENTITY));
        
        if ($publication->getId() == 0)
        {
            $this->add_column(
                new StaticTableColumn(
                    RightsService :: RIGHT_PUBLISH, 
                    $this->getRightIcon(RightsService :: RIGHT_PUBLISH, 'PublishRight')));
        }
        else
        {
            $this->add_column(
                new StaticTableColumn(
                    RightsService :: RIGHT_TAKE, 
                    $this->getRightIcon(RightsService :: RIGHT_TAKE, 'TakeRight')));
            $this->add_column(
                new StaticTableColumn(
                    RightsService :: RIGHT_MAIL, 
                    $this->getRightIcon(RightsService :: RIGHT_MAIL, 'MailRight')));
            $this->add_column(
                new StaticTableColumn(
                    RightsService :: RIGHT_REPORT, 
                    $this->getRightIcon(RightsService :: RIGHT_REPORT, 'ReportRight')));
            $this->add_column(
                new StaticTableColumn(
                    RightsService :: RIGHT_MANAGE, 
                    $this->getRightIcon(RightsService :: RIGHT_MANAGE, 'ManageRight')));
        }
    }

    /**
     *
     * @param integer $right
     * @param string $translationVariable
     * @return string
     */
    private function getRightIcon($right, $translationVariable)
    {
        return Theme :: getInstance()->getImage(
            'Rights/' . $right, 
            'png', 
            Translation :: get($translationVariable), 
            null, 
            ToolbarItem :: DISPLAY_ICON, 
            false, 
            \Chamilo\Application\Survey\Manager :: context());
    }
}
