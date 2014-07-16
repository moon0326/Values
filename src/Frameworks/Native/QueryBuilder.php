<?php namespace Moon\Properties\Frameworks\Native;

use Moon\Properties\QueryBuilderInterface;

use \DB as DB;
use \PDO;

class QueryBuilder implements QueryBuilderInterface
{
    protected $conn;

    public function __construct($config)
    {
        $this->conn = new PDO(
            'mysql:host='.$config['host'].';dbname='.$config['database'],
            $config['user'],
            $config['password']
        );
    }

    public function getConnection()
    {
        return $this->conn;
    }

    protected function createWheres(array $wheres = [], $implodeSeparator = ' and ')
    {
        $where = [];

        foreach ($wheres as $key=>$value) {
            $where[] = '`'.$key.'`=?';
        }

        return implode($implodeSeparator,$where);

    }

    public function select($table, array $wheres = [])
    {
        $queryStr = 'select * from '. $table . ' where '. $this->createWheres($wheres);
        $sql = $this->conn->prepare($queryStr);
        $execute = $sql->execute(array_values($wheres));

        if (!$execute) {
            return null;
        }

        $rows = $sql->fetchAll(PDO::FETCH_CLASS, 'stdClass');

        if (count($rows)) {
            return $rows;
        }

        return null;
    }

    public function insert($table, array $values)
    {
        $cols = array_map(function($value) {
            return '`'.$value.'`';
        }, array_keys($values));

        $colsStr = '(' . implode(',', $cols) . ')';
        $valuesPlaceholder = [];

        foreach ($cols as $col) {
            $valuesPlaceholder[] = '?';
        }

        $valuesPlaceholder = '(' . implode(',',$valuesPlaceholder) . ')';


        $query = $this->conn->prepare("insert into ". $table . ' ' . $colsStr . ' values '.$valuesPlaceholder);


        $result = $query->execute(array_values($values));

        $id = $this->conn->lastInsertId();

        return $id;
    }

    public function selectFirst($table, array $wheres)
    {
        $record = $this->select($table, $wheres);

        if ($record) {
            return $record[0];
        }

        return $record;
    }

    public function update($table, array $values, $id)
    {
        $queryStr = 'update ' . $table . ' set ' . $this->createWheres($values,',') . ' where id = ' . $id;
        $query = $this->conn->prepare($queryStr);

        return $query->execute(array_values($values));
    }

    public function delete($table, $id)
    {
        $queryStr = 'delete from ' . $table . ' where id = ' . $id;
        return $this->conn->query($queryStr);
    }

    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    public function rollback()
    {
        $this->conn->rollback();
    }

    public function commit()
    {
        $this->conn->commit();
    }
}