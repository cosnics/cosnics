<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\User\Domain;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Export parameters for users
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserExportParameters
{
    /**
     * @var User[]
     */
    protected $users;

    /**
     * @var string
     */
    protected $exportFilename;

    /**
     * UserExportParameters constructor.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User[] $users
     * @param string $exportFilename
     */
    public function __construct(array $users, $exportFilename)
    {
        $this->setUsers($users)
            ->setExportFilename($exportFilename);
    }

    /**
     * @return \Chamilo\Core\User\Storage\DataClass\User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User[] $users
     *
     * @return UserExportParameters
     */
    protected function setUsers($users)
    {
        if (!is_array($users))
        {
            throw new \InvalidArgumentException('The given users parameters should be a valid array');
        }

        $this->users = $users;

        return $this;
    }

    /**
     * @return string
     */
    public function getExportFilename()
    {
        return $this->exportFilename;
    }

    /**
     * @param string $exportFilename
     *
     * @return UserExportParameters
     */
    protected function setExportFilename($exportFilename)
    {
        if (empty($exportFilename) || !is_string($exportFilename))
        {
            throw new \InvalidArgumentException(
                'The given export filename should be a valid string and should not be empty'
            );
        }

        $this->exportFilename = $exportFilename;

        return $this;
    }

}