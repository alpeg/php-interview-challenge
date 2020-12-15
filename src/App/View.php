<?php


namespace App;


abstract class View extends Container
{
    abstract public function render($options);

    public function isAuthed()
    {
        return $this->getApp()->service('auth')->isAuthed();
    }

    public function isAdmin()
    {
        return $this->getApp()->service('auth')->isAdmin();
    }
}