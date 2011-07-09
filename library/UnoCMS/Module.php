<?php
/**
 * MIT License
 * @see: http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright (c) <2011> <Andrea Belvedere> <scieck@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace UnoCMS;

/**
 * Base class for UnoCMS Modules
 */
abstract class Module
{
    /**
     * By default returns the module base class
     *
     * @return string The name of the Module
     */
    public function name()
    {
        return array_pop(explode('\\', get_class($this)));
    }

    /**
     * @return string A description of the Module
     */
    public function description()
    {
        return '';
    }

    /**
     * Module version should be compatible and usable
     * with the "version_compare" function
     * @see http://php.net/manual/en/function.version-compare.php
     *
     * @return string Module version
     */
    abstract public function version();

    /**
     * Performs operations necessary to install the module
     * i.e. create database tables
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function install()
    {
        return FALSE;
    }

    /**
     * Clean up on uninstall, i.e. remove database tables
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function uninstall()
    {
        return FALSE;
    }

    /**
     * Handle module updates
     *
     * @return bool TRUE on success, FALSE on failure
     */
    public function update()
    {
        return FALSE;
    }

    /**
     * @return array Containing $this module Admin menu
     */
    public function adminMenu()
    {
        $menu = array();
        $module = $this->name();
        $filepath = APPPATH . 'Module/'.$module .'/Controller/Admin.php';
        if (is_file($filepath))
        {
            $classname = 'Module\\'.$module.'\\Controller\\Admin';
            $ref = new \ReflectionClass($classname);
            $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method)
            {
                // if it does not start with underscore
                if (substr($method->getName(), 0, 1) != '_')
                {
                    $menu[$this->getMenuName($method)] = strtolower($module).'/admin/'.$method->getName();
                }
            }
        }
        return $menu;
    }

    private function getMenuName(\ReflectionMethod $method)
    {
        $docblock = $method->getDocComment();
        if ($docblock)
        {
            $docblock = array_map('trim', explode("\n", preg_replace('/[\*\r\t]/', ' ', trim(substr($docblock, 3, -2)))));
            foreach ($docblock as $block)
            {
                if (FALSE !== strpos($block, '@menu'))
                {
                    preg_match('/@.+?\s+(.+?)$/i', $block, $docs);
                    if (count($docs) == 2)
                    {
                        return $docs[1];
                    }
                }
            }
        }
        return ucfirst(strtolower($method->getName()));
    }
}