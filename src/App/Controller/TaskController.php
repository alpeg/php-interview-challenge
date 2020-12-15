<?php


namespace App\Controller;

use App\AppUtil;
use App\Controller;
use App\Model\TaskItem;
use App\Service\Db;
use App\View\ItemsPage;
use App\View\ItemsView;

class TaskController extends Controller
{
    public function index($verb, $path, $order = null)
    {
        $itemsPage = (new ItemsPage())->setApp($this->getApp());
        /** @var Db $db */
        $db = $this->getApp()->service('db');
        $itemsCount = $db->fetchCount(\App\Model\TaskItem::class);
        $maxPage = AppUtil::pageMax($itemsCount, 3);
        $page = AppUtil::page(filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT), $maxPage);
        $orderArr = ['id' => 'DESC'];
        if ($order && preg_match('#^(username|email|text)_(asc|desc)$#', $order, $matches)) {
            $orderArr = [
                $matches[1] => strtoupper($matches[2]),
                'id' => 'DESC',
            ];
        }
        $items = $db->fetch(\App\Model\TaskItem::class, null, $orderArr, 3, $page);
        return $itemsPage->render([
            'items' => $items,
            'itemsPage' => $page,
            'itemsPageMax' => $maxPage,
            'itemsAddForm' => true,
            'itemsFormUsername' => @$_COOKIE['itemsFormUsername'],
            'itemsFormEmail' => @$_COOKIE['itemsFormEmail'],
            'order' => $order,
            'self' => BASENS . $path,
            'itemsEdit' => isset($_GET['edit']) ? ((int)$_GET['edit']) : null,
            'itemsAdded' => isset($_GET['added']) ? ((int)$_GET['added']) : null,
        ]);
    }

    public function add($verb, $path)
    {
        if (
            !isset($_POST['task'])
            || !AppUtil::validateAssocSimple($taskArr = $_POST['task'], ['username' => true, 'email' => true, 'text' => true], true)
        ) die('HTTP/1.0 400 Bad Request');
        $item = (new TaskItem())->fromAssoc($taskArr);
        /** @var Db $db */
        $db = $this->getApp()->service('db');
        $db->store($item);
        setcookie('itemsFormUsername', $item->getUsername(), time() + 3600 * 24 * 30, BASENS, null, true, false);
        setcookie('itemsFormEmail', $item->getEmail(), time() + 3600 * 24 * 30, BASENS, null, true, false);

        header('Location: ' . BASE . '?added=' . ItemsView::ITEM_ADDED_OK);
    }


    public function edit($verb, $path, $taskId)
    {
        if (!$this->getApp()->service('auth')->isAdmin()) {
            header('HTTP/1.0 403');
            echo "Access denied.";
        }
        /** @var Db $db */
        $db = $this->getApp()->service('db');
        /** @var TaskItem[] $items */
        $items = $db->fetch(\App\Model\TaskItem::class, ['id' => (int)$taskId]);
        if (!$items[0]) {
            header('HTTP/1.0 404 Not Found');
            echo "Not Found.";
            return;
        }
        if (
            !isset($_POST['task'])
            || !AppUtil::validateAssocSimple($taskArr = $_POST['task'], ['text' => true], true)
        ) die('HTTP/1.0 400 Bad Request');
        if ($taskArr['text'] != $items[0]->getText()) {
            $items[0]->setText($taskArr['text']);
            $items[0]->setEdited(1);
            $added = ItemsView::ITEM_ADDED_EDIT;
        } else {
            $added = ItemsView::ITEM_ADDED_NOEDIT;
        }
        $db->store($items[0]);
        header('Location: ' . (isset($_GET['next']) ? $_GET['next'] : BASE) . '&added=' . $added);
    }

    public function done($verb, $path, $taskId)
    {
        if (!$this->getApp()->service('auth')->isAdmin()) {
            header('HTTP/1.0 403');
            echo "Access denied.";
        }
        /** @var Db $db */
        $db = $this->getApp()->service('db');
        /** @var TaskItem[] $items */
        $items = $db->fetch(\App\Model\TaskItem::class, ['id' => (int)$taskId]);
        if (!$items[0]) {
            header('HTTP/1.0 404 Not Found');
            echo "Not Found.";
            return;
        }
        $items[0]->setComplete(1);
        $db->store($items[0]);
        header('Location: ' . (isset($_GET['next']) ? $_GET['next'] : BASE));
    }
}