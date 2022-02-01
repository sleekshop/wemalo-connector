<?php

class wemaloCtl
{

  function __construct()
  {

  }


  public static function CreateOrder($order=array(),$count=0)
   {
     $endpoint="https://connect.wemalo.com/v1/goodsOrder/add";
     $method="POST";
     $args=array();
     $meta=array("externalId"=>$order["id"],"orderNumber"=>$order["order_number"]);
     $receiver=array(array("name1"=>$order["delivery_firstname"] . " " .$order["delivery_lastname"],
     "street"=>$order["delivery_street"],"streetNumber"=>$order["delivery_number"],
     "countryCode2Letter"=>"DE","city"=>$order["delivery_city"],"zip"=>$order["delivery_zip"],"email"=>$order["email"]));
     $args["meta"]=$meta;
     $args["meta"]["receiver"]=$receiver;
     $cart=array();
     foreach($order["cart"]["contents"] as $element)
     {
      if($element["id_product"]>0)
      {
       $piece=array();
       $piece["externalId"]=$element["id_product"];
       $piece["quantity"]=$element["quantity"];
       $cart[]=$piece;
      }
     }
     $args["positions"]=$cart;
     $res=self::sendReq($endpoint,$method,WEMALO_TOKEN,$args);
     sleep(1);
     $res=json_decode($res);
     if($res->status=="404" AND $count==0)
      {
        if(self::CreateCartProducts($order["cart"]["contents"]))
         {
          $res=self::CreateOrder($order,1);
          if($res->status=="404") return(array("error"=>1));
         }
      }
     return($res);
   }


   public static function CreateProduct($args=array())
   {
     $endpoint="https://connect.wemalo.com/v1/product/add";
     $method="POST";
     $final_args=array();
     foreach($args as $key=>$value)
      {
        $final_args[$key]=str_replace('"',"'",$value);
      }
     $res=self::sendReq($endpoint,$method,WEMALO_TOKEN,$final_args);
     sleep(1);
     return($res);
   }


  private static function CreateCartProducts($cart=array())
   {
    foreach($cart as $element)
     {
       if($element["id_product"]>0)
        {
        if($element["element_number"]=="") $element["element_number"]=$element["id_product"];
         $args=array("externalId"=>$element["id_product"],"sku"=>$element["element_number"],"name"=>$element["name"],"description"=>$element["description"],"productGroup"=>"group1");
         self::CreateProduct($args);
        }
     }
     return(true);
   }


  private static function sendReq($endpoint="",$method="",$token="",$args=array())
   {

    // Create a new cURL resource
    $ch = curl_init($endpoint);
    $payload = json_encode($args);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    // Set the content type to application/json and add Authorization token
    $token='Authorization:JWT ' . $token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json',$token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return($result);
   }


}

?>
