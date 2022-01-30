<?php
error_reporting(0);
include("./conf.inc.php");
include("./vendor/sleekcommerce/init.inc.php");
include("./vendor/wemalo/wemalo.inc.php");
$i=0;
$error="";
$files = scandir('./newOrders');
foreach($files as $file) {
  if($file != "." AND $file != "..")
   {
     $id=file_get_contents("./newOrders/".$file);
     $id=json_decode($id);
     $id=$id->id;
     $constraint=array("main.id_order"=>$id);
     $res=orderCtl::SearchOrders($constraint,0,0);
     $res=array_shift($res);
     if($res["order_payment_state"]=="PAYMENT_RECEIVED")
      {
        $r=wemaloCtl::CreateOrder($res);
        if($r->error==1)
         {
           $error=$error . "error: " . $res["id"] . ", ";
         }
         else {
           copy("./newOrders/".$file, "./transferredOrders/".$file);
           unlink("./newOrders/".$file);
         }
      }
   }
}
$log=date("Y-m-d H-i-s")." - cron: get new orders, added ".$i." new orders." . $error ."\n";
file_put_contents('./logs/log_'.date("j.n.Y").'.log', $log, FILE_APPEND);
die("CRON_DONE: ". $i . " " . $error);
 ?>
