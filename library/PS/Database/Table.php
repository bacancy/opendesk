<?php
class PS_Database_Table extends Zend_Db_Table_Abstract {
/**
 * @var Zend_Db
 */ 
protected $read = NULL;

/**
 * @var Zend_Db
 */ 
protected $write = NULL;

/**
 * Constructor.
 *
 * Supported params for $config are:
 * - db              = user-supplied instance of database connector,
 *                     or key name of registry instance.
 * - name            = table name.
 * - primary         = string or array of primary key(s).
 * - rowClass        = row class name.
 * - rowsetClass     = rowset class name.
 * - referenceMap    = array structure to declare relationship
 *                     to parent tables.
 * - dependentTables = array of child tables.
 * - metadataCache   = cache for information from adapter describeTable().
 *
 * @param  mixed $config Array of user-specified config options, or just the Db Adapter.
 * @return void
 */ 
public function __construct($config=array()){    
	   
    $this->read  = Zend_Registry::get(PS_App_DbAdapter);
    $this->write = Zend_Registry::get(PS_App_DbAdapter);

    $config['db'] = $this->read;              
    return parent::__construct($config);
}


/**
 * Inserts a new row.
 *
 * @param  array  $data  Column-value pairs.
 * @return mixed         The primary key of the row inserted.
 */    
public function insert(array $data){
    $this->setAdapter($this->write);
    $result = parent::insert($data);
    $this->setAdapter($this->read);

    return $result;
}

/**
 * Updates existing rows.
 *
 * @param  array        $data  Column-value pairs.
 * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
 * @return int          The number of rows updated.
 */    
public function update(array $data, $where){
    $this->setAdapter($this->write);
    $result = parent::update($data,$where);
    $this->setAdapter($this->read);
    return $result;
}

/**
 * Fetches a new blank row (not from the database).
 *
 * @param  array $data OPTIONAL data to populate in the new row.
 * @param  string $defaultSource OPTIONAL flag to force default values into new row
 * @return Zend_Db_Table_Row_Abstract
 */    
public function createRow(array $data = array(), $defaultSource = NULL){
    $this->setAdapter($this->write);
    $result = parent::createRow($data, $defaultSource);
    $this->setAdapter($this->read);
    return $result;
}

/**
 * Deletes existing rows.
 *
 * @param  array|string $where SQL WHERE clause(s).
 * @return int          The number of rows deleted.
 */    
public function delete($where){
    $this->setAdapter($this->write);
    $result = parent::delete($where);
    $this->setAdapter($this->read);    
    return $result;
}

/**
 * Allow to set current used connection
 * from Enalog_Db_Table_Row
 * 
 * @param Zend_Db $db
 */    
public function setAdapter($db){
    $this->_db = self::_setupAdapter($db);
    return $this;
} 
}
