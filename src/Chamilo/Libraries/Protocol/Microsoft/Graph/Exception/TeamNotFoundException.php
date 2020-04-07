<?php

namespace Chamilo\Libraries\Protocol\Microsoft\Graph\Exception;

use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Translation\Translation;

/**
 * Class TeamNotFoundException
 *
 * @package Chamilo\Libraries\Protocol\Microsoft\Graph\Exception
 */
class TeamNotFoundException extends UserException
{
    /**
     * @var string
     */
    protected $teamId;

    /**
     * TeamNotFoundException constructor.
     *
     * @param string $teamId
     */
    public function __construct($teamId = null)
    {
        parent::__construct(
            Translation::getInstance()->getTranslator()->trans("TeamNotFoundException", [], 'Chamilo\\Libraries')
        );
    }
}
