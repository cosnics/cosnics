<?php
namespace Chamilo\Core\Repository\Quota\Table\Request;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

class RequestTable extends DataClassListTableRenderer implements TableActionsSupport
{
    public const TABLE_IDENTIFIER = Manager::PARAM_REQUEST_ID;
    public const TYPE_DENIED = 4;
    public const TYPE_GRANTED = 3;
    public const TYPE_PENDING = 2;
    public const TYPE_PERSONAL = 1;

    /**
     * @var \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

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
     * @return \Chamilo\Libraries\Format\Table\FormAction\TableActions
     */
    public function getTableActions(): TableActions
    {
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        if ($this->getRightsService()->canUserViewAllQuotaRequests($this->get_component()->getUser()))
        {
            if ($this->get_component()->get_table_type() == self::TYPE_PENDING ||
                $this->get_component()->get_table_type() == self::TYPE_DENIED)
            {
                $actions->addAction(
                    new TableAction(
                        $this->get_component()->get_url([Manager::PARAM_ACTION => Manager::ACTION_GRANT]),
                        Translation::get('GrantSelected', null, StringUtilities::LIBRARIES)
                    )
                );
            }

            if ($this->get_component()->get_table_type() == self::TYPE_PENDING)
            {
                $actions->addAction(
                    new TableAction(
                        $this->get_component()->get_url([Manager::PARAM_ACTION => Manager::ACTION_DENY]),
                        Translation::get('DenySelected', null, StringUtilities::LIBRARIES)
                    )
                );
            }
        }

        $actions->addAction(
            new TableAction(
                $this->get_component()->get_url([Manager::PARAM_ACTION => Manager::ACTION_DELETE]),
                Translation::get('RemoveSelected', null, StringUtilities::LIBRARIES)
            )
        );

        return $actions;
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
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param \Chamilo\Core\Repository\Quota\Rights\Service\RightsService $rightsService
     */
    public function setRightsService(RightsService $rightsService): void
    {
        $this->rightsService = $rightsService;
    }

    /**
     * @param \Symfony\Component\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}
