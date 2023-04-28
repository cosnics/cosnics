<?php
namespace Chamilo\Core\Repository\Quota\Table\Request;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

class RequestTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{
    /**
     * @var \Chamilo\Core\Repository\Quota\Rights\Service\RightsService
     */
    private $rightsService;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    private $translator;

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
     * @return \Symfony\Component\Translation\Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function get_actions($object)
    {
        $rightsService = $this->getRightsService();
        $toolbar = new Toolbar();

        if ($rightsService->canUserViewQuotaRequests($this->get_component()->getUser()))
        {
            if (!$object->was_granted() && $rightsService->isUserIdentifierTargetForUser(
                    $object->get_user_id(), $this->get_component()->getUser()
                ))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Grant'), new FontAwesomeGlyph('play', ['text-success'], null, 'fas'),
                        $this->get_component()->get_url(
                            [
                                Manager::PARAM_ACTION => Manager::ACTION_GRANT,
                                Manager::PARAM_REQUEST_ID => $object->get_id()
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }

            if (!$object->is_pending() && $rightsService->isUserIdentifierTargetForUser(
                    $object->get_user_id(), $this->get_component()->getUser()
                ))
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Deny'), new FontAwesomeGlyph('stop', ['text-danger'], null, 'fas'),
                        $this->get_component()->get_url(
                            [
                                Manager::PARAM_ACTION => Manager::ACTION_DENY,
                                Manager::PARAM_REQUEST_ID => $object->get_id()
                            ]
                        ), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        if ($this->get_component()->get_user()->is_platform_admin() ||
            ($this->get_component()->get_user_id() == $object->get_user_id() && $object->is_pending()))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete', null, StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $this->get_component()->get_url(
                        [
                            Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                            Manager::PARAM_REQUEST_ID => $object->get_id()
                        ]
                    ), ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->render();
    }

    public function renderCell(TableColumn $column, $object): string
    {
        $calculator = new Calculator($object->get_user());

        switch ($column->get_name())
        {
            case Translation::get('User') :
                return $object->get_user()->get_fullname();
            case Request::PROPERTY_QUOTA :
                return Filesystem::format_file_size($object->get_quota());
            case Request::PROPERTY_CREATION_DATE :
                return DatetimeUtilities::getInstance()->formatLocaleDate(null, $object->get_creation_date());
            case Request::PROPERTY_DECISION_DATE :
                return DatetimeUtilities::getInstance()->formatLocaleDate(null, $object->get_decision_date());
            case Request::PROPERTY_DECISION :
                return $object->get_decision_icon();
            case Translation::get('UsedDiskSpace') :
                return Filesystem::format_file_size($calculator->getUsedUserDiskQuota());
            case Translation::get('MaximumUsedDiskSpace') :
                return Filesystem::format_file_size($calculator->getMaximumUserDiskQuota());
        }

        return parent::renderCell($column, $object);
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
