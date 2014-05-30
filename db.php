<?php
/**
 * Utility class on top of PDO
 *
 * @author David Normington
 */
class DB extends PDO
{
	 public function __construct($host, $user, $pass, $database)
	 {
		  parent::__construct('mysql:host='.$host.';dbname='.$database, $user, $pass);
	 }

	 /**
	  * Prepares a select stmt
	  * @param String $sql 
	  * @param String|Array $vars 
	  * @return PDOStatement
	  */
	 public function select($sql, $vars=array())
	 {
		  $stmt = $this->prepare($sql);

		  if(!is_array($vars)){
			   $vars = array($vars);
		  }

		  $stmt->execute($vars);
		  return $stmt;
	 }

	 /**
	  * For executing an insert statement when the 
	  * number of rows to insert isn't know until runtime 
	  * @param String $sql
	  * @param Array $vars
	  */
	 public function insertDynamic($sql, array $vars){
		  $insertValues = '';
		  foreach($vars as $var){
			   $insertValues .= ',("'.$var.'")';
		  }
		  $insertValues = trim($insertValues, ',');

		  $sql = str_replace('?', $insertValues, $sql);

	 	  return $this->query($sql);
	 } 
}
