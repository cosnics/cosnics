<?php
namespace Chamilo\Core\Repository\Quota\Component;

use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Quota\Manager;
use Chamilo\Core\Repository\Quota\Storage\DataClass\Request;
use Chamilo\Core\Repository\Quota\Storage\DataManager;
use Chamilo\Core\Repository\Quota\Table\Request\RequestTable;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Table\PropertiesTable;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 * @package Chamilo\Core\Repository\Quota\Component
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class BrowserComponent extends Manager implements TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    private $table_type;

    private $calculator;

    public function run()
    {
        $rightsService = $this->getRightsService();
        $reset_cache = (bool) \Chamilo\Libraries\Platform\Session\Request::get(self::PARAM_RESET_CACHE);
        $this->calculator = new Calculator($this->get_user(), $reset_cache);
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = array();

        $html[] = $this->render_header();

        if ($this->calculator->isEnabled() || $this->get_user()->is_platform_admin())
        {
            $html[] = $this->buttonToolbarRenderer->render();
        }

        $condition = new EqualityCondition(
            new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id())
        );
        $user_requests = DataManager::count(Request::class, new DataClassCountParameters($condition));

        if ($user_requests > 0 || $rightsService->canUserViewQuotaRequests($this->getUser()))
        {
            $tabs = new DynamicTabsRenderer('quota');

            $tabs->add_tab(
                new DynamicContentTab(
                    'personal', Translation::get('Personal'),
                    new FontAwesomeGlyph('comments', array('fa-lg'), null, 'fas'), $this->getUserQuota()
                )
            );

            if ($this->calculator->isEnabled())
            {
                if ($user_requests > 0)
                {
                    $this->table_type = RequestTable::TYPE_PERSONAL;
                    $table = new RequestTable($this, $this->getTranslator(), $rightsService);
                    $tabs->add_tab(
                        new DynamicContentTab(
                            'personal_request', Translation::get('YourRequests'),
                            new FontAwesomeGlyph('inbox', array('fa-lg'), null, 'fas'), $table->as_html()
                        )
                    );
                }
            }

            if ($rightsService->canUserViewQuotaRequests($this->getUser()))
            {
                if ($this->getUser()->is_platform_admin())
                {
                    $platform_quota = array();
                    $platform_quota[] = '<h3>' . htmlentities(Translation::get('AggregatedUserDiskQuotas')) . '</h3>';
                    $platform_quota[] = Calculator::getBar(
                        $this->calculator->getAggregatedUserDiskQuotaPercentage(),
                        Filesystem::format_file_size($this->calculator->getUsedAggregatedUserDiskQuota()) . ' / ' .
                        Filesystem::format_file_size(
                            $this->calculator->getMaximumAggregatedUserDiskQuota()
                        )
                    );
                    $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                    $platform_quota[] = '<h3>' . htmlentities(Translation::get('ReservedDiskSpace')) . '</h3>';
                    $platform_quota[] = Calculator::getBar(
                        $this->calculator->getReservedDiskSpacePercentage(),
                        Filesystem::format_file_size($this->calculator->getUsedReservedDiskSpace()) . ' / ' .
                        Filesystem::format_file_size(
                            $this->calculator->getMaximumReservedDiskSpace()
                        )
                    );
                    $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                    $platform_quota[] = '<h3>' . htmlentities(Translation::get('AllocatedDiskSpace')) . '</h3>';
                    $platform_quota[] = Calculator::getBar(
                        $this->calculator->getAllocatedDiskSpacePercentage(),
                        Filesystem::format_file_size($this->calculator->getUsedAllocatedDiskSpace()) . ' / ' .
                        Filesystem::format_file_size(
                            $this->calculator->getMaximumAllocatedDiskSpace()
                        )
                    );
                    $platform_quota[] = '<div style="clear: both;">&nbsp;</div>';

                    $tabs->add_tab(
                        new DynamicContentTab(
                            'platform', Translation::get('Platform'),
                            new FontAwesomeGlyph('tools', array('fa-lg'), null, 'fas'),
                            implode(PHP_EOL, $platform_quota)
                        )
                    );
                }

                if ($this->calculator->isEnabled())
                {
                    $target_users = $rightsService->getTargetUsersForUser($this->getUser());

                    if (count($target_users) > 0)
                    {
                        $target_condition = new InCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                            $target_users
                        );
                    }
                    else
                    {
                        $target_condition = new EqualityCondition(
                            new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                            new StaticConditionVariable(- 1)
                        );
                    }

                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                        new StaticConditionVariable(Request::DECISION_PENDING)
                    );

                    if (!$this->get_user()->is_platform_admin())
                    {
                        $conditions[] = $target_condition;
                    }

                    $condition = new AndCondition($conditions);

                    if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                    {
                        $this->table_type = RequestTable::TYPE_PENDING;
                        $table = new RequestTable($this, $this->getTranslator(), $rightsService);
                        $tabs->add_tab(
                            new DynamicContentTab(
                                RequestTable::TYPE_PENDING, Translation::get('PendingRequests'),
                                new FontAwesomeGlyph('hourglass-half', array('fa-lg'), null, 'fas'), $table->as_html()
                            )
                        );
                    }

                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                        new StaticConditionVariable(Request::DECISION_GRANTED)
                    );

                    if (!$this->get_user()->is_platform_admin())
                    {
                        $conditions[] = $target_condition;
                    }

                    $condition = new AndCondition($conditions);

                    if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                    {
                        $this->table_type = RequestTable::TYPE_GRANTED;
                        $table = new RequestTable($this, $this->getTranslator(), $rightsService);
                        $tabs->add_tab(
                            new DynamicContentTab(
                                RequestTable::TYPE_GRANTED, Translation::get('GrantedRequests'),
                                new FontAwesomeGlyph('check-square', array('fa-lg'), null, 'fas'), $table->as_html()
                            )
                        );
                    }

                    $conditions = array();
                    $conditions[] = new EqualityCondition(
                        new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                        new StaticConditionVariable(Request::DECISION_DENIED)
                    );

                    if (!$this->get_user()->is_platform_admin())
                    {
                        $conditions[] = $target_condition;
                    }

                    $condition = new AndCondition($conditions);

                    if (DataManager::count(Request::class, new DataClassCountParameters($condition)) > 0)
                    {
                        $this->table_type = RequestTable::TYPE_DENIED;
                        $table = new RequestTable($this, $this->getTranslator(), $rightsService);
                        $tabs->add_tab(
                            new DynamicContentTab(
                                RequestTable::TYPE_DENIED, Translation::get('DeniedRequests'),
                                new FontAwesomeGlyph('times-circle', array('fa-lg'), null, 'fas'), $table->as_html()
                            )
                        );
                    }
                }
            }

            $html[] = $tabs->render();
        }
        else
        {
            $html[] = $this->getUserQuota();
        }

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            if ($this->calculator->upgradeAllowed())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('UpgradeQuota'), new FontAwesomeGlyph('angle-double-up', array(), null, 'fas'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_UPGRADE))
                    )
                );
            }

            if ($this->calculator->requestAllowed())
            {
                $commonActions->addButton(
                    new Button(
                        Translation::get('RequestUpgrade'),
                        new FontAwesomeGlyph('question-circle', array(), null, 'fas'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE))
                    )
                );
            }

            if ($this->get_user()->is_platform_admin())
            {
                if ($this->calculator->isEnabled())
                {
                    $toolActions->addButton(
                        new Button(
                            Translation::get('ConfigureManagementRights'),
                            new FontAwesomeGlyph('lock', array(), null, 'fas'),
                            $this->get_url(array(self::PARAM_ACTION => self::ACTION_RIGHTS))
                        )
                    );
                }

                $toolActions->addButton(
                    new Button(
                        Translation::get('ResetTotal'), new FontAwesomeGlyph('undo', array(), null, 'fas'),
                        $this->get_url(array(self::PARAM_RESET_CACHE => 1))
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getUserQuota()
    {
        $user_quota = array();

        if ($this->calculator->isEnabled())
        {
            $user_quota[] = '<h3>' . htmlentities(Translation::get('UsedDiskSpace')) . '</h3>';
            $user_quota[] = Calculator::getBar(
                $this->calculator->getUserDiskQuotaPercentage(),
                Filesystem::format_file_size($this->calculator->getUsedUserDiskQuota()) . ' / ' .
                Filesystem::format_file_size(
                    $this->calculator->getMaximumUserDiskQuota()
                )
            );
            $user_quota[] = '<div style="clear: both;">&nbsp;</div>';
        }

        $user_quota[] = $this->get_statistics();
        $user_quota[] = '<div style="clear: both;">&nbsp;</div>';

        return implode(PHP_EOL, $user_quota);
    }

    public function get_statistics()
    {
        $html = array();
        $html[] = '<h3>' . htmlentities(Translation::get('RepositoryStatistics')) . '</h3>';

        $properties = array();
        //TODO: Make this a normal count
        $properties[Translation::get('NumberOfContentObjects')] = $this->calculator->getUsedDatabaseQuota();

        $type_counts = array();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id())
        );
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
            $properties[Translation::get('MostUsedContentObjectType')] = Translation::get(
                    'TypeName', null, ClassnameUtilities::getInstance()->getNamespaceFromClassname($most_used)
                ) . ' (' . $type_counts[$most_used] . ')';
        }

        $reference_count = $type_counts[$most_used] / 2;

        unset($type_counts[$most_used]);

        $frequent = array();

        foreach ($type_counts as $type => $count)
        {
            if ($count >= $reference_count && $count > 0)
            {
                $frequent[] = Translation::get(
                        'TypeName', null, ClassnameUtilities::getInstance()->getNamespaceFromClassname($type)
                    ) . ' (' . $count . ')';
            }
        }

        $properties[Translation::get('OtherFrequentlyUsedContentObjectTypes')] = implode('<br />', $frequent);

        $properties[Translation::get('AvailableDiskSpace')] = Filesystem::format_file_size(
            $this->calculator->getAvailableUserDiskQuota()
        );

        $condition = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->get_user_id())
        );
        $oldest_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_active_content_objects(
            ContentObject::class, new DataClassRetrievesParameters($condition)
        )->next_result();

        if ($oldest_object instanceof ContentObject)
        {
            $properties[Translation::get('OldestContentObject')] =
                '<a href="' . $this->get_parent()->get_content_object_viewing_url($oldest_object) . '">' .
                $oldest_object->get_title() . '</a> - ' .
                DatetimeUtilities::format_locale_date(null, $oldest_object->get_creation_date());
        }

        $table = new PropertiesTable($properties);
        $html[] = '<div class="quota_statistics">';
        $html[] = $table->toHTML();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see @see common\libraries.NewObjectTableSupport::get_object_table_condition()
     */
    public function get_table_condition($object_table_class_name)
    {
        $rightsService = $this->getRightsService();
        $conditions = array();

        switch ($this->table_type)
        {
            case RequestTable::TYPE_PENDING :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_PENDING)
                );
                break;
            case RequestTable::TYPE_PERSONAL :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_USER_ID),
                    new StaticConditionVariable($this->get_user_id())
                );
                break;
            case RequestTable::TYPE_GRANTED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_GRANTED)
                );
                break;
            case RequestTable::TYPE_DENIED :
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(Request::class, Request::PROPERTY_DECISION),
                    new StaticConditionVariable(Request::DECISION_DENIED)
                );
                break;
        }

        if (!$this->getUser()->is_platform_admin() && $rightsService->canUserViewQuotaRequests($this->getUser()) &&
            $this->table_type != RequestTable::TYPE_PERSONAL)
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

    public function get_table_type()
    {
        return $this->table_type;
    }
}
