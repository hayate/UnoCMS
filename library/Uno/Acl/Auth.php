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

/**
 * TODO: add description of this class, including database tables it relies on
 */
namespace Uno\Acl;

class Auth
{
    const SUCCESS = 0x30;
    const INVALID_IDENTITY = 0x31;
    const INVALID_PASSWORD = 0X32;
    const USER = 'QCID'; // cookie and session name;

    protected static $instance = NULL;

    protected $fields;
    protected $params;
    protected $db;
    protected $role;
    protected $error;

    protected $session;
    protected $cookie;


    protected function __construct()
    {
        $this->fields = array();
        $this->params = array('identity' => array('field' => NULL, 'value' => NULL),
                              'credential' => array('field' => NULL, 'value' => NULL));
        $this->error = self::SUCCESS;
        $this->db = \Database::getInstance();

        $this->session = \Uno\Session::getInstance();
        $this->cookie = \Uno\Cookie::getInstance();
        if ($this->session->exists(self::USER))
        {
            $this->fields = $this->session->get(self::USER);
        }
        else if ($this->cookie->exists(self::USER))
        {
            $this->fields = $this->cookie->get(self::USER);
            $this->session->set(self::USER, $this->fields);
        }
    }

    public static function getInstance()
    {
        if (NULL === static::$instance)
        {
            static::$instance = new Auth();
        }
        return static::$instance;
    }

    public function setIdentity($identity, $field = 'username')
    {
        $this->params['identity']['field'] = $field;
        $this->params['identity']['value'] = $identity;
    }

    public function setCredential($credential, $field = 'password')
    {
        $this->params['credential']['field'] = $field;
        $this->params['credential']['value'] = $credential;
    }

    public function hasIdentity()
    {
        return !empty($this->fields);
    }

    public function clearIdentity()
    {
        $this->session->delete(self::USER);
        $this->cookie->delete(self::USER);
        $this->fields = array();
    }


    /**
     * @param string $algo The algorithm use to hash the password
     * @param int $remember An integer indicating the number of seconds this authentication should remain valid.
     *
     * @return bool TRUE on success authentication FALSE otherwise
     */
    public function authenticate($algo = NULL, $remember = FALSE)
    {
        $query = 'SELECT * FROM users WHERE '. $this->params['identity']['field'] .'=? LIMIT 1';
        $stm = $this->db->prepare($query);
        $stm->bindValue(1, $this->params['identity']['value']);
        if (! $stm->execute())
        {
            $info = $stm->errorInfo();
            \Uno\Log::error($info);
            throw new Exception($info[2]);
        }
        $fields = $stm->fetch(\PDO::FETCH_ASSOC);
        if (empty($fields))
        {
            $this->error = self::INVALID_IDENTITY;
            return FALSE;
        }
        $pword = hash($algo, $this->params['credential']['value']);
        if (strcmp($pword, $fields[$this->params['credential']['field']]) == 0)
        {
            $this->fields = $fields;
            unset($this->fields[$this->params['credential']['field']]);
            unset($this->params['credential']['value']);

            $this->session->set(self::USER, $this->fields);
            if (is_numeric($remember))
            {
                $this->cookie->set(self::USER, $this->fields, intval($remember));
            }
            return TRUE;
        }
        $this->error = self::INVALID_PASSWORD;
        return FALSE;
    }

    public function role($reload = FALSE)
    {
        if (! empty($this->role) && !$reload)
        {
            return $this->role;
        }
        $query = 'SELECT roles.name as role FROM roles JOIN users_roles ON users_roles.role_id=roles.id WHERE users_roles.user_id=?';
        $stm = $this->db->prepare($query);
        $stm->bindValue(1, intval($this->id), \PDO::PARAM_INT);
        if (! $stm->execute())
        {
            $info = $stm->errorInfo();
            \Uno\Log::error($info);
            throw new Exception($info[2]);
        }
        $this->role = $stm->fetchColumn();
        return $this->role;
    }

    public function errorString()
    {
        switch($this->error)
        {
        case self::INVALID_IDENTITY:
            return _('A valid identity could not be found.');
        case self::INVALID_PASSWORD:
            return _('Invalid Password');
        default:
            return '';
        }
    }

    public function errorCode()
    {
        return $this->error;
    }

    public function __get($name)
    {
        return $this->get($name, NULL);
    }

    public function get($name, $default = NULL)
    {
        if ($name == 'role' && $this->hasIdentity())
        {
            return $this->role();
        }
        if (isset($this->fields[$name]))
        {
            return $this->fields[$name];
        }
        return $default;
    }
}