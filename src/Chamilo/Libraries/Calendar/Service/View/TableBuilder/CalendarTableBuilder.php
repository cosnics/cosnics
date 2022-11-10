<?php
namespace Chamilo\Libraries\Calendar\Service\View\TableBuilder;

use Chamilo\Core\User\Service\UserSettingService;
use Chamilo\Core\User\Storage\DataClass\User;
use HTML_Table;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service\View\Table
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class CalendarTableBuilder
{
    public const TIME_PLACEHOLDER = '__TIME__';

    protected Translator $translator;

    protected User $user;

    protected UserSettingService $userSettingService;

    public function __construct(Translator $translator, User $user, UserSettingService $userSettingService)
    {
        $this->translator = $translator;
        $this->user = $user;
        $this->userSettingService = $userSettingService;
    }

    public function render(int $displayTime, array $events, array $classes = [], ?string $dayUrlTemplate = null): string
    {
        array_unshift($classes, 'table-calendar');

        $attributes = ['class' => $classes, 'cellspacing' => 0];

        $table = new HTML_Table();
        $table->setAttributes($attributes);
        $cellMapping = $this->buildTable($table, $displayTime, $dayUrlTemplate);

        $this->addEvents($displayTime, $table, $cellMapping, $events);

        return $table->toHtml();
    }

    abstract protected function addEvents(int $displayTime, HTML_Table $table, array $cellMapping, array $events);

    abstract protected function buildTable(HTML_Table $table, int $displayTime, ?string $dayUrlTemplate = null): array;

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUserSettingService(): UserSettingService
    {
        return $this->userSettingService;
    }
}
