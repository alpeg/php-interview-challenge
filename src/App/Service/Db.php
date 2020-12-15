<?php


namespace App\Service;


use App\Model;
use App\Service;

class Db extends Service
{
    private $dbh;

    /**
     * @param $params array
     * @param $where string|null
     * @return string
     */
    private static function sqlWhere(&$params, $where = null)
    {
        if (!$where || count($where) < 1) return '';
        $sqlWhereArr = [];
        foreach ($where as $k => $v) {
            $sqlWhereArr[] = "`$k`=?";
            $params[] = $v;
        }
        return ' WHERE ' . implode(',', $sqlWhereArr);
    }

    /**
     * @param $params array
     * @param $order string|null
     * @return string
     */
    private static function sqlOrder(&$params, $order = null)
    {
        if (!$order || count($order) < 1) return '';
        $sqlOrderArr = [];
        foreach ($order as $k => $v) {
            $sqlOrderArr[] = "`$k` $v";
        }
        return ' ORDER BY ' . implode(',', $sqlOrderArr);
    }

    /**
     * @param $params array
     * @param null $numpage
     * @param int|null $page
     * @return string
     */
    private static function sqlPage($numpage = null, $page = null)
    {
        if (!$numpage || $numpage <= 0) return '';
        if ($page === null) $page = 0;
        if ($page < 0) return [];
        $offset = $page * $numpage;
        return ' LIMIT ' . $offset . ', ' . $numpage;
    }

    /**
     * Возвращает элементы модели из базы.
     *
     * @param $class Model
     * @param $where string|null
     * @param $order string|null
     * @param $numpage int|null количество на странице
     * @param $page int страница (начиная с 0)
     * @return Model
     */
    public function fetch($class, $where = null, $order = null, $numpage = null, $page = 0)
    {
        $params = [];
        $sqlWhere = self::sqlWhere($params, $where);
        $sqlOrder = self::sqlOrder($params, $order);
        $sqlPage = self::sqlPage($numpage, $page);
        $stmt = $this->dbh->prepare('SELECT * FROM `' . (new $class)->table() . '`' . $sqlWhere . $sqlOrder . $sqlPage);
        $stmt->execute($params);
        return array_map(function ($row) use ($class) {
            $item = new $class;
            return $item->fromAssoc($row);
        }, $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Возвращает количество элементов.
     *
     * @param $class Model
     * @param $where string|null
     * @param $order string|null
     * @param $numpage int|null количество на странице
     * @param $page int страница (начиная с 0)
     * @return int
     */
    public function fetchCount($class, $where = null)
    {
        $params = [];
        $sqlWhere = self::sqlWhere($params, $where);
        $stmt = $this->dbh->prepare('SELECT count(`id`) FROM `' . (new $class)->table() . '`' . $sqlWhere);
        $stmt->execute($params);
        $r = $stmt->fetchAll(\PDO::FETCH_NUM);
        return (int)$r[0][0];
    }

    /**
     * Сохраняет модель в базу
     *
     * @param $item Model
     * @return bool success
     */
    public function store($item)
    {
        if ($item->getId() === null) return $this->create($item);
        // update
        $params = [];
        // get_class_methods
        $sqlSetArray = [];
        foreach (get_class_methods($item) as $class_method) {
            if (substr($class_method, 0, 3) !== 'set') continue;
            $getV = 'get' . substr($class_method, 3);
            $key = strtolower(substr($class_method, 3));
            if ($key == 'id') continue;
            $sqlSetArray[] = '`' . $key . '`=?';
            $params[] = call_user_func([$item, $getV]);
        }
        $sqlSet = join(',', $sqlSetArray);
        $params[] = $item->getId();
        $stmt = $this->dbh->prepare($x = 'UPDATE `' . $item->table() . '` SET ' . $sqlSet . ' WHERE `id`=?');
        return $stmt->execute($params);
    }

    /**
     * @param $item Model
     * @return bool success
     */
    private function create($item)
    {
        $params = [];
        // get_class_methods
        $sqlKeyArray = [];
        $sqlValueArray = [];
        foreach (get_class_methods($item) as $class_method) {
            if (substr($class_method, 0, 3) !== 'set') continue;
            $getV = 'get' . substr($class_method, 3);
            $key = strtolower(substr($class_method, 3));
            if ($key == 'id') continue;
            $propValue = call_user_func([$item, $getV]);
            if ($propValue === null) continue;
            $sqlKeyArray[] = '`' . $key . '`';
            $sqlValueArray[] = '?';
            $params[] = $propValue;
        }
        $sqlKey = join(',', $sqlKeyArray);
        $sqlValue = join(',', $sqlValueArray);
        $stmt = $this->dbh->prepare('INSERT INTO `' . $item->table() . '`(' . $sqlKey . ') VALUES(' . $sqlValue . ');');
        if ($success = $stmt->execute($params)) {
            $item->setId($this->dbh->lastInsertId());
        }
        return $success;
    }

    public function __construct()
    {
        $this->dbh = new \PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
            DB_USERNAME,
            DB_PASSWORD,
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        $this->dbh->exec('SET NAMES utf8mb4');
    }
}