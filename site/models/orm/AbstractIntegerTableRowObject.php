<?php

/*
 * This class represents a single row in a table. E.g. each row in a table can
 * be turned into one of these and interacted with.
 */

declare(strict_types = 1);



abstract class AbstractIntegerTableRowObject
{
    protected int $m_id; // the identifier, of type uuid
    protected bool $m_isSavedInDatabase = false;


    /**
     * Return an object that can be used to interface with the table in a generic way.
     * E.g. delete(id) load(id), and search()
     * @return TableInterface
     */
    public abstract function getTableHandler() : AbstractIntegerBasedTable;


    /**
     * Constructor with no parameters and declared final so cannot be overridden.
     * If you need to create other ways of creating the object, then create a new createXyz method.
     */
    final protected function __construct()
    {
        $this->m_isSavedInDatabase = false;
    }


    /**
     * Deletes a row from the database provided by the items id
     * @return void
     */
    public function delete() : void
    {
        $this->getTableHandler()->delete($this->m_id);
        $this->m_isSavedInDatabase = false;
    }


    /**
     * Update part of the object. This is the same as replace, except that it
     * can take a subset of the objects parameters, rather than requiring all of them.
     * @param array $data - array of name value pairs.
     */
    public function update(array $data) : void
    {
        $setters = $this->getSetFunctions();

        foreach ($data as $name => $value)
        {
            if (!isset($setters[$name]))
            {
                $warningMessage = "Missing setter for: $name when updating: " . get_called_class();
                trigger_error($warningMessage, E_USER_WARNING);
            }
            else
            {
                $setter = $setters[$name];
                $setter($value);
            }
        }

        $this->save();
    }


    /**
     * Saves this object to the mysql database.
     * @param void
     */
    public function save() : void
    {
        $row = $this->getDatabaseArrayForm();

        if ($this->m_isSavedInDatabase)
        {
            $this->getTableHandler()->update($this->m_id, $row);
        }
        else
        {
            $this->getTableHandler()->create($row);
        }

        $this->m_isSavedInDatabase = true;
    }

    /**
     * Get this object in an array form appropriate for inserting into the database.
     * E.g the indexes are the names of the columns in the databse etc.
     * @return array
     */
    protected function getDatabaseArrayForm() : array
    {
        $properties = array();
        $getFuncs = $this->getAccessorFunctions();

        foreach ($getFuncs as $columnName => $callback)
        {
            /* @var $callback Callback */
            $property = $callback();
            $properties[$columnName] = $property;
        }

        $properties['id'] = $this->m_id;
        return $properties;
    }


    /**
     * Get the query that could be used to update this object in the database.
     * @return string
     */
    private function getUpdateQuery() : string
    {
        $row = $this->getDatabaseArrayForm();
        $db = $this->getTableHandler()->getDb();

        $query =
            "UPDATE {$this->getTableHandler()->getEscapedTableName()} SET " .
            Programster\PgsqlLib\PgsqlLib::generateQueryPairs($db->getResource(), $row) .
            " WHERE {$db->generateQueryPairs(['id' => $this->m_id])}";

        return $query;
    }


    /**
     * Get the query that could be used to insert this object into the database.
     * @return string
     * @throws \Safe\Exceptions\StringsException
     */
    private function getInsertQuery() : string
    {
        $db = $this->getTableHandler()->getDb();
        $row = $this->getDatabaseArrayForm();
        $columnNames = array_keys($row);
        $values = array_values($row);
        $escapedColumnNames = $db->escapeIdentifiers($columnNames);
        $escapedValues = $db->escapeValues($values);

        $valuesString = "";

        foreach ($escapedValues as $escapedValue)
        {
            if ($escapedValue === null)
            {
                $valuesString .= "NULL, ";
            }
            else
            {
                $valuesString .= $escapedValue . ", ";
            }
        }

        $valuesString = \Safe\substr($valuesString, 0, strlen($valuesString) - 2);

        $query =
            "INSERT INTO {$this->getTableHandler()->getEscapedTableName()}" .
            " (" . implode(",", $escapedColumnNames) . ")" .
            " VALUES ($valuesString)";

        return $query;
    }


    /**
     * Returns the relevant query for saving this object to the database, depending on whether the object
     * already exists in the database or not.
     * @return string
     */
    public function getSaveQuery() : string
    {
        return ($this->m_isSavedInDatabase) ? $this->getUpdateQuery() : $this->getInsertQuery();
    }


    /**
     * Mark this object as having been saved in the database.
     * Warning - this should only really ever be called by the table handler. If you manually call this, you
     * may need to be careful taking into account the table handlers object cache.
     * @return void
     */
    public function markSaved() : void
    {
        $this->m_isSavedInDatabase = true;
    }


    /**
     * When cloning objects create a new identifier for the object, and make sure to set the flag stating that it
     * is not already saved to the database.
     */
    public function __clone()
    {
        $this->m_isSavedInDatabase = false;
    }


    /**
     * Fetches an array of mysql column name to property clusures for this object allowing us
     * to get and set them.
     * @return Array<\Closure>
     */
    protected abstract function getAccessorFunctions();


    /**
     * Fetches an array of mysql column name to property references for this object allowing us
     * to get and set them.
     * @return Array<\Closure>
     */
    protected abstract function getSetFunctions();


    /**
     * Get this object in array form. This will be keyed by the column names
     * and have values of what is in the database. This is in its "raw" form and
     * may not be suitable for returning in an API response where some things may
     * need to be filtered out (such as passwords) or formatted (such as unix timestamps).
     * @return array
     */
    public function getArrayForm()
    {
        $arrayForm = array();
        $arrayForm['id'] = $this->m_id;
        $accessors = $this->getAccessorFunctions();

        foreach ($accessors as $column_name => $callback)
        {
            $arrayForm[$column_name] = $callback();
        }

        return $arrayForm;
    }


    /**
     * Create this object from a row in the database.
     * WARNING - do not use this method to create new objects that don't exist in the database.
     * @param array $row
     * @param $fieldInfoMap
     * @return static
     */
    public static function createFromDatabaseRow(array $row, $fieldInfoMap) : static
    {
        $object = new static();
        $object->initializeFromArray($row, $fieldInfoMap);

        $object->m_isSavedInDatabase = true;
        return $object;
    }


    /**
     * Create a new object for possibly saving to the database later. Use this method instead of createFromDatabaseRow
     * when you want to create new records.
     * WARNING - this does NOT automatically save to the database. You will need to call ->save() or do something else.
     * @param array $data - the data in the form that it would be if retrieved from the database.
     * @return static - the newly created object.
     */
    public static function createNewFromArray(array $data) : static
    {
        $object = new static();
        $object->initializeFromArray($data);
        $object->m_isSavedInDatabase = false;
        return $object;
    }


    /**
     * Helper to the constructor. Create this object from the passed in inputs.
     * @param array $row - name value pairs of column to values
     * @param array $fieldInfoMap - optional array of field info. Should have key of name of field and a value array
     * that contains "type". E.g. https://www.php.net/manual/en/function.pg-meta-data.php
     * @throws \Exception
     */
    protected function initializeFromArray(array $row, $fieldInfoMap=null) : void
    {
        $intFieldTypes = array(
            "int2", // smallInt
            "int4", // integer or "serial"
            "int8", // bigint,
            "serial"
        );

        $floatFieldTypes = array(
            "numeric",
            "float4",
            "money"
        );

        $boolFieldTypes = array(
            "bool"
        );

        if (!isset($row['id']) || !is_numeric($row['id']))
        {
            throw new Exception("Missing required integer ID");
        }

        $this->m_id = intval($row['id']);
        $setMethods = $this->getSetFunctions();

        foreach ($setMethods as $columnName => $callback)
        {
            /* @var $callback Callback */
            if (array_key_exists($columnName, $row) === false)
            {
                if
                (
                    !in_array($columnName, $this->getTableHandler()->getFieldsThatAllowNull())
                    && !in_array($columnName, $this->getTableHandler()->getFieldsThatHaveDefaults())
                )
                {
                    $errMsg = $columnName . ' has not yet been created in the mysql table for: ' .
                        get_class($this);

                    throw new \Exception($errMsg);
                }
            }
            else
            {
                $value = $row[$columnName];

                if
                (
                    $fieldInfoMap != null
                    && isset($fieldInfoMap[$columnName])
                )
                {
                    $fieldType = $fieldInfoMap[$columnName]['type'];

                    if (in_array($fieldType, $floatFieldTypes))
                    {
                        if ($value !== null)
                        {
                            $value = floatval($value);
                        }

                        $callback(floatval($value));
                    }
                    else if (in_array($fieldType, $intFieldTypes))
                    {
                        if ($value !== null)
                        {
                            $value = intval($value);
                        }

                        $callback($value);
                    }
                    else if (in_array($fieldType, $boolFieldTypes))
                    {
                        if ($value !== null)
                        {
                            $value = ($value === "t"); // value is string "t" or "f" for true or false.
                        }

                        $callback($value);
                    }
                    else
                    {
                        $callback($value);
                    }
                }
                else
                {
                    $callback($value);
                }
            }
        }
    }


    # Accessors
    public function getId() : int|null { return $this->m_id; }
}
