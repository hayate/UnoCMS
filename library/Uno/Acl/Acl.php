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
namespace Uno\Acl;

class Acl
{
    const ALLOW = 'allow';
    const DENY = 'deny';

    protected static $instance = NULL;

    protected function __construct() {}


    public static function getInstance()
    {
        if (NULL === static::$instance)
        {
            static::$instance = new Acl();
        }
        return static::$instance;
    }

    public function hasPermission($role, Resource $resource, $action = '*')
    {
        if (is_string($role))
        {
            $role = Role::factory($role);
        }
        $permission = \ORM::factory('permissions')->where(array('resource_id' => $resource->id,
                                                                'role_id' => $role->id))->find();
        if (! $permission->loaded())
        {
            return FALSE;
        }
        if ($permission->permission == self::DENY)
        {
            return FALSE;
        }
        if (is_string($permission->actions))
        {
            $actions = preg_split('/,|\s+/', $permission->actions, -1, PREG_SPLIT_NO_EMPTY);
            return in_array($action, $actions);
        }
        return FALSE;
    }
}