<?php
namespace Chamilo\Core\Repository\Workspace\Rights\Table\EntityRelation;

use Chamilo\Core\Repository\Workspace\Rights\Manager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;

class EntityRelationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    public const COLUMN_ENTITY = 'entity';

    /**
     * @param int $right
     * @param string $translationVariable
     *
     * @return string
     */
    private function getRightIcon($right, $translationVariable)
    {
        switch ($right)
        {
            case RightsService::RIGHT_VIEW:
                $glyphName = 'desktop';
                break;
            case RightsService::RIGHT_ADD:
                $glyphName = 'plus';
                break;
            case RightsService::RIGHT_EDIT:
                $glyphName = 'pencil-alt';
                break;
            case RightsService::RIGHT_DELETE:
                $glyphName = 'trash-alt';
                break;
            case RightsService::RIGHT_USE:
                $glyphName = 'share-square';
                break;
            case RightsService::RIGHT_COPY:
                $glyphName = 'copy';
                break;
            case RightsService::RIGHT_MANAGE:
                $glyphName = 'cog';
                break;
            default:
                $glyphName = 'lock';
        }

        $glyph = new FontAwesomeGlyph($glyphName, [], Translation::get($translationVariable), 'fas');

        return $glyph->render();
    }

    /**
     * @see \Chamilo\Libraries\Format\Table\TableColumnModel::initializeColumns()
     */
    public function initializeColumns()
    {
        $this->addColumn(
            new StaticTableColumn(
                self::COLUMN_ENTITY, Translation::getInstance()->getTranslation('Entity', null, Manager::context())
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                RightsService::RIGHT_VIEW, $this->getRightIcon(RightsService::RIGHT_VIEW, 'ViewRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(RightsService::RIGHT_ADD, $this->getRightIcon(RightsService::RIGHT_ADD, 'AddRight'))
        );
        $this->addColumn(
            new StaticTableColumn(
                RightsService::RIGHT_EDIT, $this->getRightIcon(RightsService::RIGHT_EDIT, 'EditRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                RightsService::RIGHT_DELETE, $this->getRightIcon(RightsService::RIGHT_DELETE, 'DeleteRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(RightsService::RIGHT_USE, $this->getRightIcon(RightsService::RIGHT_USE, 'UseRight'))
        );
        $this->addColumn(
            new StaticTableColumn(
                RightsService::RIGHT_COPY, $this->getRightIcon(RightsService::RIGHT_COPY, 'CopyRight')
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                RightsService::RIGHT_MANAGE, $this->getRightIcon(RightsService::RIGHT_MANAGE, 'ManageRight')
            )
        );
    }
}
