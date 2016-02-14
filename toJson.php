<?php
require "Storage.php";

class Access{
  protected $_db;

  //Konstruktor
  public function __construct()
  {
    $this->_db = new Storage();
  }

  public function toJson($datum){
    $rest = substr($datum, 0, -1);
    $date = DateTime::createFromFormat('Ymd',$rest);
    $reformDate = $date->format('Y-m-d');

    $dir1 = "";
    if(!is_dir($dir1 .=  "uploads/json/".$reformDate)){
        mkdir($dir1, 0777, true);
        chmod($dir1, 0777);
    }

    $uidwebid = $this->_db->select("SELECT AdId,CuId,Section,SalesValue,Impressions FROM sgs_ut");
    $uidwebid_encode = json_encode($uidwebid);
    $uidwebid_encode = str_replace('\\u0000', "", $uidwebid_encode);

    $file_uidwebid = fopen($dir1."/uidwebid.json", "w") or die("Unable to open file!");
    fwrite($file_uidwebid, $uidwebid_encode);
    fclose($file_uidwebid);

    $browserid = $this->_db->select("SELECT count(*) as count FROM browserid");
    $campaignid = $this->_db->select("SELECT count(*) as count FROM campaignid");
    $cityid = $this->_db->select("SELECT count(*) as count FROM cityid");
    $impressions = $this->_db->select("SELECT count(*) as count FROM impressions");
    $osid = $this->_db->select("SELECT count(*) as count FROM osid");
    $websiteid = $this->_db->select("SELECT count(*) as count FROM websiteid");

    $statistic = array('browserid' => $browserid[0]['count'], 'campaignid' => $campaignid[0]['count'],'cityid' => $cityid[0]['count'],'impressions' => $impressions[0]['count'],'osid' => $osid[0]['count'],'websiteid' => $websiteid[0]['count'],);
    $statistic_encode = json_encode($statistic);

    $file_statistic = fopen($dir1."/statistic.json", "w") or die("Unable to open file!");
    fwrite($file_statistic, $statistic_encode);
    fclose($file_statistic);
  }

  public function getJson($datum,$file){
    $rest = substr($datum, 0, -1);
    $date = DateTime::createFromFormat('Ymd',$rest);
    $reformDate = $date->format('Y-m-d');

    $str = file_get_contents('uploads/json/'.$reformDate.'/'.$file.'.json');

    $json = json_decode($str, true); // decode the JSON into an associative array

    echo '<pre>' . print_r($json, true) . '</pre>';
  }


} //end of class Access

/*
* Die abgeleitete Klasse von Access-Klasse
*/
class Ot_Access extends Access{
  public function __construct($table,$datum)
  {
    parent::__construct();
    $this->table = $table;
    $this->datum = $datum;
    $this->file = 'uidwebid';
  }
  public function call_toJson()
  {
    return $this->toJson($this->datum);
  }
  public function call_getJson()
  {
    return $this->getJson($this->datum,$this->file);
  }
}

//Erstellen eines neuen Objekts von Access-Klasse
$obj = new Access;

//Aufruf der Methode getDatum() um das aktuelle Datum zu finden
$datum = '20160214/';

/*
* Auswahl der Tabelle
* optionale Tabellen:
* $table = array("cf","gl","ir","kv","kw","tc","ga");
*/
$table = array("ga");

/*
* Bindung der Tabelle mit Schlüssel(key) und Datum
*/
$stack = array();
foreach ($table as $key) {
  $stack[]= new Ot_Access($key,$datum);
}

/*
* Einstellung des Vorgangprozesses
*/
foreach ($stack as $key) {
  $key->call_toJson();
  //$key->call_getJson();
}
?>
