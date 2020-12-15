<?php


namespace App;


abstract class Model
{
    /**
     * Создает объект модели из ассоциативного массива
     * @param $assoc
     * @return $this
     */
    public function fromAssoc($assoc)
    {
        foreach ($assoc as $k => $v) {
            $setV = 'set' . strtoupper(substr($k, 0, 1)) . strtolower(substr($k, 1));
            call_user_func([$this, $setV], $v);
        }
        return $this;
    }

    /**
     * Имя таблицы
     * @return string
     */
    abstract public function table();
}