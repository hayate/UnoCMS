<?php
namespace Module\Admin\Controller;

use \Uno\Acl\Auth;


class Index extends \Uno\Controller
{
    protected $template = 'layout';
    protected $render = TRUE;
    protected $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = Auth::getInstance();
        if (! $this->auth->hasIdentity())
        {
            $this->redirect('/');
        }
    }

    public function index($param = NULL)
    {
        $this->template->header = new \View('header');
        $this->template->content = new \View('Index/index');
    }

    public function resource($filename)
    {
        $this->render = FALSE;

        $res = new \Uno\Asset(APPPATH .'Module/Admin/View/static/');
        $res->render($filename);
    }

    public function modules()
    {
        $modules = new \FilesystemIterator(APPPATH . 'Module/');
        foreach ($modules as $module)
        {
            $path = $module->getPathname();
            $filepath = $path .'/'.$module->getFilename().'.php';
            if (is_file($filepath))
            {
                $classname = '\\'.$module->getFilename().'\\'.$module->getFilename();
                include_once $filepath;
                $obj = new $classname();
                var_dump($obj->adminMenu(), $obj->name());
            }
        }
    }
}