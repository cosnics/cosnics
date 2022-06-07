<?php
namespace Chamilo\Core\Metadata\Provider\Component;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Provider\Form\ProviderLinkForm;
use Chamilo\Core\Metadata\Provider\Manager;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\SortableStaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Metadata\Provider\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ConfigurerComponent extends Manager
{

    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $selectedEntity = $this->getSelectedEntity();
        $this->set_parameter(self::PARAM_ENTITY_TYPE, $selectedEntity);

        if ($selectedEntity instanceof DataClassEntity)
        {
            return $this->handleSelectedEntityType();
        }
        else
        {
            return $this->renderEntityTypeSelectionTable();
        }
    }

    /**
     *
     * @return string[]
     */
    public function getEntityTypeSelectionTableData()
    {
        $tableData = [];

        foreach ($this->getExpandedEntities() as $expandedEntity)
        {
            $actionUrl = $this->get_url(array(self::PARAM_ENTITY_TYPE => $expandedEntity->getDataClassName()));
            $actionItem = new ToolbarItem(
                Translation::get('SetProviderLinks'), new FontAwesomeGlyph('cog', [], null, 'fas'), $actionUrl,
                ToolbarItem::DISPLAY_ICON
            );

            $tableData[] = array($expandedEntity->getIcon(), $expandedEntity->getType(), $actionItem->as_html());
        }

        return $tableData;
    }

    /**
     * @return \Chamilo\Core\Metadata\Provider\Service\PropertyProviderService
     */
    public function getPropertyProviderService()
    {
        return $this->getService(PropertyProviderService::class);
    }
    
    /**
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface
     * @throws \Exception
     */
    public function getSelectedEntity()
    {
        $entityType = $this->getRequest()->query->get(self::PARAM_ENTITY_TYPE);

        if (is_string($entityType))
        {
            return $this->getService(DataClassEntityFactory::class)->getEntityFromDataClassName($entityType);
        }
        else
        {
            $expandedEntities = $this->getExpandedEntities();

            if (count($expandedEntities) == 1)
            {
                return array_pop($expandedEntities);
            }
        }
    }

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     */
    public function handleSelectedEntityType()
    {
        $form = new ProviderLinkForm($this->getSelectedEntity(), $this->get_url());

        if ($form->validate())
        {
            $submittedValues = $form->exportValues();

            $success = $this->getPropertyProviderService()->updateEntityProviderLinks(
                $this->getSelectedEntity(), $submittedValues[EntityService::PROPERTY_METADATA_SCHEMA]
            );

            $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

            $message = Translation::get(
                $translation, array('OBJECT' => Translation::get('ProviderLink')), StringUtilities::LIBRARIES
            );

            $this->redirect($message, !$success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @return string
     */
    public function renderEntityTypeSelectionTable()
    {
        $headers = [];

        $glyph = new FontAwesomeGlyph('folder', [], Translation::get('EntityType'), 'fas');

        $headers[] = new SortableStaticTableColumn($glyph->render());
        $headers[] = new SortableStaticTableColumn(Translation::get('EntityType'));
        $headers[] = new StaticTableColumn('');

        $table = new SortableTableFromArray($this->getEntityTypeSelectionTableData(), $headers);

        $html = [];

        $html[] = $this->render_header();
        $html[] = $table->toHtml();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }
}
