<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\DropDownButtonCollection;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;
use QuickformException;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class JumpBarRenderer
{
    public function __construct(protected Translator $translator, protected ButtonToolBarRenderer $buttonToolBarRenderer
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function render(string $navigationUrl, int $currentTime): string
    {
        try {
            return $this->buttonToolBarRenderer->render($this->getButtonToolBar($navigationUrl, $currentTime));
        }
        catch (QuickformException) {
            return '';
        }
    }

    private function getButtonToolBar(string $navigationUrl, int $currentTime): ButtonToolBar
    {
        $buttonToolBar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolBar->addButton(
            new Button($this->translator->trans('JumpTo', [], StringUtilities::LIBRARIES), null, null,
                DisplayTypeEnum::LABEL, null, ['btn-link'])
        );
        $buttonToolBar->addButton($buttonGroup);

        $dateButton = new DropDownButtonCollection(date('j', $currentTime));

        foreach ($this->getDays($currentTime) as $day) {
            $dayUrl = str_replace(
                CalendarTableBuilder::TIME_PLACEHOLDER,
                (string) mktime(0, 0, 0, (int) date('n', $currentTime), $day, (int) date('Y', $currentTime)),
                $navigationUrl
            );

            $isActive = date('j', $currentTime) == $day;
            $dateButton->addButton(
                new SubButton((string) $day, null, $dayUrl, DisplayTypeEnum::LABEL, null, [], null, $isActive)
            );
        }

        $months = $this->getMonths();
        $monthButton = new DropDownButtonCollection($months[date('n', $currentTime)]);

        foreach ($this->getMonths() as $month => $monthLabel) {
            $monthUrl = str_replace(
                CalendarTableBuilder::TIME_PLACEHOLDER, (string) mktime(
                0, 0, 0, $month, (int) date('j', $currentTime), (int) date('Y', $currentTime)
            ), $navigationUrl
            );

            $isActive = date('n', $currentTime) == $month;
            $monthButton->addButton(
                new SubButton($monthLabel, null, $monthUrl, DisplayTypeEnum::LABEL, null, [], null, $isActive)
            );
        }

        $yearButton = new DropDownButtonCollection(date('Y', $currentTime));

        foreach ($this->getYears($currentTime) as $year) {
            $yearUrl = str_replace(
                CalendarTableBuilder::TIME_PLACEHOLDER,
                (string) mktime(0, 0, 0, (int) date('n', $currentTime), (int) date('j', $currentTime), $year),
                $navigationUrl
            );

            $isActive = date('Y', $currentTime) == $year;
            $yearButton->addButton(
                new SubButton((string) $year, null, $yearUrl, DisplayTypeEnum::LABEL, null, [], null, $isActive)
            );
        }

        $buttonGroup->addButton($dateButton);
        $buttonGroup->addButton($monthButton);
        $buttonGroup->addButton($yearButton);

        return $buttonToolBar;
    }

    /**
     * @return int[]
     */
    public function getDays(int $currentTime): array
    {
        $numberDays = date('t', $currentTime);
        $days = [];

        for ($i = 1; $i <= $numberDays; $i ++) {
            $days[$i] = $i;
        }

        return $days;
    }

    /**
     * @return string[]
     */
    public function getMonths(): array
    {
        $monthNames = [
            $this->translator->trans('JanuaryLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('FebruaryLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('MarchLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('AprilLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('MayLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('JuneLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('JulyLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('AugustLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('SeptemberLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('OctoberLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('NovemberLong', [], StringUtilities::LIBRARIES),
            $this->translator->trans('DecemberLong', [], StringUtilities::LIBRARIES)
        ];

        $months = [];

        foreach ($monthNames as $key => $month) {
            $months[$key + 1] = $month;
        }

        return $months;
    }

    /**
     * @return int[]
     */
    public function getYears(int $currentTime): array
    {
        $year = (int) date('Y', $currentTime);
        $years = [];

        for ($i = $year - 5; $i <= $year + 5; $i ++) {
            $years[$i] = $i;
        }

        return $years;
    }
}
