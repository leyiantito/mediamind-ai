<?php

namespace MediaMindAI\Core\Database;

use PDO;
use PDOException;
use RuntimeException;

abstract class Model
{
    protected static $connection;
    protected $table;
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $hidden = [];
    protected $fillable = [];
    protected $attributes = [];
    protected $original = [];
    protected $casts = [];
    public $exists = false;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        foreach ($this->fillableFromArray($attributes) as $key => $value) {
            if ($this->isFillable($key)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        return null;
    }

    public function getTable()
    {
        return $this->table ?? strtolower((new \ReflectionClass($this))->getShortName()) . 's';
    }

    public static function setConnection(PDO $connection)
    {
        static::$connection = $connection;
    }

    public static function getConnection(): PDO
    {
        if (!static::$connection) {
            throw new RuntimeException('Database connection has not been established.');
        }
        return static::$connection;
    }

    public static function all()
    {
        $instance = new static;
        $stmt = static::getConnection()->query("SELECT * FROM {$instance->getTable()}");
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function find($id)
    {
        $instance = new static;
        $stmt = static::getConnection()->prepare(
            "SELECT * FROM {$instance->getTable()} WHERE {$instance->primaryKey} = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetchObject(get_called_class());
    }

    public function save()
    {
        if ($this->exists) {
            return $this->performUpdate();
        }
        return $this->performInsert();
    }

    protected function performInsert()
    {
        $attributes = $this->attributes;
        
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            $attributes['created_at'] = $now;
            $attributes['updated_at'] = $now;
        }

        $columns = implode(', ', array_keys($attributes));
        $placeholders = implode(', ', array_fill(0, count($attributes), '?'));
        
        $sql = "INSERT INTO {$this->getTable()} ($columns) VALUES ($placeholders)";
        
        $stmt = static::getConnection()->prepare($sql);
        $result = $stmt->execute(array_values($attributes));
        
        if ($result) {
            $this->{$this->primaryKey} = static::getConnection()->lastInsertId();
            $this->exists = true;
            return true;
        }
        
        return false;
    }

    protected function performUpdate()
    {
        $attributes = $this->attributes;
        
        if ($this->timestamps) {
            $attributes['updated_at'] = date('Y-m-d H:i:s');
        }

        $setClause = implode(' = ?, ', array_keys($attributes)) . ' = ?';
        $values = array_values($attributes);
        $values[] = $this->{$this->primaryKey};
        
        $sql = "UPDATE {$this->getTable()} SET $setClause WHERE {$this->primaryKey} = ?";
        
        $stmt = static::getConnection()->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete()
    {
        if (!$this->exists) {
            return false;
        }
        
        $sql = "DELETE FROM {$this->getTable()} WHERE {$this->primaryKey} = ?";
        $stmt = static::getConnection()->prepare($sql);
        $result = $stmt->execute([$this->{$this->primaryKey}]);
        
        if ($result) {
            $this->exists = false;
            return true;
        }
        
        return false;
    }

    public static function where($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        $instance = new static;
        $sql = "SELECT * FROM {$instance->getTable()} WHERE $column $operator ?";
        $stmt = static::getConnection()->prepare($sql);
        $stmt->execute([$value]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
    }

    public static function create(array $attributes)
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public function update(array $attributes)
    {
        if (!$this->exists) {
            return false;
        }
        
        $this->fill($attributes);
        return $this->save();
    }

    public function toArray()
    {
        return array_diff_key(
            $this->attributes,
            array_flip($this->hidden)
        );
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    public function __toString()
    {
        return $this->toJson();
    }

    protected function fillableFromArray(array $attributes)
    {
        if (count($this->fillable) > 0) {
            return array_intersect_key($attributes, array_flip($this->fillable));
        }
        return $attributes;
    }

    protected function isFillable($key)
    {
        if (in_array($key, $this->fillable)) {
            return true;
        }
        return empty($this->fillable) && !str_starts_with($key, '_');
    }
}
