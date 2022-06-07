<?php
namespace Chamilo\Core\Metadata\Provider\Table\ProviderLink;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Provider\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Table\Relation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProviderLinkTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\Column\TableColumn $column
     * @param \Chamilo\Core\Metadata\Storage\DataClass\ProviderLink $providerLink
     * @return string
     */
    public function render_cell($column, $providerLink)
    {
        switch ($column->get_name())
        {
            case ProviderLinkTableColumnModel::PROPERTY_TYPE :
                $entity = DataClassEntityFactory::getInstance()->getEntityFromDataClassName(
                    $providerLink->get_entity_type());
                return $entity->getType();
            case ProviderLinkTableColumnModel::PROPERTY_NAME :
                $propertyNamespace = ClassnameUtilities::getInstance()->getNamespaceFromClassname(
                    $providerLink->getProviderRegistration()->get_provider_class());
                $propertyName = $providerLink->getProviderRegistration()->get_property_name();
                $propertyNameTranslationVariable = StringUtilities::getInstance()->createString($propertyName)->upperCamelize()->__toString();
                
                return Translation::get($propertyNameTranslationVariable, null, $propertyNamespace);
            case ProviderLinkTableColumnModel::PROPERTY_SCHEMA :
                return $providerLink->getElement()->getSchema()->getTranslationByIsocode(
                    Translation::getInstance()->getLanguageIsocode());
            case ProviderLinkTableColumnModel::PROPERTY_ELEMENT :
                return $providerLink->getElement()->get_display_name();
        }
        
        return parent::render_cell($column, $providerLink);
    }

    /**
     * Returns the actions toolbar
     * 
     * @param \Chamilo\Core\Metadata\Relation\Instance\Storage\DataClass\RelationInstance $providerLink
     * @return string
     */
    public function get_actions($providerLink)
    {
        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);
        
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $this->get_component()->get_url(
                    array(
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE, 
                        Manager::PARAM_PROVIDER_LINK_ID => $providerLink->get_id())), 
                ToolbarItem::DISPLAY_ICON, 
                true));
        
        return $toolbar->as_html();
    }
}