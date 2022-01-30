## Wemalo app for sleekshop

## General info
This sleekshop - app is built for connecting your sleekshop - instance with an running wemalo - instance.

## Functionalities
* Sync all your products with your wemalo instance
* Sync your orders with your wemalo instance

## Todos
* we want to use the webhook triggered by wemalo when an order state changes.

## Setup
* Upload this code on your server in an public folder.
* Install this app in your sleekshop - backend ( you need your credentials from wemalo and api credentials from sleekshop). We recommend you to create a seperate channel.
* Create cronjob for fetching your orders generated. You must execute the file: cron_get_new_orders.php
* Create cronjob for uploading your orders to wemalo. You must execute the file: cron_transfer_orders.php
