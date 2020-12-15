<?php


namespace App\Controller;

use App\Controller;
use App\Model\User;
use App\Service\Db;
use App\View\AuthPage;

class AuthController extends Controller
{

    public function logout($verb, $path)
    {
        setcookie('auth', '0', 1, BASENS, null, true, false);
        session_destroy();
        header('Location: ' . BASE);
    }

    public function index($verb, $path)
    {
        $authPage = (new AuthPage())->setApp($this->getApp());
        /** @var Db $db */
        $db = $this->getApp()->service('db');
        $itemsFormUsername = @$_COOKIE['itemsFormUsername'];
        $error = null;
        if ($verb == 'POST') {
            if (!isset($_POST['username']) || !$_POST['username']) {
                $error = 'Введите логин.';
            } else if (!isset($_POST['password']) || !$_POST['password']) {
                $error = 'Введите пароль.';
            } else {
                $username = $_POST['username'];
                $password = $_POST['password'];
                /** @var User[] $users */
                $users = $db->fetch(\App\Model\User::class, ['username' => $username]);
                if (count($users) < 1) {
                    $error = 'Неверный логин.';
                    setcookie('itemsFormUsername', '', 1, BASENS, null, true, false);
                    $itemsFormUsername = '';
                } elseif ($users[0]->getPassword() !== $password) {
                    $error = 'Неверный пароль.';
                    setcookie('itemsFormUsername', $users[0]->getUsername(), time() + 3600 * 24 * 30, BASENS, null, true, false);
                } else {
                    $itemsFormUsername = $users[0]->getUsername();
                    setcookie('itemsFormUsername', $itemsFormUsername, time() + 3600 * 24 * 30, BASENS, null, true, false);
                    setcookie('auth', '1', time() + 3600 * 24 * 30, BASENS, null, true, false);
                    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                    $_SESSION['user'] = $users[0];
                    header('Location: ' . BASE);
                }
            }
        }


        return $authPage->render([
            'itemsFormUsername' => $itemsFormUsername,
            'itemsFormEmail' => @$_COOKIE['itemsFormEmail'],
            'self' => BASENS . $path,
            'error' => $error,
        ]);
    }
}