<?php
namespace Module\Admin\Controller;

use Module\Admin\Model\Module;
use \Uno\Acl\Auth;


class Index extends \Uno\Controller
{
    protected $template = 'layout';
    protected $render = TRUE;
    protected $session;
    protected $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = Auth::getInstance();
        if (! $this->auth->hasIdentity())
        {
            $this->redirect('/');
        }
        $this->session = \Uno\Session::getInstance();
        $current = \URI::getInstance()->segment(-1);

        $this->template->header = new \View('header');
        $topmenu = array(array('name' => _('Dashboard'),
                               'link' => '/admin',
                               'active' => 'admin' == $current),
                         array('name' => _('Menus'),
                               'link' => '',
                               'active' => 'menus' == $current),
                         array('name' => _('Layouts'),
                               'link' => '',
                               'active' => 'layouts' == $current),
                         array('name' => _('Widgets'),
                               'link' => '',
                               'active' => 'widgets' == $current),
                         array('name' => _('Modules'),
                               'link' => '/admin/modules',
                               'active' => 'modules' == $current)
            );
        $this->template->header->topmenu = $topmenu;
    }

    public function index($param = NULL)
    {
        $this->template->content = new \View('Index/index');
    }

    public function install($module)
    {
        $filepath = APPPATH .'Module/'. $module .'/'. $module.'.php';
        if (is_file($filepath))
        {
            $classname = 'Module\\'.$module.'\\'.$module;
            $obj = new $classname();
            if (! $obj->install())
            {
                $this->session->set('error', sprintf(_('Module "%s" could not be installed.'), $module));
            }
            else {
                try {
                    $orm = Module::getInstance();
                    $orm->name = $obj->name();
                    $orm->description = $obj->description();
                    $orm->version = $obj->version();
                    $orm->save();
                    $this->session->set('success',sprintf(_('Module "%s" installed successfully.'), $module));
                }
                catch (\Exception $ex)
                {
                    \Uno\Log::error($ex);
                    $this->session->set('error', sprintf(_('Module: %s could not be installed.'), $module));
                }
            }
        }
        $this->redirect('admin/modules');
    }

    public function uninstall($module)
    {
        try {
            $mod = Module::factory($module);
            if ($mod->loaded())
            {
                $mod->delete();
                $classname = 'Module\\'.$module.'\\'.$module;
                $obj = new $classname();
                if ($obj->uninstall())
                {
                    $this->session->set('success', sprintf(_('Module "%s" uninstalled successfully.'), $module));
                    $this->redirect('admin/modules');
                }
            }
        }
        catch (Exception $ex)
        {
            \Uno\Log::error($ex);
        }
        $this->session->set('error', sprintf(_('Module "%s" could not be uninstall.'), $module));
        $this->redirect('admin/modules');
    }

    public function activate($module)
    {
        try {
            $mod = Module::factory($module);
            if ($mod->loaded())
            {
                $mod->enabled = 1;
                $mod->save();
                $this->session->set('success', sprintf(_('Module "%s" activated successfully.'), $module));
            }
        }
        catch (\Exception $ex)
        {
            \Uno\Log::error($ex);
        }
        $this->redirect('admin/modules');
    }

    public function deactivate($module)
    {
        try {
            $mod = Module::factory($module);
            if ($mod->loaded())
            {
                $mod->enabled = 0;
                $mod->save();
                $this->session->set('success', sprintf(_('Module "%s" deactivated successfully.'), $module));
            }
        }
        catch (\Exception $ex)
        {
            \Uno\Log::error($ex);
        }
        $this->redirect('admin/modules');
    }

    public function resource($filename)
    {
        $this->render = FALSE;

        $res = new \Uno\Asset(APPPATH .'Module/Admin/View/static/');
        $res->render($filename);
    }

    public function modules()
    {
        $this->template->content = new \View('Index/modules');
        $this->template->content->installed = Module::getInstance()->findAll();

        $names = Module::getInstance()->names();
        $uninstalled = array();

        $modules = new \FilesystemIterator(APPPATH . 'Module/');
        foreach ($modules as $module)
        {
            if ($module->isDir() && !in_array($module->getFilename(), $names))
            {
                $filepath = $module->getPathname() .'/'.$module->getFilename().'.php';
                if (is_file($filepath))
                {
                    $classname = 'Module\\'.$module->getFilename().'\\'.$module->getFilename();
                    $uninstalled[] = new $classname();
                }
            }
        }
        $this->template->content->uninstalled = $uninstalled;
    }
}