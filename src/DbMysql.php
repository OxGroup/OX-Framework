<?php

/**
 * Created by OxProfitGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:44
 */

//CRUD Technology :)

namespace Ox;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Migrations\Configuration\Configuration;

/**
 * Class DbMysql
 * @package Ox
 */
class DbMysql
{

	public $dbh, $CharFix, $whereTpl, $freeWhere, $cfg;
	protected $data = array();
	protected $dataParams, $dataTpl, $whereParams, $whereData;


	public function __construct()
	{
		$config = new \Doctrine\DBAL\Configuration();
		$this->dbh = DriverManager::getConnection(array(
			'dbname' => Config::$dbname,
			'user' => Config::$dbuser,
			'password' => Config::$dbuserpass,
			'host' => Config::$dbhost,
			'driver' => Config::$dbDriver,
			'driverOptions' => array(
				1002 => 'SET NAMES utf8'
			)
		), $config);

	}

	/**
	 * @return array|string
	 */
	public function update()
	{
		$this->setCfg();

		if (!isset($this->table)) {
			return "No isset table";
		} elseif (!isset($this->data)) {
			return "No isset data";
		} else {

			$orderby = "";
			if (isset($this->orderby)) {
				$orderby = $this->orderby;
			}

			$limit = "";
			if (isset($this->limit)) {
				$limit = $this->limit;
			}

			$this->dataTpl = str_replace(" and ", ', ', $this->dataTpl);
			$sqltxt = "UPDATE `" . $this->table . "` SET {$this->dataTpl} {$this->whereTpl} {$orderby} {$limit};";
			$sth = $this->dbh->prepare($sqltxt);
			$sth->execute($this->whereParams + $this->dataParams);
			$this->clean();
			return (object)array("cous" => $sth->rowCount(), "sqlquery" => $sqltxt, "errorInfo" => $sth->errorInfo());

		}
	}

	private function setCfg()
	{

		foreach ($this->cfg as $k => $v) {
			$this->{$k} = $v;
		}

		if (isset($this->where) and $this->where != "") {
			$where = $this->buildParams($this->where);

			$where['tpl'] .= $this->freeWhere;
			if ($where['tpl'] != "")
				$where['tpl'] = "WHERE " . $where['tpl'];
			$this->whereTpl = $where['tpl'];
			$this->whereParams = $where['data'];
		}elseif(!empty($this->freeWhere)){
			$where=array();
			$where['tpl'] = "WHERE " . $this->freeWhere;
			$this->whereTpl = $where['tpl'];
		}

		if (!empty($this->data) and $this->data != "") {
			$where = $this->buildParams($this->data, "data");
			$this->dataTpl = $where['tpl'];
			$this->dataParams = $where['data'];

		}

		if (isset($this->order) and $this->order != "") {
				$this->orderby = " ORDER BY {$this->order}";
		}

		if (isset($this->limit) and $this->limit != "") {
			$this->limit = " limit " . $this->limit;
		}

	}

	/**
	 * @param array  $params
	 * @param string $sub
	 *
	 * @return array
	 */
	protected function buildParams($params = array(), $sub = "")
	{
		$doubleKeys=array();
		$cous=0;
		$CharFix = new CharFix();
		foreach ($params as $key => $val) {

			$keyChar = explode("|", $key);
			if (isset($keyChar['1'])) {


				switch ($keyChar['1']) {
					case("noSpec"):
						$val = $CharFix->noSpec($val);
						break;

					case("noHtml"):
						$val = $CharFix->noHtml($val);
						break;

					case("charNumber"):
						$val = $CharFix->charNumber($val);
						break;

					case("char"):
						$val = $CharFix->char($val);
						break;

					case("number"):
						$val = $CharFix->number($val);
						break;
				}
			}
			if (isset($keyChar['2'])) {
				switch ($keyChar['2']) {
					case("in"):
						$spec = " IN ";
						$specStart = "(";
						$specStop = ")";
						$key = $keyChar['0'];
						break;

					case("not in"):
						$spec = " IN ";
						$specStart = "(";
						$specStop = ")";
						$key = $keyChar['0'];
						break;
						
					default:
						$spec = $keyChar['2'];
						$specStart = "";
						$specStop = "";
						$key = $keyChar['0'];
						break;
				}
			} else {
				$spec = "=";
				$specStart = "";
				$specStop = "";
				$key = $keyChar['0'];
			}

			$key = $CharFix->charNumber($key);


			if(isset($doubleKeys[$sub . $key])){
				$doubleKeys[$sub . $key. $cous++] = 0;
				$dataPocess[$sub . $key. $cous] = $val;
				$tplKey="{$sub}{$key}{$cous}";
			}else {
				$dataPocess[$sub . $key] = $val;
				$doubleKeys[$sub . $key] = 0;
				$tplKey = "{$sub}{$key}";
			}
			$str = "`{$keyChar['0']}`{$spec}{$specStart}:{$tplKey}{$specStop}";
			if (!isset($tplProcess)) {
				$tplProcess = $str;
			} else {
				$tplProcess .= " and " . $str;
			}
		}
		if (empty($dataPocess))
			$dataPocess = array();
		if (empty($tplProcess))
			$tplProcess = "";
		return array("data" => $dataPocess, "tpl" => $tplProcess);

	}

	public function Clean()
	{
		foreach ($this->cfg as $k => $v) {
			$this->{$k} = "";
		}
		$this->whereParams = (object)array();

	}

	/**
	 * @return array|string
	 */
	public function delete()
	{
		$this->setCfg();

		if (!isset($this->table)) {
			return "No isset table";
		} else {

			$orderby = "";
			if (isset($this->orderby)) {
				$orderby = $this->orderby;
			}

			$limit = "";
			if (isset($this->limit)) {
				$limit = $this->limit;
			}
			$sqltxt = "DELETE FROM `" . $this->table . "` {$this->whereTpl} {$orderby} {$limit};";
			$sth = $this->dbh->prepare($sqltxt);
			$sth->execute($this->whereParams);
			$this->clean();
			return (object)array("cous" => $sth->rowCount(), "rows" => $result, "sqlquery" => $sqltxt, "errorInfo" => $sth->errorInfo());

		}
	}

	/**
	 * @return array|string
	 */
	public function create()
	{
		$this->setCfg();

		if (!isset($this->table)) {
			return "No isset table";
		} elseif (!isset($this->dataParams)) {
			return "No isset data";
		} else {
			$this->dataTpl = str_replace(" and ", ', ', $this->dataTpl);
			$sqltxt = "INSERT INTO `" . $this->table . "` SET {$this->dataTpl};";
			$sth = $this->dbh->prepare($sqltxt);
			if ($this->data == "")
				$this->data = array();

			$sth->execute($this->dataParams);
			
			$this->clean();
			return (object)array("cous" => $sth->rowCount(), "id" => $this->dbh->lastInsertId(), "sqlquery" => $sqltxt, "errorInfo" => $sth->errorInfo());


		}
	}

	/**
	 * @return object|string
	 * @cous
	 * @rows
	 */
	public function read()
	{
		$this->setCfg();

		if (!isset($this->table)) {
			return "No isset table";
		} else {

			$orderby = "";
			if (isset($this->orderby)) {
				$orderby = $this->orderby;
			}

			$limit = "";
			if (isset($this->limit)) {
				$limit = $this->limit;
			}

			try {

				$sqltxt = "SELECT * FROM `" . $this->table . "` {$this->whereTpl} {$orderby} {$limit};";
				$sth = $this->dbh->prepare($sqltxt);

				print_r($sqltxt);
				print_r($this->whereParams);


				$sth->execute($this->whereParams);
				$result = $sth->fetchAll(\PDO::FETCH_OBJ);
				$this->clean();
				return (object)array("cous" => $sth->rowCount(), "rows" => $result, "sqlquery" => $sqltxt, "errorInfo" => $sth->errorInfo());
			} catch (\Exception $e) {
				// catch( Exception $e ) will give no warning, but will not catch Exception
				echo "ERROR: $e";
			}

		}

	}

	/**
	 * @return $this
	 */
	public function ShowArray()
	{

		return $this;

	}
	
	
	public function transaction($array){

		try {
			// do stuff

			$this->dbh->beginTransaction(); // start inner transaction, nesting level 2

			foreach ($array as $val) {
				$stmt = $this->dbh->prepare($val);
				$stmt->execute();
			}

			try {
				// do stuff
				$this->dbh->commit(); // commits inner transaction, does not start a new one
				return true;
			} catch (\Exception $e) {
				$this->dbh->rollback(); // rolls back inner transaction, does not start a new one
				return false;
			}

			$this->dbh->commit(); // commits outer transaction, and immediately starts a new one
		} catch (\Exception $e) {
			$this->dbh->rollback(); // rolls back outer transaction, and immediately starts a new one
			return false;
		}
	}

}
