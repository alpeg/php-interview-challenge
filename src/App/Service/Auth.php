<?php


namespace App\Service;


use App\Model\User;
use App\Service;

class Auth extends Service
{
    private $user;

    public function init()
    {
        if (@$_COOKIE['auth'] == '1') {
            session_start();
            if ($_SESSION['user']) {
                $this->setUser($_SESSION['user']);
            } else {
                setcookie('auth', '0', 1, BASENS, null, true, false);
                session_destroy();
            }
        }
    }

    public function isAuthed()
    {
        return !!$this->getUser();
    }
    public function isAdmin()
    {
        $user = $this->getUser();
        return $user && $user->getAdmin() == 1;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

}