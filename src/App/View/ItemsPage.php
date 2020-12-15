<?php


namespace App\View;


use App\Model\TaskItem;

class ItemsPage extends TemplatePage
{
    public function render($options)
    {
        $options = $options + ['items' => [], 'title' => 'ToDo-список'];
        parent::renderHeader($options);
        extract($options);
        /** @var TaskItem[] $items */
        $itemsView = (new ItemsView())->setApp($this->getApp());
        $itemsView->render($options);
        ?><?php
        parent::renderFooter();
    }
}