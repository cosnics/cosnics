<?php
namespace Chamilo\Core\Repository\Quota\Table;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Rights\Service\RightsService;
use Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Quota\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ManagementRequestTableRenderer extends RequestTableRenderer
{
    public const PROPERTY_CURRENTLY_USED_DISK_SPACE = 'CurrentlyUsedDiskSpace';
    public const PROPERTY_MAXIMUM_USED_DISK_SPACE = 'MaximumUsedDiskSpace';
    public const PROPERTY_USER = 'User';

    protected StorageSpaceCalculator $storageSpaceCalculator;

    public function __construct(
        Translator $translator, UrlGenerator $urlGenerator, ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        RightsService $rightsService, DatetimeUtilities $datetimeUtilities, User $user,
        StorageSpaceCalculator $storageSpaceCalculator
    )
    {
        $this->storageSpaceCalculator = $storageSpaceCalculator;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $rightsService, $datetimeUtilities, $user
        );
    }

    public function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->storageSpaceCalculator;
    }

    protected function initializeColumns()
    {
        parent::initializeColumns();

        $translator = $this->getTranslator();

        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_USER, $translator->trans(self::PROPERTY_USER, [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_CURRENTLY_USED_DISK_SPACE,
                $translator->trans(self::PROPERTY_CURRENTLY_USED_DISK_SPACE, [], Manager::CONTEXT)
            )
        );
        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_MAXIMUM_USED_DISK_SPACE,
                $translator->trans(self::PROPERTY_MAXIMUM_USED_DISK_SPACE, [], Manager::CONTEXT)
            )
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $request): string
    {
        $storageSpaceCalculator = $this->getStorageSpaceCalculator();
        $user = $request->get_user();

        switch ($column->get_name())
        {
            case self::PROPERTY_USER :
                return $request->get_user()->get_fullname();
            case self::PROPERTY_CURRENTLY_USED_DISK_SPACE :
                return Filesystem::format_file_size($storageSpaceCalculator->getUsedStorageSpaceForUser($user));
            case self::PROPERTY_MAXIMUM_USED_DISK_SPACE:
                return Filesystem::format_file_size($storageSpaceCalculator->getAllowedStorageSpaceForUser($user));
        }

        return parent::renderCell($column, $resultPosition, $request);
    }

}