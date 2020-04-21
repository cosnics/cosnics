<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entry;

use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Entry;
use Chamilo\Application\Weblcms\Bridge\Assignment\Storage\DataClass\Score;

/**
 *
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entry\User
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntryTableColumnModel
    extends \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entry\EntryTableColumnModel
{
    /**
     * @return string
     */
    function getEntryClassName()
    {
        return Entry::class;
    }

    /**
     * @return string
     */
    function getScoreClassName()
    {
        return Score::class;
    }
}