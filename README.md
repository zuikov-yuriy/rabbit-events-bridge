**Description**

It is a simple wrapper build atop on nuwber/rabbitevents library to provide routing-like interface to process incoming rabbit messages.
It makes routing key analogous to http routes, message to http request and defines special controllers to process the message.

**Installation**

1. You must add this repository as a source of packages in composer.json file.
`{
"type": "vcs",
"url": "https://github.com/Isreal-IT/rabbit-events-bridge"
}
`
2. Install the package using composer
   `composer install israel-it/rabbitevents-bridge`

3. Run following command:
   `php artisan rabbitevents-bridge:install`
   
**Usage**

1. To send message use nuwber/rabbitevents helper function publish:
```php
publish('gr.event-status.update', [
      'event_external_id' => $grEvent->external_id,
      'vendor_external_id' => $grEvent->enterprise_id,
      'status' => $status,
   ]);
```

2. To accept message on other side in routes-rabbit-events-bridge/routes.php you must define a routing key and its controller and method for processing.
```php
MessageRouter::add("vcrm.enterprise-tags.update", EnterpriseTagController::class . '@update');
```
   
3. Controllers for message processing must be extended from `TheP6\RabbitEventsBridge\Controllers\RabbitEventsBridgeController`. These Controllers have dependency injection configured for them, no need to make one.
4. You can define special cases for messages by extending `TheP6\RabbitEventsBridge\Message`. Here you can validate structure of the message in similiar manner as you do for Laravels http requests:
   ```php
   class UpdateExternalTagMessage extends Message
   {
      protected function rules(): array {
         
         return [
               'id' => [
                  'required',
                  'uuid',
               ],

               'name' => [
                  'required',
                  'min:2',
                  'max:255',
               ],
        ];
      }
   }
