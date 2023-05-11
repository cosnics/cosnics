<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Score;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EntryTableRenderer extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\EntryTableRenderer
{
    public function getEntryClassName(): string
    {
        return Entry::class;
    }

    public function getScoreClassName(): string
    {
        return Score::class;
    }
}
