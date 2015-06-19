<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Libraries\Platform\Translation;

class ExternalObjectAcl
{
    const ACL_OWNER = 'owner';
    const ACL_READER = 'reader';
    const ACL_WRITER = 'writer';
    const ACL_PUBLIC = 'public';
    const PROPERTY_ROLE = 'role';
    const PROPERTY_KEY = 'key';

    private $entries;

    public function __construct()
    {
        $this->entries = array();
        $this->entries[self :: ACL_WRITER] = array();
        $this->entries[self :: ACL_READER] = array();
        $this->entries[self :: ACL_PUBLIC] = false;
    }

    public function add_collaborator($user)
    {
        $this->entries[self :: ACL_WRITER][] = $user;
    }

    public function add_viewer($user)
    {
        $this->entries[self :: ACL_READER][] = $user;
    }

    public function set_public($role = false, $key = false)
    {
        if ($role)
        {
            $this->entries[self :: ACL_PUBLIC] = array(self :: PROPERTY_ROLE => $role, self :: PROPERTY_KEY => $key);
        }
    }

    public function set_owner($user)
    {
        $this->entries[self :: ACL_OWNER] = $user;
    }

    public function get_collaborators()
    {
        return $this->entries[self :: ACL_WRITER];
    }

    public function get_viewers()
    {
        return $this->entries[self :: ACL_READER];
    }

    public function is_public()
    {
        return $this->entries[self :: ACL_PUBLIC] != false;
    }

    public function has_collaborators()
    {
        return count($this->entries[self :: ACL_WRITER]) > 0;
    }

    public function count_collaborators()
    {
        return count($this->entries[self :: ACL_WRITER]);
    }

    public function has_viewers()
    {
        return count($this->entries[self :: ACL_READER]) > 0;
    }

    public function count_viewers()
    {
        return count($this->entries[self :: ACL_READER]);
    }

    public function is_not_shared()
    {
        return ! $this->is_public() && ! $this->has_collaborators() && ! $this->has_viewers();
    }

    public function __toString()
    {
        if ($this->is_not_shared())
        {
            return Translation :: get('NotShared');
        }
        else
        {
            $text = array();
            
            if ($this->is_public())
            {
                $text[] = Translation :: get('Everyone');
            }
            
            if ($this->has_collaborators())
            {
                $collaborators = $this->count_collaborators();
                $text[] = $collaborators . ' ' . Translation :: get(
                    $collaborators === 1 ? 'Collaborator' : 'Collaborators');
            }
            
            if ($this->has_viewers())
            {
                $viewers = $this->count_viewers();
                $text[] = $viewers . ' ' . Translation :: get($viewers === 1 ? 'Viewer' : 'Viewers');
            }
            return implode(', ', $text);
        }
    }
}
