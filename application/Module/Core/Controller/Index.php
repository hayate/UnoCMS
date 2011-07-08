<?php
namespace Module\Core\Controller;

use Uno\Acl\Auth;


class Index extends \Uno\Controller
{
    protected $auth;
    protected $session;
    protected $render = TRUE;

    public function __construct()
    {
        parent::__construct();
        $this->auth = Auth::getInstance();
        $this->session = \Uno\Session::getInstance();
        $this->template->user = $this->auth;
    }

    public function index()
    {
        if ($this->isPost())
        {
            $this->auth->setIdentity($this->post('username'));
            $this->auth->setCredential($this->post('password'));
            if (! $this->auth->authenticate('sha256'))
            {
                $this->session->set('error', $this->auth->errorString());
            }
            $this->refresh();
        }
        $this->template->error = $this->session->getOnce('error', FALSE);
    }

    public function logout()
    {
        $this->auth->clearIdentity();
        $this->redirect('/');
    }
}