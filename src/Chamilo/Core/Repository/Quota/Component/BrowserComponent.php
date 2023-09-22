<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Service\StorageSpaceCalculator;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\Repository\Quota\Storage\DataManager;
use Chamilo\Core\Repository\Quota\Table\ManagementRequestTableRenderer;
use Chamilo\Core\Repository\Quota\Table\RequestTableRenderer;
use Chamilo\Core\Repository\Quota\Table\UserRequestTableRenderer;
use Chamilo\Core\Repository\Service\ContentObjectUrlGenerator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ProgressBarRenderer;
use Chamilo\Libraries\Format\Table\PropertiesTableRenderer;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @package Chamilo\Core\Repository\Quota\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @throws \QuickformException
     * @throws \Exception
     */
    public function run()
    {
        $rightsService = $this->getRightsService();
        $this->handleCache();

        $storageSpaceCalculator = $this->getStorageSpaceCalculator();
        $translator = $this->getTranslator();

        $html = [];

        $html[] = $this->renderHeader();

        if ($storageSpaceCalculator->isStorageQuotumEnabled() || $this->getUser()->isPlatformAdmin())
        {
            $html[] = $this->getButtonToolbarRenderer()->render();
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );
        $user_requests = DataManager::count(Request::class, new DataClassCountParameters($condition));

        if ($user_requests > 0 || $rightsService->canUserViewQuotaRequests($this->getUser()))
        {
            $tabs = new TabsCollection();

            $tabs->add(
                new ContentTab(
                    'personal', $translator->trans('Personal', [], Manager::CONTEXT), $this->getUserQuota(),
                    new FontAwesomeGlyph('comments', ['fa-lg'], null, 'fas')
                )
            );

            if ($storageSpaceCalculator->isStorageQuotumEnabled())
            {
                if ($user_requests > 0)
                {
                    $totalNumberOfItems = DataManager::count(
                        Request::class,
                        new DataClassCountParameters($this->getRequestCondition(RequestTableRenderer::TYPE_PERSONAL))
                    );
                    $requestTableRenderer = $this->getUserRequestTableRenderer();

                    $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
                        $requestTableRenderer->getParameterNames(), $requestTableRenderer->getDefaultParameterValues(),
                        $totalNumberOfItems
                    );

                    $requests = DataManager::retrieves(
                        Request::class, new DataClassRetrievesParameters(
                            $this->getRequestCondition(RequestTableRenderer::TYPE_PERSONAL),
                            $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
                            $requestTableRenderer->determineOrderBy($tableParameterValues)
                        )
                    );

                    $tabs->add(
                        new ContentTab(
                            'personal_request', $translator->trans('YourRequests', [], Manager::CONTEXT),
                            $requestTableRenderer->render($tableParameterValues, $requests),
                            new FontAwesomeGlyph('inbox', ['fa-lg'], null, 'fas')
                        )
                    );
                }
            }

            if ($rightsService->canUserViewQuotaRequests($this->getUser()))
            {
                if ($this->getUser()->isPlatformAdmin())
                {
                    $filesystemTools = $this->getFilesystemTools();

                    $progressBarRenderer = $this->getProgressBarRenderer();

                    $platform_quota = [];
                    $platform_quota[] =
                        '<h3>' . htmlentities($translator->trans('AggregatedUserDiskQuotas', [], Manager::CONTEXT)) .
                        '</h3>';
                    $platform_quota[] = $progressBarRenderer->render(
                        $storageSpaceCalculator->getAggregatedUserStorageSpacePercentage(),
                        $filesystemTools->formatFileSize($storageSpaceCalculator->getUsedAggregatedUserStorageSpace()) .
                        ' / ' . $filesystemTools->formatFileSize(
                            $storageSpaceCalculator->getMaximumAggregatedUserStorageSpace()
                        )
                    );
                    $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                    $platform_quota[] =
                        '<h3>' . htmlentities($translator->trans('ReservedDiskSpace', [], Manager::CONTEXT)) . '</h3>';
                    $platform_quota[] = $progressBarRenderer->render(
                        $storageSpaceCalculator->getReservedStorageSpacePercentage(), $filesystemTools->formatFileSize(
                            $storageSpaceCalculator->getMaximumAggregatedUserStorageSpace()
                        ) . ' / ' . $filesystemTools->formatFileSize(
                            $storageSpaceCalculator->getMaximumAllocatedStorageSpace()
                        )
                    );
                    $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                    $platform_quota[] =
                        '<h3>' . htmlentities($translator->trans('AllocatedDiskSpace', [], Manager::CONTEXT)) . '</h3>';
                    $platform_quota[] = $progressBarRenderer->render(
                        $storageSpaceCalculator->getAllocatedStorageSpacePercentage(),
                        $filesystemTools->formatFileSize($storageSpaceCalculator->getUsedAggregatedUserStorageSpace()) .
                        ' / ' . $filesystemTools->formatFileSize(
                            $storageSpaceCalculator->getMaximumAllocatedStorageSpace()
                        )
                    );
                    $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                    $tabs->add(
                        new ContentTab(
                            'platform', $translator->trans('Platform', [], Manager::CONTEXT),
                            implode(PHP_EOL, $platform_quota), new FontAwesomeGlyph('tools', ['fa-lg'], null, 'fas')
                        )
                    );
                }

                if ($storageSpaceCalculator->isStorageQuotumEnabled())
                {
                    $target_users = $rightsService->getTargetUsersForUser($this->getUser());

                    if (count($target_users) > 0)
                    {
                        $target_condition = new InCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID), $target_users
                        );
                    }
                    else
                    {
                        $target_condition = new EqualityCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                            new StaticConditionVariable(- 1)
                        );
                    }

                    $conditions = [];
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                        new StaticConditionVariable(Request::DECISION_PENDING)
                    );

                    if (!$this->getUser()->isPlatformAdmin())
                    {
                        $conditions[] = $target_condition;
                    }

                    $condition = new AndCondition($conditions);

                    if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                    {
                        $tabs->add(
                            new ContentTab(
                                (string) RequestTableRenderer::TYPE_PENDING,
                                $translator->trans('PendingRequests', [], Manager::CONTEXT),
                                $this->renderManagementRequestTable(RequestTableRenderer::TYPE_PENDING),
                                new FontAwesomeGlyph('hourglass-half', ['fa-lg'], null, 'fas')
                            )
                        );
                    }

                    $conditions = [];
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                        new StaticConditionVariable(Request::DECISION_GRANTED)
                    );

                    if (!$this->getUser()->isPlatformAdmin())
                    {
                        $conditions[] = $target_condition;
                    }

                    $condition = new AndCondition($conditions);

                    if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                    {
                        $tabs->add(
                            new ContentTab(
                                (string) RequestTableRenderer::TYPE_GRANTED,
                                $translator->trans('GrantedRequests', [], Manager::CONTEXT),
                                $this->renderManagementRequestTable(RequestTableRenderer::TYPE_GRANTED),
                                new FontAwesomeGlyph('check-square', ['fa-lg'], null, 'fas')
                            )
                        );
                    }

                    $conditions = [];
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                        new StaticConditionVariable(Request::DECISION_DENIED)
                    );

                    if (!$this->getUser()->isPlatformAdmin())
                    {
                        $conditions[] = $target_condition;
                    }

                    $condition = new AndCondition($conditions);

                    if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                    {
                        $tabs->add(
                            new ContentTab(
                                (string) RequestTableRenderer::TYPE_DENIED,
                                $translator->trans('DeniedRequests', [], Manager::CONTEXT),
                                $this->renderManagementRequestTable(RequestTableRenderer::TYPE_DENIED),
                                new FontAwesomeGlyph('times-circle', ['fa-lg'], null, 'fas')
                            )
                        );
                    }
                }
            }

            $html[] = $this->getTabsRenderer()->render('quota', $tabs);
        }
        else
        {
            $html[] = $this->getUserQuota();
        }

        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    protected function getAggregatedUserStorageSpaceCacheAdapter(): AdapterInterface
    {
        return $this->getService('Chamilo\Core\Repository\Quota\Service\CachedAggregatedUserStorageSpaceCacheAdapter');
    }

    /**
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     */
    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();

            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            if ($this->getRightsService()->canUserUpgradeStorageSpace($this->getUser()))
            {
                $commonActions->addButton(
                    new Button(
                        $translator->trans('UpgradeQuota', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('angle-double-up', [], null, 'fas'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_UPGRADE])
                    )
                );
            }

            if ($this->getRightsService()->canUserRequestAdditionalStorageSpace($this->getUser()))
            {
                $commonActions->addButton(
                    new Button(
                        $translator->trans('RequestUpgrade', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('question-circle', [], null, 'fas'),
                        $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE])
                    )
                );
            }

            if ($this->getUser()->isPlatformAdmin())
            {
                if ($this->getStorageSpaceCalculator()->isStorageQuotumEnabled())
                {
                    $toolActions->addButton(
                        new Button(
                            $translator->trans('ConfigureManagementRights', [], Manager::CONTEXT),
                            new FontAwesomeGlyph('lock', [], null, 'fas'),
                            $this->get_url([self::PARAM_ACTION => self::ACTION_RIGHTS])
                        )
                    );
                }

                $toolActions->addButton(
                    new Button(
                        $translator->trans('ResetTotal', [], Manager::CONTEXT),
                        new FontAwesomeGlyph('undo', [], null, 'fas'), $this->get_url([self::PARAM_RESET_CACHE => 1])
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    protected function getContentObjectUrlGenerator(): ContentObjectUrlGenerator
    {
        return $this->getService(ContentObjectUrlGenerator::class);
    }

    protected function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->getService(DatetimeUtilities::class);
    }

    public function getManagementRequestTableRenderer(): ManagementRequestTableRenderer
    {
        return $this->getService(ManagementRequestTableRenderer::class);
    }

    protected function getProgressBarRenderer(): ProgressBarRenderer
    {
        return $this->getService(ProgressBarRenderer::class);
    }

    protected function getPropertiesTableRenderer(): PropertiesTableRenderer
    {
        return $this->getService(PropertiesTableRenderer::class);
    }

    /**
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @throws \Exception
     */
    public function getRequestCondition(int $requestType): AndCondition
    {
        $rightsService = $this->getRightsService();
        $conditions = [];

        switch ($requestType)
        {
            case RequestTableRenderer::TYPE_PENDING :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_PENDING)
                );
                break;
            case RequestTableRenderer::TYPE_PERSONAL :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                    new StaticConditionVariable($this->getUser()->getId())
                );
                break;
            case RequestTableRenderer::TYPE_GRANTED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_GRANTED)
                );
                break;
            case RequestTableRenderer::TYPE_DENIED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_DENIED)
                );
                break;
        }

        if (!$this->getUser()->isPlatformAdmin() && $rightsService->canUserViewQuotaRequests($this->getUser()) &&
            $requestType != RequestTableRenderer::TYPE_PERSONAL)
        {
            $target_users = $rightsService->getTargetUsersForUser($this->getUser());

            if (count($target_users) > 0)
            {
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID), $target_users
                );
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                    new StaticConditionVariable(- 1)
                );
            }
        }

        return new AndCondition($conditions);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    protected function getStorageSpaceCalculator(): StorageSpaceCalculator
    {
        return $this->getService(StorageSpaceCalculator::class);
    }

    protected function getTabsRenderer(): TabsRenderer
    {
        return $this->getService(TabsRenderer::class);
    }

    /**
     * @throws \TableException
     */
    public function getUserQuota(): string
    {
        $user_quota = [];
        $storageSpaceCalculator = $this->getStorageSpaceCalculator();
        $filesystemTools = $this->getFilesystemTools();

        if ($storageSpaceCalculator->isStorageQuotumEnabled())
        {
            $user_quota[] =
                '<h3>' . htmlentities($this->getTranslator()->trans('UsedDiskSpace', [], Manager::CONTEXT)) . '</h3>';
            $user_quota[] = $this->getProgressBarRenderer()->render(
                $storageSpaceCalculator->getStorageSpacePercentageForUser($this->getUser()),
                $filesystemTools->formatFileSize(
                    $storageSpaceCalculator->getUsedStorageSpaceForUser($this->getUser())
                ) . ' / ' . $filesystemTools->formatFileSize(
                    $storageSpaceCalculator->getAllowedStorageSpaceForUser($this->getUser())
                )
            );
            $user_quota[] = '<div style="clear: both;">&nbsp;</div>';
        }

        $user_quota[] = $this->renderStatistics();
        $user_quota[] = '<div style="clear: both;">&nbsp;</div>';

        return implode(PHP_EOL, $user_quota);
    }

    protected function getUserRequestTableRenderer(): UserRequestTableRenderer
    {
        return $this->getService(UserRequestTableRenderer::class);
    }

    protected function getUserStorageSpaceCacheAdapter(): AdapterInterface
    {
        return $this->getService('Chamilo\Core\Repository\Quota\Service\CachedUserStorageSpaceCacheAdapter');
    }

    protected function handleCache()
    {
        $resetCache = $this->getRequest()->query->get(self::PARAM_RESET_CACHE, false);

        if ($resetCache)
        {
            $this->getUserStorageSpaceCacheAdapter()->clear();
            $this->getAggregatedUserStorageSpaceCacheAdapter()->clear();
        }
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Chamilo\Libraries\Rights\Exception\RightsLocationNotFoundException
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderManagementRequestTable(int $requestType): string
    {
        $totalNumberOfItems = DataManager::count(
            Request::class, new DataClassCountParameters($this->getRequestCondition($requestType))
        );
        $requestTableRenderer = $this->getUserRequestTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $requestTableRenderer->getParameterNames(), $requestTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $requests = DataManager::retrieves(
            Request::class, new DataClassRetrievesParameters(
                $this->getRequestCondition($requestType), $tableParameterValues->getNumberOfItemsPerPage(),
                $tableParameterValues->getOffset(), $requestTableRenderer->determineOrderBy($tableParameterValues)
            )
        );

        return $requestTableRenderer->render($tableParameterValues, $requests);
    }

    /**
     * @throws \TableException
     */
    public function renderStatistics(): string
    {
        $translator = $this->getTranslator();
        $storageSpaceCalculator = $this->getStorageSpaceCalculator();

        $html = [];
        $html[] = '<h3>' . htmlentities($translator->trans('RepositoryStatistics', [], Manager::CONTEXT)) . '</h3>';

        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );

        $properties = [];
        $properties[$translator->trans('NumberOfContentObjects', [], Manager::CONTEXT)] =
            \Chamilo\Core\Repository\Storage\DataManager::count_active_content_objects(
                ContentObject::class, new DataClassCountParameters($condition)
            );

        $type_counts = [];

        $most_used = null;

        foreach (\Chamilo\Core\Repository\Storage\DataManager::get_registered_types() as $type)
        {
            $type_counts[$type] = \Chamilo\Core\Repository\Storage\DataManager::count_active_content_objects(
                $type, new DataClassCountParameters($condition)
            );
            if ($type_counts[$type] > $type_counts[$most_used])
            {
                $most_used = $type;
            }
        }

        arsort($type_counts);

        if ($most_used)
        {
            $properties[$translator->trans('MostUsedContentObjectType', [], Manager::CONTEXT)] = $translator->trans(
                    'TypeName', [], $this->getClassnameUtilities()->getNamespaceFromClassname($most_used)
                ) . ' (' . $type_counts[$most_used] . ')';
        }

        $reference_count = $type_counts[$most_used] / 2;

        unset($type_counts[$most_used]);

        $frequent = [];

        foreach ($type_counts as $type => $count)
        {
            if ($count >= $reference_count && $count > 0)
            {
                $frequent[] = $translator->trans(
                        'TypeName', [], $this->getClassnameUtilities()->getNamespaceFromClassname($type)
                    ) . ' (' . $count . ')';
            }
        }

        $properties[$translator->trans('OtherFrequentlyUsedContentObjectTypes', [], Manager::CONTEXT)] =
            implode('<br />', $frequent);

        $properties[$translator->trans('AvailableDiskSpace', [], Manager::CONTEXT)] =
            $this->getFilesystemTools()->formatFileSize(
                $storageSpaceCalculator->getAvailableStorageSpaceForUser($this->getUser())
            );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );
        $oldest_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_active_content_objects(
            ContentObject::class, new DataClassRetrievesParameters($condition)
        )->current();

        if ($oldest_object instanceof ContentObject)
        {
            $properties[$translator->trans('OldestContentObject', [], Manager::CONTEXT)] =
                '<a href="' . $this->getContentObjectUrlGenerator()->getViewUrl($oldest_object) . '">' .
                $oldest_object->get_title() . '</a> - ' .
                $this->getDatetimeUtilities()->formatLocaleDate(null, $oldest_object->get_creation_date());
        }

        $html[] = '<div class="quota_statistics">';
        $html[] = $this->getPropertiesTableRenderer()->render($properties);
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
