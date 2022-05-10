<?php

namespace Core;

use PDO;
use PDOException;

class DB
{
	private static $_instance = null;
	private $_pdo,
		$_query,
		$_error = false,
		$_results,
		$_count = 0,
		$_lastInsertID = null;


	private function __construct()
	{						//constructor seskládá automaticky(je spuštěn když je voláná class) propojení 														k databázi private aby se nemohla volat dokola
		try {											//pokusí se o připojení pokud nevýjde -> catch Error

			$this->_pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . '; charset=utf8', DB_USER, DB_PASS);
			//vloží do var _pdo propojení s databází a napojí se na databázi

		} catch (PDOException $e) {						//zachytí případné errory které by se během připojení stránky s databází mohly vyskytnout
			die($e->getMessage());						//zabije zbytek kódu stránky pokud se připojení k databázi nepodaří a vypíše případné errory
		}
	}

	public static function getInstance()
	{				//zpřístupňuje přístup k databázi, hlavní funkce při komunikaci s databází
		if (!isset(self::$_instance)) {					//zkontroluje zdali již propojení není zajištěno popř propojí
			self::$_instance = new DB();
		}
		return self::$_instance; 						//navrátí hodnotu propojení s databází
	}

	public function query($sql, $params = array())
	{				//funkce query může být použita pro sql příkaz když komunikujeme s databází
		$this->_error = false;
		if ($this->_query = $this->_pdo->prepare($sql)) {		//zkontroluje zda byl zadán sql příkaz a připraví jej "prepare" sql = SELCT * FROM atd
			$x = 1;
			if (count($params)) {								//zkontroluje zda jsou k dispozici parametry (WHERE username = ... atd)
				foreach ($params as $param) {					//foreach zajistí že každý parametr kdyby jsme hledali např více jmen bude vyhledán
					$this->_query->bindValue($x, $param);		//bindne parametry tam kde je otazník "WHERE username = ?"
					$x++;
				}
			}
			if ($this->_query->execute()) {									//pokud dotaz (query) proběhne zprávně neboli je správně 																	zapsán a je executnut tak ->	
				$this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);	//zapíše do _results hodnotu všech vyhledaných objektů
				$this->_count = $this->_query->rowCount();					//_count zapíše do _count množství objektů
				$this->_lastInsertID = $this->_pdo->lastInsertID();
			} else {
				$this->_error = true;								//jinak vyhodí error pokud se execute neprovede správně
			}
		}
		return $this;														//navrátí celý objekt aby jsme s ním mohli dále pracovat
	}

	/*
// basic insert function zapisována přes fields  array - fiel => value
//	public function insert($table, $fields = array()){
//		$fieldString = '';
//		$valueString = '';
//		$values = [];
//		foreach ($fields as $field => $value) {
//			$fieldString .= '`' . $field . '`,';
//			$valueString .= '?,';
//			$values[] = $value;
//		}
//		$fieldString = rtrim($fieldString, ',');
//		$valueString = rtrim($valueString, ',');
//
//		$sql = "INSERT INTO {$table} ({$fieldString}) VALUES ({$valueString})";
//		if (!$this->query($sql, $values)->error()) {
//			return true;
//		}
//		return false;
//	}
*/



	//insert funkce zapisována přes string a array - 'name, surname, email', ['patrik', 'picka', 'mail@mail.com']
	public function insert($table, $fields = '', $values = [])
	{
		$fields = str_replace(' ', '', $fields);
		//$values = str_replace(' ', '', $values);
		$fields_arr = explode(',', $fields);
		//$values_arr = explode(',', $values);

		$fieldString = '';
		$valueString = '';
		foreach ($fields_arr as $field) {

			$fieldString .= '`' . $field . '`,';
			$valueString .= '?,';
		}
		$fieldString = rtrim($fieldString, ',');
		$valueString = rtrim($valueString, ',');

		$sql = "INSERT INTO {$table} ({$fieldString}) VALUES ({$valueString})";
		if (!$this->query($sql, $values)->error()) {
			return true;
		}
		return false;
	}

	/*
// basic update function zapisována přes fields  array - fiel => value
//	public function update($table, $id, $fields = []){
//		$fieldString = '';
//		$values = [];
//		$x = 1;
//		foreach ($fields as $field => $value) {
//			$fieldString .= "{$field} = ?";
//			if ($x < count($fields)) {
//				$fieldString .= ', ';
//			}
//			$x++;
//			$values[] = $value;
//		}
//
//		$sql =  "UPDATE {$table} SET {$fieldString} WHERE id = {$id}";
//		if (!$this->query($sql, $values)->error()) {
//			return true;
//		}
//		return false;
//	}
*/


	//update funkce zapisována přes string a array - 'name, surname, email', ['patrik', 'picka', 'mail@mail.com']
	public function update($table, $id, $fields, $values = [])
	{
		$fields = str_replace(' ', '', $fields);
		//$values = str_replace(' ', '', $values);
		$fields_arr = explode(',', $fields);
		//$values_arr = explode(',', $values);

		$fieldString = '';
		$x = 1;
		foreach ($fields_arr as $field) {
			$fieldString .= "{$field} = ?";
			if ($x < count($fields_arr)) {
				$fieldString .= ', ';
			}
			$x++;
		}
		$sql =  "UPDATE {$table} SET {$fieldString} WHERE id = {$id}";
		if (!$this->query($sql, $values)->error()) {
			return true;
		}
		return false;
	}


	protected function _find($table, $params = [])
	{
		$conditionString = '';
		$bind = [];
		$order = '';
		$limit = '';

		//conditions
		if (isset($params['conditions'])) {
			if (is_array($params['conditions'])) {
				foreach ($params['conditions'] as $conditions) {
					$conditionString .= ' ' . $conditions . ' AND';
				}
				$conditionString = trim($conditionString);
				$conditionString = rtrim($conditionString, ' AND');
			} else {
				$conditionString = $params['conditions'];
			}
			if ($conditionString != '') {
				$conditionString = ' WHERE ' . $conditionString;
			}
		}
		//bind
		if (array_key_exists('bind', $params)) {
			$bind = $params['bind'];
		}
		//order
		if (array_key_exists('order', $params)) {
			$order = ' ORDER BY ' . $params['order'];
		}
		//limit
		if (array_key_exists('limit', $params)) {
			$limit = ' LIMIT ' . $params['limit'];
		}

		$sql = "SELECT * FROM {$table}{$conditionString}{$order}{$limit}";
		if ($this->query($sql, $bind)) {
			if (!count($this->_results)) return false;

			return true;
		}
		return false;
	}


	public function get($table, $params = [])
	{
		if ($this->_find($table, $params)) {
			return $this->results();
		}
		return false;
	}

	public function getFirst($table, $params = [])
	{
		if ($this->_find($table, $params)) {
			return $this->first();
		}
		return false;
	}


	public function delete($table, $id)
	{
		$sql = "DELETE FROM {$table} WHERE id = {$id}";
		if (!$this->query($sql)->error()) {
			return true;
		}
		return false;
	}


	public function first()
	{
		return (!empty($this->_results)) ? $this->_results[0] : [];
	}

	public function count()
	{
		return $this->_count;
	}

	public function lastID()
	{
		return $this->_lastInsertID;
	}

	public function get_columns($table)
	{
		return $this->query("SHOW COLUMNS FROM {$table}")->results();
	}

	public function results()
	{
		return $this->_results;
	}

	public function error()
	{
		return $this->_error;
	}
}
