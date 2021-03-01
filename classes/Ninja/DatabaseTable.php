<?php

namespace Ninja;

use DateTime;
use \Exception;
use \PDOException;

class DatabaseTable
{
  private $pdo;
  private $table;
  private $primaryKey;
  private $className;
  private $constructorArgs;

  public function __construct(\PDO $pdo, string $table, string $primaryKey, string $className = '\stdClass', array $constructorArgs = [])
  {
    $this->pdo = $pdo;
    $this->table = $table;
    $this->primaryKey = $primaryKey;
    $this->className = $className;
    $this->constructorArgs = $constructorArgs;
  }

  private function query($sql, $parameters = [])
  {
    $query = $this->pdo->prepare($sql);
    $query->execute($parameters);
    return $query;
  }

  public function total($column = null, $value = null, $logicalOperator = '=', $concatOperator = 'AND')
  {
    $sql = 'SELECT COUNT(*) FROM `' . $this->table . '`';
    $parameters = [];

    if (!empty($column)) {
      if (!is_array($column)) {
        $sql .= ' WHERE `' . $column . '` ' . $logicalOperator . ' :value';
        $parameters = ['value' => $value];
      } else {
        $sql .= ' WHERE';
        if (is_array($value)) {
          foreach ($column as $key => $val) {
            $sql .= ' `' . $val . '` ' . $logicalOperator . ' :value' . $key . ' ' . $concatOperator;
          }

          $sql = rtrim($sql, ' ' . $concatOperator);

          $parameters = [];

          foreach ($value as $key => $v) {
            $parameters = array_merge($parameters, ['value' . $key => $v]);
          }
        } else {
          foreach ($column as $key => $val) {
            $sql .= ' `' . $val . '` ' . $logicalOperator . ' :value ' . $concatOperator;
          }

          $sql = rtrim($sql, ' ' . $concatOperator);

          $parameters = [
            'value' => $value
          ];
        }
      }
    }

    $query = $this->query($sql, $parameters);

    $row = $query->fetch();

    return $row[0];
  }

  public function findById($value)
  {
    $query = 'SELECT * FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :value';

    $parameters = [
      'value' => $value
    ];

    $query = $this->query($query, $parameters);

    return $query->fetchObject($this->className, $this->constructorArgs);
  }

  public function findAll($orderBy = null, $limit = null, $offset = null)
  {
    $query = 'SELECT * FROM `' . $this->table . '`';

    if ($orderBy != null) {
      $query .= ' ORDER BY ' . $orderBy;
    }

    if ($limit != null) {
      $query .= ' LIMIT ' . $limit;
    }

    if ($offset != null) {
      $query .= ' OFFSET ' . $offset;
    }

    $result = $this->query($query);

    return $result->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
  }

  /**
   * @method find
   * @param $column - Colonna dove cercare un valore
   * @param $value - Valore da cercare nella colonna $column
   * @param $orderBy - Ordinamento ('nome_colonna_ordinamento [ASC || DESC]')
   * @param $limit - Numero di elementi da estrarre
   * @param $offset - Da dove iniziare ad estrarre
   * @param $logicalOperator - Operatore di confronto ['=' || '<>' || '>' || '<' || '<=' || '>=' || 'LIKE']
   * @param $concatOperator - Operatore di concatenamento ['AND' || 'OR']
   */
  public function find($column, $value, $orderBy = null, $limit = null, $offset = null, $logicalOperator = '=', $concatOperator = 'AND')
  {
    $functionPattern = '/^[a-zA-z]+\([\w`,"\'\s]+\)$/';
    if (!is_array($column)) {
      if (!preg_match($functionPattern, $column)) {

        $query = 'SELECT * FROM `' . $this->table . '` WHERE `' . $column . '` ' . $logicalOperator . ' :value';
      } else {
        // $column è una funzione quindi non ci vogliono i backtick `
        $query = 'SELECT * FROM `' . $this->table . '` WHERE ' . $column . ' ' . $logicalOperator . ' :value';
      }

      $parameters = [
        'value' => $value
      ];
    } else {
      $query = 'SELECT * FROM `' . $this->table . '` WHERE';

      if (is_array($value)) {
        foreach ($column as $key => $val) {
          if (preg_match($functionPattern, $val)) {
            // $val è una funzione quindi non ci vogliono i backtick `
            $query .= ' ' . $val . ' ' . $logicalOperator . ' :value' . $key . ' ' . $concatOperator;
          } else {
            $query .= ' `' . $val . '` ' . $logicalOperator . ' :value' . $key . ' ' . $concatOperator;
          }
        }

        $query = rtrim($query, ' ' . $concatOperator);

        $parameters = [];

        foreach ($value as $key => $v) {
          $parameters = array_merge($parameters, ['value' . $key => $v]);
        }
      } else {
        foreach ($column as $key => $val) {
          // $val è una funzione quindi non ci vogliono i backtick `
          if (preg_match($functionPattern, $val)) {
            $query .= ' ' . $val . ' ' . $logicalOperator . ' :value ' . $concatOperator;
          } else {
            $query .= ' `' . $val . '` ' . $logicalOperator . ' :value ' . $concatOperator;
          }
        }

        $query = rtrim($query, ' ' . $concatOperator);

        $parameters = [
          'value' => $value
        ];
      }
    }

    if ($orderBy != null) {
      $query .= ' ORDER BY ' . $orderBy;
    }

    if ($limit != null) {
      $query .= ' LIMIT ' . $limit;
    }

    if ($offset != null) {
      $query .= ' OFFSET ' . $offset;
    }

    $query = $this->query($query, $parameters);

    return $query->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
  }

  public function insert($fields)
  {
    $query = 'INSERT INTO `' . $this->table . '` (';

    foreach ($fields as $key => $value) {
      $query .= "`$key`, ";
    }

    $query = rtrim($query, ', ');

    $query .= ') VALUES (';

    foreach ($fields as $key => $value) {
      $query .= ":$key, ";
    }

    $query = rtrim($query, ', ');

    $query .= ')';

    $fields = $this->processDates($fields);

    $this->query($query, $fields);

    return $this->pdo->lastInsertId();
  }

  public function update($fields)
  {
    $query = ' UPDATE `' . $this->table . '` SET ';

    foreach ($fields as $key => $value) {
      $query .= "`$key` = :$key, ";
    }

    $query = rtrim($query, ', ');

    $query .= " WHERE `$this->primaryKey` = :primaryKey";

    $fields['primaryKey'] = $fields[$this->primaryKey];

    $this->query($query, $fields);
  }

  public function delete($id)
  {
    $parameters = ['id' => $id];

    $this->query('DELETE FROM `' . $this->table . '` WHERE `' . $this->primaryKey . '` = :id', $parameters);
  }

  public function deleteWhere($column, $value, $column2 = null, $value2 = null)
  {
    $query = 'DELETE FROM ' . $this->table . ' WHERE `' . $column . '` = :value';

    $parameters = [
      'value' => $value
    ];

    if (!empty($column2) && !empty($value2)) {
      $query .= ' AND `' . $column2 . '` = :value2';
      $parameters = array_merge($parameters, ['value2' => $value2]);
    }

    $query = $this->query($query, $parameters);
  }

  private function processDates($fields)
  {
    foreach ($fields as $key => $value) {
      if ($value instanceof DateTime) {
        $fields[$key] = $value->format('Y-m-d H:i:s');
      }
    }

    return $fields;
  }

  public function save($record)
  {
    $entity = new $this->className(...$this->constructorArgs);
    try {
      if (empty($record[$this->primaryKey])) {
        $record[$this->primaryKey] = null;
      }
      $insertId = $this->insert($record);
      $entity->{$this->primaryKey} = $insertId;
    } catch (PDOException $e) {
      $this->update($record);
    }

    foreach ($record as $key => $value) {
      if (!empty($value)) {
        $entity->$key = $value;
      }
    }

    return $entity;
  }
}
