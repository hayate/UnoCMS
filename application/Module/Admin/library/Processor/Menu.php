<?php
namespace Processor;

class Menu extends \Uno\Processor
{
    public function process($action = NULL)
    {
        switch ($action)
        {
        case 'create':
            return $this->createNewMenu();
        default:
            $this->addError(_('Invalid Request'));
        }
        return FALSE;
    }

    protected function createNewMenu()
    {
        try {
            $this->val->addRule('name', array('required'), array(_('Menu name is a required field.')));
            $this->val->addCallback('name', array($this, '_check_menu_name_is_unique'));
            if ($this->val->validate())
            {
                $orm = \ORM::factory('menus');
                $orm->name = $this->name;
                $orm->description = $this->val->get('description', '');
                $orm->save();

                $this->setProperty('msg', sprintf(_('Menu "%s" created successfully.'), $this->name));
                return TRUE;
            }
        }
        catch (Exception $ex)
        {
            \Uno\Log::error($ex);
        }
        return FALSE;
    }

    public function _check_menu_name_is_unique(\Uno\Validator $valid, $field)
    {
        $orm = \ORM::factory('menus')->where('name', $valid->$field)->find();
        if ($orm->loaded())
        {
            $valid->addError($field, sprintf(_('Menu name "%s" already exists.'), $valid->$field));
        }
    }
}