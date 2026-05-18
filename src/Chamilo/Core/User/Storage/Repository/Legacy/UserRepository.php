<?php
namespace Chamilo\Core\User\Storage\Repository\Legacy;

use Chamilo\Libraries\Storage\Repository\DataClassRepository;

/**
 * @package Chamilo\Core\User\Storage\Repository
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class UserRepository
{
    public function __construct(protected DataClassRepository $dataClassRepository)
    {
    }
}