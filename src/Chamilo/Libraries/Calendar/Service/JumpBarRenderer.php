<?php
namespace Chamilo\Libraries\Calendar\Service;

use Chamilo\Libraries\Calendar\Service\TableBuilder\CalendarTableBuilder;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\Button;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonGroup;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\ButtonToolBar;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\DropDownButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Domain\SubButton;
use Chamilo\Libraries\UserInterface\ActionBar\Architecture\Interface\ButtonDisplayInterface;
use Chamilo\Libraries\UserInterface\ActionBar\Service\ButtonToolBarRenderer;
use QuickformException;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Calendar\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class JumpBarRenderer
{

    private Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function render(string $navigationUrl, int $currentTime): string
    {
        try
        {
            return $this->getButtonToolBarRenderer($navigationUrl, $currentTime)->render();
        }
        catch (QuickformException)
        {
            return '';
        }
    }

    private function getButtonToolBarRenderer(string $navigationUrl, int $currentTime): ButtonToolBarRenderer
    {
        $buttonToolbar = new ButtonToolBar();
        $buttonGroup = new ButtonGroup();

        $buttonToolbar->addButton(
            new Button($this->getTranslator()->trans('JumpTo', [], StringUtilities::LIBRARIES), null, null,
                ButtonDisplayInterface::DISPLAY_LABEL, null, ['btn-link'])
        );
        $buttonToolbar->addButton($buttonGroup);

        $dateButton = new DropDownButton(date('j', $currentTime));

        foreach ($this->getDays($currentTime) as $day)
        {
            $dayUrl = str_replace(
                CalendarTableBuilder::TIME_PLACEHOLDER,
                (string) mktime(0, 0, 0, (int) date('n', $currentTime), $day, (int) date('Y', $currentTime)),
                $navigationUrl
            );

            $isActive = date('j', $currentTime) == $day;
            $dateButton->addDropDownButton(
                new SubButton((string) $day, null, $dayUrl, ButtonDisplayInterface::DISPLAY_LABEL, null, [], null,
                    $isActive)
            );
        }

        $months = $this->getMonths();
        $monthButton = new DropDownButton($months[date('n', $currentTime)]);

        foreach ($this->getMonths() as $month => $monthLabel)
        {
            $monthUrl = str_replace(
                CalendarTableBuilder::TIME_PLACEHOLDER, (string) mktime(
                0, 0, 0, $month, (int) date('j', $currentTime), (int) date('Y', $currentTime)
            ), $navigationUrl
            );

            $isActive = date('n', $currentTime) == $month;
            $monthButton->addDropDownButton(
                new SubButton($monthLabel, null, $monthUrl, ButtonDisplayInterface::DISPLAY_LABEL, null, [], null,
                    $isActive)
            );
        }

        $yearButton = new DropDownButton(date('Y', $currentTime));

        foreach ($this->getYears($currentTime) as $year)
        {
            $yearUrl = str_replace(
                CalendarTableBuilder::TIME_PLACEHOLDER,
                (string) mktime(0, 0, 0, (int) date('n', $currentTime), (int) date('j', $currentTime), $year),
                $navigationUrl
            );

            $isActive = date('Y', $currentTime) == $year;
            $yearButton->addDropDownButton(
                new SubButton((string) $year, null, $yearUrl, ButtonDisplayInterface::DISPLAY_LABEL, null, [], null,
                    $isActive)
            );
        }

        $buttonGroup->addGroupButton($dateButton);
        $buttonGroup->addGroupButton($monthButton);
        $buttonGroup->addGroupButton($yearButton);

        return new ButtonToolBarRenderer($buttonToolbar);
    }

    /**
     * @return int[]
     */
    public function getDays(int $currentTime): array
    {
        $numberDays = date('t', $currentTime);
        $days = [];

        for ($i = 1; $i <= $numberDays; $i ++)
        {
            $days[$i] = $i;
        }

        return $days;
    }

    /**
     * @return string[]
     */
    public function getMonths(): array
    {
        $translator = $this->getTranslator();

        $monthNames = [
            $translator->trans('JanuaryLong', [], StringUtilities::LIBRARIES),
            $translator->trans('FebruaryLong', [], StringUtilities::LIBRARIES),
            $translator->trans('MarchLong', [], StringUtilities::LIBRARIES),
            $translator->trans('AprilLong', [], StringUtilities::LIBRARIES),
            $translator->trans('MayLong', [], StringUtilities::LIBRARIES),
            $translator->trans('JuneLong', [], StringUtilities::LIBRARIES),
            $translator->trans('JulyLong', [], StringUtilities::LIBRARIES),
            $translator->trans('AugustLong', [], StringUtilities::LIBRARIES),
            $translator->trans('SeptemberLong', [], StringUtilities::LIBRARIES),
            $translator->trans('OctoberLong', [], StringUtilities::LIBRARIES),
            $translator->trans('NovemberLong', [], StringUtilities::LIBRARIES),
            $translator->trans('DecemberLong', [], StringUtilities::LIBRARIES)
        ];

        $months = [];

        foreach ($monthNames as $key => $month)
        {
            $months[$key + 1] = $month;
        }

        return $months;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @return int[]
     */
    public function getYears(int $currentTime): array
    {
        $year = (int) date('Y', $currentTime);
        $years = [];

        for ($i = $year - 5; $i <= $year + 5; $i ++)
        {
            $years[$i] = $i;
        }

        return $years;
    }
}
