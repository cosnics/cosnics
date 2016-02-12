<?php
namespace Chamilo\Libraries\Architecture\Interfaces;

/**
 * A class implements the <code>Autosavable</code> interface to
 * indicate that it supports autosave
 * 
 * @author jevdheyd
 */
interface Autosavable
{

    public function autosave_required();

    public function autosave_defaults();
}
