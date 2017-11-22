<?php
namespace Chamilo\Core\Admin\Storage\DataClass;

use Chamilo\Core\Admin\Storage\DataManager;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package admin.lib
 * @author Hans De Bisschop
 */
class RemotePackage extends DataClass
{

    /**
     * Package properties
     */
    const PROPERTY_CODE = 'code';
    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SECTION = 'section';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_AUTHORS = 'authors';
    const PROPERTY_VERSION = 'version';
    const PROPERTY_CYCLE = 'cycle';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_SIZE = 'size';
    const PROPERTY_MD5 = 'md5';
    const PROPERTY_SHA1 = 'sha1';
    const PROPERTY_SHA256 = 'sha256';
    const PROPERTY_SHA512 = 'sha512';
    const PROPERTY_TAGLINE = 'tagline';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_HOMEPAGE = 'homepage';
    const PROPERTY_DEPENDENCIES = 'dependencies';
    const PROPERTY_EXTRA = 'extra';

    // Sub-properties
    const PROPERTY_CYCLE_PHASE = 'phase';
    const PROPERTY_CYCLE_REALM = 'realm';

    // Release phases
    const PHASE_ALPHA = 'alpha';
    const PHASE_BETA = 'beta';
    const PHASE_RELEASE_CANDIDATE = 'release_candidate';
    const PHASE_GENERAL_AVAILABILITY = 'general_availability';

    // Release realm
    const REALM_MAIN = 'main';
    const REALM_UNIVERSE = 'universe';

    /**
     * Get the default properties
     *
     * @return array The property names.
     */
    public static function get_default_property_names($extended_property_names = array())
    {
        return parent::get_default_property_names(
            array(
                self::PROPERTY_CODE,
                self::PROPERTY_CONTEXT,
                self::PROPERTY_NAME,
                self::PROPERTY_SECTION,
                self::PROPERTY_CATEGORY,
                self::PROPERTY_AUTHORS,
                self::PROPERTY_VERSION,
                self::PROPERTY_CYCLE,
                self::PROPERTY_FILENAME,
                self::PROPERTY_SIZE,
                self::PROPERTY_MD5,
                self::PROPERTY_SHA1,
                self::PROPERTY_SHA256,
                self::PROPERTY_SHA512,
                self::PROPERTY_TAGLINE,
                self::PROPERTY_DESCRIPTION,
                self::PROPERTY_HOMEPAGE,
                self::PROPERTY_DEPENDENCIES,
                self::PROPERTY_EXTRA));
    }

    /**
     * inherited
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Returns the code of this Package.
     *
     * @return the code.
     */
    public function get_code()
    {
        return $this->get_default_property(self::PROPERTY_CODE);
    }

    /**
     * Sets the code of this Package.
     *
     * @param code
     */
    public function set_code($code)
    {
        $this->set_default_property(self::PROPERTY_CODE, $code);
    }

    /**
     * Returns the context of this Package.
     *
     * @return the context.
     */
    public function get_context()
    {
        return $this->get_default_property(self::PROPERTY_CONTEXT);
    }

    /**
     * Sets the context of this Package.
     *
     * @param context
     */
    public function set_context($context)
    {
        $this->set_default_property(self::PROPERTY_CONTEXT, $context);
    }

    /**
     * Returns the name of this Package.
     *
     * @return the name.
     */
    public function get_name()
    {
        return $this->get_default_property(self::PROPERTY_NAME);
    }

    /**
     * Sets the name of this Package.
     *
     * @param name
     */
    public function set_name($name)
    {
        $this->set_default_property(self::PROPERTY_NAME, $name);
    }

    /**
     * Returns the section of this Package.
     *
     * @return the section.
     */
    public function get_section()
    {
        return $this->get_default_property(self::PROPERTY_SECTION);
    }

    /**
     * Sets the section of this Package.
     *
     * @param section
     */
    public function set_section($section)
    {
        $this->set_default_property(self::PROPERTY_SECTION, $section);
    }

    /**
     * Returns the category of this Package.
     *
     * @return the category.
     */
    public function get_category()
    {
        return $this->get_default_property(self::PROPERTY_CATEGORY);
    }

    /**
     * Sets the category of this Package.
     *
     * @param category
     */
    public function set_category($category)
    {
        $this->set_default_property(self::PROPERTY_CATEGORY, $category);
    }

    /**
     * Returns the authors of this Package.
     *
     * @return the authors.
     */
    public function get_authors()
    {
        return unserialize($this->get_default_property(self::PROPERTY_AUTHORS));
    }

    /**
     * Sets the authors of this Package.
     *
     * @param authors
     */
    public function set_authors($authors)
    {
        $this->set_default_property(self::PROPERTY_AUTHORS, serialize($authors));
    }

    /**
     * Returns the version of this Package.
     *
     * @return the version.
     */
    public function get_version()
    {
        return $this->get_default_property(self::PROPERTY_VERSION);
    }

    /**
     * Sets the version of this Package.
     *
     * @param version
     */
    public function set_version($version)
    {
        $this->set_default_property(self::PROPERTY_VERSION, $version);
    }

    /**
     * Returns the cycle of this Package.
     *
     * @return the cycle.
     */
    public function get_cycle()
    {
        return unserialize($this->get_default_property(self::PROPERTY_CYCLE));
    }

    /**
     * Sets the cycle of this Package.
     *
     * @param cycle
     */
    public function set_cycle($cycle)
    {
        $this->set_default_property(self::PROPERTY_CYCLE, serialize($cycle));
    }

    /**
     * Returns the cycle phase of this Package.
     *
     * @return the cycle phase.
     */
    public function get_cycle_phase()
    {
        $cycle = $this->get_cycle();
        return $cycle[self::PROPERTY_CYCLE_PHASE];
    }

    /**
     * Returns the cycle realm of this Package.
     *
     * @return the cycle realm.
     */
    public function get_cycle_realm()
    {
        $cycle = $this->get_cycle();
        return $cycle[self::PROPERTY_CYCLE_REALM];
    }

    /**
     * Returns the filename of this Package.
     *
     * @return the filename.
     */
    public function get_filename()
    {
        return $this->get_default_property(self::PROPERTY_FILENAME);
    }

    /**
     * Sets the filename of this Package.
     *
     * @param filename
     */
    public function set_filename($filename)
    {
        $this->set_default_property(self::PROPERTY_FILENAME, $filename);
    }

    /**
     * Returns the size of this Package.
     *
     * @return the size.
     */
    public function get_size()
    {
        return $this->get_default_property(self::PROPERTY_SIZE);
    }

    /**
     * Sets the size of this Package.
     *
     * @param size
     */
    public function set_size($size)
    {
        $this->set_default_property(self::PROPERTY_SIZE, $size);
    }

    /**
     * Returns the md5 of this Package.
     *
     * @return the md5.
     */
    public function get_md5()
    {
        return $this->get_default_property(self::PROPERTY_MD5);
    }

    /**
     * Sets the md5 of this Package.
     *
     * @param md5
     */
    public function set_md5($md5)
    {
        $this->set_default_property(self::PROPERTY_MD5, $md5);
    }

    /**
     * Returns the sha1 of this Package.
     *
     * @return the sha1.
     */
    public function get_sha1()
    {
        return $this->get_default_property(self::PROPERTY_SHA1);
    }

    /**
     * Sets the sha1 of this Package.
     *
     * @param sha1
     */
    public function set_sha1($sha1)
    {
        $this->set_default_property(self::PROPERTY_SHA1, $sha1);
    }

    /**
     * Returns the sha256 of this Package.
     *
     * @return the sha256.
     */
    public function get_sha256()
    {
        return $this->get_default_property(self::PROPERTY_SHA256);
    }

    /**
     * Sets the sha256 of this Package.
     *
     * @param sha256
     */
    public function set_sha256($sha256)
    {
        $this->set_default_property(self::PROPERTY_SHA256, $sha256);
    }

    /**
     * Returns the sha512 of this Package.
     *
     * @return the sha512.
     */
    public function get_sha512()
    {
        return $this->get_default_property(self::PROPERTY_SHA512);
    }

    /**
     * Sets the sha512 of this Package.
     *
     * @param sha512
     */
    public function set_sha512($sha512)
    {
        $this->set_default_property(self::PROPERTY_SHA512, $sha512);
    }

    /**
     * Returns the tagline of this Package.
     *
     * @return the tagline.
     */
    public function get_tagline()
    {
        return $this->get_default_property(self::PROPERTY_TAGLINE);
    }

    /**
     * Sets the tagline of this Package.
     *
     * @param tagline
     */
    public function set_tagline($tagline)
    {
        $this->set_default_property(self::PROPERTY_TAGLINE, $tagline);
    }

    /**
     * Returns the description of this Package.
     *
     * @return the description.
     */
    public function get_description()
    {
        return $this->get_default_property(self::PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Package.
     *
     * @param description
     */
    public function set_description($description)
    {
        $this->set_default_property(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the extras of this Package.
     *
     * @return the extras.
     */
    public function get_extra()
    {
        return unserialize($this->get_default_property(self::PROPERTY_EXTRA));
    }

    /**
     * Sets the extras of this Package.
     *
     * @param extras
     */
    public function set_extra($extra)
    {
        $this->set_default_property(self::PROPERTY_EXTRA, serialize($extra));
    }

    /**
     * Returns the homepage of this Package.
     *
     * @return the homepage.
     */
    public function get_homepage()
    {
        return $this->get_default_property(self::PROPERTY_HOMEPAGE);
    }

    /**
     * Sets the homepage of this Package.
     *
     * @param homepage
     */
    public function set_homepage($homepage)
    {
        $this->set_default_property(self::PROPERTY_HOMEPAGE, $homepage);
    }

    /**
     * Returns the dependencies of this Package.
     *
     * @return the dependencies.
     */
    public function get_dependencies()
    {
        return unserialize($this->get_default_property(self::PROPERTY_DEPENDENCIES));
    }

    /**
     * Sets the dependencies of this Package.
     *
     * @param dependencies
     */
    public function set_dependencies($dependencies)
    {
        $this->set_default_property(self::PROPERTY_DEPENDENCIES, serialize($dependencies));
    }

    public function is_official()
    {
        return $this->get_cycle_realm() == self::REALM_MAIN;
    }

    public function is_stable()
    {
        return $this->get_cycle_phase() == self::PHASE_GENERAL_AVAILABILITY;
    }
}
