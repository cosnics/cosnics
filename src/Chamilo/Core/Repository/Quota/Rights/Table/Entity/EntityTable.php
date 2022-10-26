<?php
namespace Chamilo\Core\Repository\Quota\Rights\Table\Entity;

use Chamilo\Core\Repository\Manager as RepositoryManager;
use Chamilo\Core\Repository\Quota\Manager as QuotaManager;
use Chamilo\Core\Repository\Quota\Rights\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Table\Extension\RecordTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Rights\Table\Entity
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntityTable extends RecordTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

    /**
     * @var \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    private $rightsService;

    /**
     * @param $component
     * @param \Symfony\Component\Translation\Translator $translator
     * @param \Chamilo\Core\Repository\Quota\Rights\Service\RightsService $rightsService
     *
     * @throws \Exception
     */
    public function __construct(
        $component, Translator $translator, RightsService $rightsService
    )
    {
        $this->translator = $translator;
        $this->rightsService = $rightsService;

        parent::__construct($component);
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
     * @return \Chamilo\Core\Repository\Quota\Rights\Table\Entity\EntityTableDataProvider
     */
    public function getTableDataProvider(): EntityTableDataProvider
    {
        if (!isset($this->dataProvider))
        {
            $this->dataProvider = new EntityTableDataProvider($this, $this->getRightsService());
        }

        return $this->dataProvider;
    }

    /**
     * @return \Chamilo\Core\Repository\Quota\Rights\Table\Entity\EntityTableCellRenderer|\Chamilo\Libraries\Format\Table\TableCellRenderer
     * @throws \Exception
     */
    public function getTableCellRenderer(): EntityTableCellRenderer
    {
        if (!isset($this->cellRenderer))
        {
            $this->cellRenderer = new EntityTableCellRenderer($this, $this->getTranslator(), $this->getRightsService());
        }

        return $this->cellRenderer;
    }

    /**
     * Gets the table's column model or builds one if it is not set
     *
     * @return \Chamilo\Libraries\Format\Table\TableColumnModel
     */
    public function getTableColumnModel(): EntityTableColumnModel
    {
        if (!isset($this->columnModel))
        {
            $this->columnModel = new EntityTableColumnModel($this, $this->getTranslator());
        }

        return $this->columnModel;
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function getTableActions(): TableFormActions
    {
        $deleteUrl = new Redirect(
            array(
                RepositoryManager::PARAM_CONTEXT => RepositoryManager::package(),
                RepositoryManager::PARAM_ACTION => RepositoryManager::ACTION_QUOTA,
                QuotaManager::PARAM_ACTION => QuotaManager::ACTION_RIGHTS,
                Manager::PARAM_ACTION => Manager::ACTION_DELETE
            )
        );

        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);
        $actions->add_form_action(
            new TableFormAction(
                $deleteUrl->getUrl(), $this->getTranslator()->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }
}
