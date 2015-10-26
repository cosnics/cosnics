<?php
namespace Chamilo\Core\Metadata\Provider\Component;

use Chamilo\Core\Metadata\Provider\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Libraries\Format\Table\SortableTableFromArray;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Relation\Service\RelationService;
use Chamilo\Core\Metadata\Element\Service\ElementService;
use Chamilo\Core\Metadata\Provider\Form\ProviderLinkForm;
use Chamilo\Core\Metadata\Provider\Service\PropertyProviderService;
use Chamilo\Libraries\Utilities\Utilities;

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
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $selectedEntity = $this->getSelectedEntity();

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
     * @return \Chamilo\Core\Metadata\Entity\EntityInterface
     */
    public function getSelectedEntity()
    {
        $entityType = $this->getRequest()->query->get(self :: PARAM_ENTITY_TYPE);

        if (is_string($entityType))
        {
            return DataClassEntityFactory :: getInstance()->getEntityFromDataClassName($entityType);
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
     *
     * @return string
     */
    public function renderEntityTypeSelectionTable()
    {
        $table = new SortableTableFromArray($this->getEntityTypeSelectionTableData());
        $table->setColumnHeader(
            0,
            Theme :: getInstance()->getImage(
                'Action/Category',
                'png',
                Translation :: get('EntityType'),
                null,
                ToolbarItem :: DISPLAY_ICON,
                false,
                'Chamilo\Configuration'));
        $table->setColumnHeader(1, Translation :: get('EntityType'));
        $table->setColumnHeader(2, '');

        $html = array();

        $html[] = $this->render_header();
        $html[] = $table->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string[]
     */
    public function getEntityTypeSelectionTableData()
    {
        $tableData = array();

        foreach ($this->getExpandedEntities() as $expandedEntity)
        {
            $actionUrl = $this->get_url(array(self :: PARAM_ENTITY_TYPE => $expandedEntity->getDataClassName()));
            $actionItem = new ToolbarItem(
                Translation :: get('SetProviderLinks'),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Metadata\Provider', 'SetProviderLinks'),
                $actionUrl,
                ToolbarItem :: DISPLAY_ICON);

            $tableData[] = array($expandedEntity->getIcon(), $expandedEntity->getType(), $actionItem->as_html());
        }

        return $tableData;
    }

    public function handleSelectedEntityType()
    {
        $entityService = new EntityService();
        $elementService = new ElementService();
        $relationService = new RelationService();

        $form = new ProviderLinkForm(
            $entityService,
            $elementService,
            $relationService,
            $this->getSelectedEntity(),
            $this->get_url());

        if ($form->validate())
        {
            $submittedValues = $form->exportValues();

            $propertyProviderService = new PropertyProviderService($this->getSelectedEntity());
            $success = $propertyProviderService->updateEntityProviderLinks(
                $entityService,
                $elementService,
                $relationService,
                $submittedValues[EntityService :: PROPERTY_METADATA_SCHEMA]);

            $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

            $message = Translation :: get(
                $translation,
                array('OBJECT' => Translation :: get('ProviderLink')),
                Utilities :: COMMON_LIBRARIES);

            $this->redirect($message, ! $success, array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self :: PARAM_ENTITY_TYPE);
    }
}
