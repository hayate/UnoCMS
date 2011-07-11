<?php
namespace Module\Admin\Controller;

use Module\Admin\Model\Module;
use \Uno\Acl\Auth;
use \Processor\Menu;


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
        $current = \URI::getInstance()->segment(2);

        $this->template->header = new \View('header');
        $topmenu = array(array('name' => _('Dashboard'),
                               'link' => '/admin',
                               'active' => '' == $current),
                         array('name' => _('Menus'),
                               'link' => '/admin/menus',
                               'active' => 'menus' == $current),
                         array('name' => _('Layouts'),
                               'link' => '',
                               'active' => 'layouts' == $current),
                         array('name' => _('Widgets'),
                               'link' => '',
                               'active' => 'widgets' == $current),
                         array('name' => _('Modules'),
                               'link' => '/admin/modules',
                               'active' => strpos($current, 'module') !== FALSE)
            );
        $this->template->header->topmenu = $topmenu;
        $this->template->jscript('/admin/index/resource/admin.js');
    }

    public function index($param = NULL)
    {
        $this->template->content = new \View('Index/index');
    }

    public function menus($action = NULL)
    {
        if ($this->isPost())
        {
            $pro = new Menu($this->post());
            if (! $pro->process($action))
            {
                $this->session->set('error', $pro->errors());
                $this->session->set('create', $this->post());
                $this->refresh();
            }
            if ($pro->hasProperty('msg'))
            {
                $this->session->set('success', $pro->getProperty('msg'));
            }
            $this->redirect('admin/menus');
        }
        $this->template->content = new \View('Index/menus');
        switch ($action)
        {
        case 'create':
            $this->template->content->content = new \View('Index/menus/create');
            $this->template->content->content->set($this->session->getOnce('create', array('name' => '',
                                                                                           'description' => '')));
            break;
        default:
            $this->template->content->content = new \View('Index/menus/list');
            $this->template->content->content->menus = \ORM::factory('menus')->where('parent', 0)->findAll();
        }
    }

    public function module($module, $action = NULL)
    {
        $mod = Module::factory($module);
        if (! $mod->loaded() || !$mod->enabled)
        {
            $this->redirect('admin/modules');
        }
        $modclass = '\\Module\\'.$module.'\\'.$module;
        $modobj = new $modclass();
        if (0 == count($modobj->adminMenu()))
        {
            $this->redirect('admin/modules');
        }
        $this->template->content = new \View('Index/module');
        $this->template->content->module = $modobj;

        $adminclass = '\\Module\\'.$module.'\\Admin';
        $adminobj = new $adminclass();
        if ((NULL !== $action) && method_exists($adminobj, $action))
        {
            $this->template->content->content = $adminobj->$action();
        }
        else if (method_exists($adminobj, 'home'))
        {
            $this->template->content->content = $adminobj->home();
        }
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
                if (FALSE !== $this->get('clean', FALSE))
                {
                    $classname = 'Module\\'.$module.'\\'.$module;
                    $obj = new $classname();
                    if ($obj->uninstall())
                    {
                        $this->session->set('success', sprintf(_('Module "%s" uninstalled successfully.'), $module));
                        $this->redirect('admin/modules');
                    }
                }
                $this->session->set('success', sprintf(_('Module "%s" uninstalled successfully.'), $module));
                $this->redirect('admin/modules');
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

        $names = Module::getInstance()->names();
        $uninstalled = array();
        $installed = array();

        $modules = new \FilesystemIterator(APPPATH . 'Module/');
        foreach ($modules as $module)
        {
            if ($module->isDir())
            {
                $filepath = $module->getPathname() .'/'.$module->getFilename().'.php';
                if (is_file($filepath))
                {
                    $classname = 'Module\\'.$module->getFilename().'\\'.$module->getFilename();
                    if (in_array($module->getFilename(), $names))
                    {
                        $installed[] = array(new $classname(), Module::factory($module->getFilename()));
                    }
                    else {
                        $uninstalled[] = new $classname();
                    }
                }
            }
        }
        $this->template->content->uninstalled = $uninstalled;
        $this->template->content->installed = $installed;
    }
}