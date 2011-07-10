<?php
namespace Module\Admin\Model;

class Module extends \ORM
{
    protected static $instance = NULL;


    public function __construct($connection = 'default')
    {
        parent::__construct('modules', $connection);
    }

    public static function getInstance($connection = 'default')
    {
        if (NULL === static::$instance)
        {
            static::$instance = new Module($connection);
        }
        return static::$instance;
    }

    public static function factory($id = NULL, $connection = 'default')
    {
        $module = new Module($connection);
        if (NULL === $id)
        {
            return $module;
        }
        $mod = new Module($connection);
        return $mod->load($id);
    }

    protected function primaryKey($id = NULL)
    {
        if ($id !== NULL && !intval($id))
        {
            return 'name';
        }
        return $this->primaryKey;
    }

    public function names($limit = NULL, $offset = NULL)
    {
        $query = 'SELECT name FROM modules';
        if (NULL !== $limit)
        {
            $query .= ' LIMIT '.$limit;
            if (NULL !== $offset)
            {
                $query .= ' OFFSET '.$offset;
            }
        }
        $stm = $this->db->prepare($query);
        try {
            $stm->execute();
            return $stm->fetchAll(\PDO::FETCH_COLUMN, 0);
        }
        catch (\Exception $ex)
        {
            \Uno\Log::error($ex);
        }
        return FALSE;
    }
}