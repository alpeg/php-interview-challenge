<?php


namespace App;


abstract class Container
{
    private $app;

    /**
     * @return App
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @param App $app
     * @return Container
     */
    public function setApp($app)
    {
        $this->app = $app;
        return $this;
    }

}