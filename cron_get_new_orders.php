<?php
error_reporting(0);
//ini_set('display_errors', '1');
include("./conf.inc.php");
include("./vendor/sleekcommerce/init.inc.php");
include("./vendor/wemalo/wemalo.inc.php");
//Here we should build a condition which checks wether configuration file exists or not.
if(!file_exists("./cfg.php"))
{
  $timestamp=date("Y-m-d H:i:s");
  $piece=array("timestamp"=>$timestamp);
  $piece=json_encode($piece);
  file_put_contents("./cfg.php",$piece);
}
$timestamp=file_get_contents("./cfg.php");
$timestamp=json_decode($timestamp);
$timestamp=$timestamp->timestamp;
$constraint=array("main.creation_date"=>array(">",$timestamp));
$res=orderCtl::SearchOrders($constraint,0,0);
$i=0;
foreach($res as $order)
 {
   $i++;
   $piece=array("id"=>$order["id"]);
   $piece=json_encode($piece);
   file_put_contents("./newOrders/".$order["id"].".php",$piece);
 }
$log=date("Y-m-d H-i-s")." - cron: get new orders, added ".$i." new orders.\n";
file_put_contents('./logs/log_'.date("j.n.Y").'.log', $log, FILE_APPEND);
$timestamp=date("Y-m-d H:i:s");
$piece=array("timestamp"=>$timestamp);
$piece=json_encode($piece);
file_put_contents("./cfg.php",$piece);
//NOW syncing products if neccessary
$files = scandir('./syncProducts');
$max_files=20;
foreach($files as $file) {
  if($file != "." AND $file != "..")
   {
     $p=file_get_contents("./syncProducts/".$file);
     $p=unserialize($p);
      if($p["element_number"]=="") $p["element_number"]=$p["id"];
       $args=array("externalId"=>$p["id"],"sku"=>$p["element_number"],"name"=>$p["attributes"]["name"]["value"],"description"=>$p["attributes"]["short_description"]["value"],"productGroup"=>"group1");
       $res=wemaloCtl::CreateProduct($args);
       $res=json_decode($res);
       if($res->Success)
       {
         unlink("./syncProducts/".$file);
       }
       $max_files--;
      sleep(1);
      if($max_files==0) break;
   }
 }
//End of syncing
die("CRON_DONE: ". $i);
 ?>