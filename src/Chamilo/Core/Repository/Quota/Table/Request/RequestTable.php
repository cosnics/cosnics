<?php
namespace Chamilo\Core\Repository\Quota\Table\Request;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Format\Table\FormAction\TableFormActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

class RequestTable extends DataClassTable implements TableActionsSupport
{
    const TABLE_IDENTIFIER = Manager::PARAM_REQUEST_ID;
    const TYPE_PERSONAL = 1;
    const TYPE_PENDING = 2;
    const TYPE_GRANTED = 3;
    const TYPE_DENIED = 4;

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
     * @return \Chamilo\Core\Repository\Quota\Table\Request\RequestTableCellRenderer|\Chamilo\Libraries\Format\Table\TableCellRenderer
     * @throws \Exception
     */
    public function getTableCellRenderer(): RequestTableCellRenderer
    {
        if (!isset($this->cellRenderer))
        {
            $this->cellRenderer =
                new RequestTableCellRenderer($this, $this->getTranslator(), $this->getRightsService());
        }

        return $this->cellRenderer;
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableFormActions
     */
    public function getTableActions(): TableFormActions
    {
        $actions = new TableFormActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->getRightsService()->canUserViewAllQuotaRequests($this->get_component()->getUser()))
        {
            if ($this->get_component()->get_table_type() == self::TYPE_PENDING ||
                $this->get_component()->get_table_type() == self::TYPE_DENIED)
            {
                $actions->add_form_action(
                    new TableFormAction(
                        $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_GRANT)),
                        Translation::get('GrantSelected', null, StringUtilities::LIBRARIES)
                    )
                );
            }

            if ($this->get_component()->get_table_type() == self::TYPE_PENDING)
            {
                $actions->add_form_action(
                    new TableFormAction(
                        $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DENY)),
                        Translation::get('DenySelected', null, StringUtilities::LIBRARIES)
                    )
                );
            }
        }

        $actions->add_form_action(
            new TableFormAction(
                $this->get_component()->get_url(array(Manager::PARAM_ACTION => Manager::ACTION_DELETE)),
                Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }
}
