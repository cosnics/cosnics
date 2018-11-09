<?php

namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entry;

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
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Entry::class;
    }

    /**
     * @return string
     */
    function getScoreClassName()
    {
        return \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\Assignment\Score::class;
    }
}