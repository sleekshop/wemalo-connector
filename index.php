<?php
header('Content-Type: text/html; charset=utf-8');

include("./vendor/sleekcommerce/init.inc.php");
include("./vendor/wemalo/wemalo.inc.php");
ConfCtl::CheckConf();
include("./conf.inc.php");

$action=$_REQUEST["id_action"];
$sync=$_REQUEST["sync"];
$save=$_REQUEST["save"];
$remote_session=$_REQUEST["remote_session"];
if($remote_session!="")
 {
   $sr=new SleekshopRequest();
   $res=$sr->get_user_data($remote_session);
   $res=json_decode($res);
   if((string)$res->object=="error") die("PERMISSION_DENIED");
 }
if($action=="") $action=1;
if(SERVER=="" OR LICENCE_USERNAME=="" OR LICENCE_PASSWORD=="" OR APPLICATION_TOKEN=="" OR LICENCE_SECRET_KEY=="") $action=2;
if($save==1) $action=3;
//When application-page is called
if($action==1)
{
  if($remote_session=="")
  {
   $token=$_GET["token"];
   $sr=new SleekshopRequest();
   $res=$sr->instant_login($token);
   $res=json_decode($res);
   $status=(string)$res->status;
   if($status!="SUCCESS") die("PERMISSION_DENIED");
   $remote_session=(string)$res->remote_session;
  }
   echo "<h3>Welcome to the wemalo app for sleekshop (initial-setup) / v 1.0.0 beta</h3>";
   echo "<a href='./index.php?id_action=1&remote_session=".$remote_session."'>start</a> | <a href='./index.php?id_action=4&remote_session=".$remote_session."'>sync products</a> | <a href='./index.php?id_action=5&remote_session=".$remote_session."'>logs</a>";
   echo "<hr>";
   echo "<form method='post' action='index.php?id_action=3&save=1'>
   Api Endpoint<br>
   <input type='text' name='api_endpoint' placeholder='API Endpoint' value='".SERVER."'><br><br>
   Licence username<br>
   <input type='text' name='licence_username' placeholder='Licence username' value='".LICENCE_USERNAME."'><br><br>
   Licence password<br>
   <input type='text' name='licence_password' placeholder='Licence password' value='".LICENCE_PASSWORD."'><br><br>
   Licence secret key<br>
   <input type='text' name='licence_secret_key' placeholder='Licence secret key' value='".LICENCE_SECRET_KEY."'><br><br>
   Application token<br>
   <input type='text' name='application_token' placeholder='Application token' value='".APPLICATION_TOKEN."'><br><br>
   <hr>
   wemalo token<br>
   <input type='text' name='wemalo_token' placeholder='wemalo token' value='".WEMALO_TOKEN."'><br><br>";
   echo "<input type='hidden' name='remote_session' value='".$remote_session."'>";
   echo "<input type='submit' value='submit'></form>";
}

//When configuration is not complete
if($action==2)
 {
   echo "<h3>Welcome to the wemalo app for sleekshop (initial-setup) / v 1.0.0 beta</h3>";
   echo "<form method='post' action='index.php?id_action=3&save=1'>
   Api Endpoint<br>
   <input type='text' name='api_endpoint' placeholder='API Endpoint' value='".SERVER."'><br><br>
   Licence username<br>
   <input type='text' name='licence_username' placeholder='Licence username' value='".LICENCE_USERNAME."'><br><br>
   Licence password<br>
   <input type='text' name='licence_password' placeholder='Licence password' value='".LICENCE_PASSWORD."'><br><br>
   Licence secret key<br>
   <input type='text' name='licence_secret_key' placeholder='Licence secret key' value='".LICENCE_SECRET_KEY."'><br><br>
   Application token<br>
   <input type='text' name='application_token' placeholder='Application token' value='".APPLICATION_TOKEN."'><br><br>
   <hr>
   wemalo token<br>
   <input type='text' name='wemalo_token' placeholder='wemalo token' value='".WEMALO_TOKEN."'><br><br>";
   echo "<input type='hidden' name='remote_session' value='".$remote_session."'>";
   echo "<input type='submit' value='submit'></form>";
 }


 //When configuration is posted
 if($action==3)
  {
    $api_endpoint=$_POST["api_endpoint"];
    $licence_username=$_POST["licence_username"];
    $licence_password=$_POST["licence_password"];
    $licence_secret_key=$_POST["licence_secret_key"];
    $application_token=$_POST["application_token"];
    $wemalo_token=$_POST["wemalo_token"];
    ConfCtl::CreateConf($api_endpoint,$licence_username,$licence_password,$licence_secret_key,$application_token,$wemalo_token);
    echo "<h3>Welcome to the wemalo app for sleekshop / v 1.0.0 beta</h3>";
    echo "Updated the configuration, click <a href='index.php?remote_session=".$remote_session."'>here</a>";
  }


  if($action==4)
  {
    if($remote_session=="")
    {
     $token=$_GET["token"];
     $sr=new SleekshopRequest();
     $res=$sr->instant_login($token);
     $res=json_decode($res);
     $status=(string)$res->status;
     if($status!="SUCCESS") die("PERMISSION_DENIED");
     $remote_session=(string)$res->remote_session;
    }

    if($sync==1)
     {
       $constraint=array();
       $res=ShopobjectsCtl::SearchProducts($constraint,0,0);
       foreach($res["products"] as $product)
        {
          file_put_contents("./syncProducts/" . $product["id"] . ".php", serialize($product));
        }
        $res=ShopobjectsCtl::SearchWarehouseEntities($constraint,0,0);
        foreach($res["warehouse_entities"] as $entity)
         {
           file_put_contents("./syncWarehouseEntities/" . $entity["id"] . ".php", serialize($entity));
         }
        $sync=2;
     }

     echo "<h3>Welcome to the wemalo app for sleekshop (initial-setup) / v 1.0.0 beta</h3>";
     echo "<a href='./index.php?id_action=1&remote_session=".$remote_session."'>start</a> | <a href='./index.php?id_action=4&remote_session=".$remote_session."'>sync products</a> | <a href='./index.php?id_action=5&remote_session=".$remote_session."'>logs</a>";
     echo "<hr>";
     if($sync==2)
      {
        echo "<h3>Products and Warehouse entities synced</h3>";
      }
      else {
        echo "<form method='post' action='index.php?id_action=4&sync=1'>";
        echo "<p>click the button to synchronize all products and warehouse entities with your wemalo backend</p>";
        echo "<input type='hidden' name='remote_session' value='".$remote_session."'>";
        echo "<input type='submit' value='sync products'></form>";
      }

  }


  if($action==5)
  {
    if($remote_session=="")
    {
     $token=$_GET["token"];
     $sr=new SleekshopRequest();
     $res=$sr->instant_login($token);
     $res=json_decode($res);
     $status=(string)$res->status;
     if($status!="SUCCESS") die("PERMISSION_DENIED");
     $remote_session=(string)$res->remote_session;
    }
    $log=file_get_contents('./logs/log_'.date("j.n.Y").'.log');
     echo "<h3>Welcome to the wemalo app for sleekshop (initial-setup) / v 1.0.0 beta</h3>";
     echo "<a href='./index.php?id_action=1&remote_session=".$remote_session."'>start</a> | <a href='./index.php?id_action=4&remote_session=".$remote_session."'>sync products</a> | <a href='./index.php?id_action=5&remote_session=".$remote_session."'>logs</a>";
     echo "<hr>";
     echo "<textarea cols='100' rows='20'>".$log."</textarea>";

  }


 ?>
