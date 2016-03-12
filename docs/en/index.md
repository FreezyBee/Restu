Quickstart
==========


Installation
------------

The best way to install FreezyBee/Restu is using  [Composer](http://getcomposer.org/):

```sh
$ composer require freezy-bee/restu
```

With Nette `2.3` and newer, you can enable the extension using your neon config.

```yml
extensions:
	restu: FreezyBee\Restu\DI\RestuExtension
```


Minimal configuration
------------------

```yml
restu:
	apiKey: **your api key**
```


Full configuration
------------------

```yml
restu:
	apiKey: **your api key**
	restaurantId: **restaurant id**
    apiUrl: https://rest-api.restu.cz/
    version: v1
```


Debugging
---------

The extension monitors request and response, when in debug mode. All that information is available in Tracy panel



Example
-------

```php

class HomepagePresenter extends Presenter
{
    /** @var \FreezyBee\Restu\Api @inject */
    public $api;

    public function actionTest()
    {

        try {
            // get restaurants
            $result = $this->api->call('GET', 'restaurants');

            // create restaurant service (restaurantId is defined in config)
            /** @var \FreezyBee\Restu\Service\Restaurants $restaurantsService1 **/
            $restaurantsService1 = $this->api->createService(\FreezyBee\Restu\Service\Restaurants::class);

            // create restaurant service (restaurantId defined in parameter)
            /** @var \FreezyBee\Restu\Service\Restaurants $restaurantsService2 **/
            $restaurantsService2 = $this->api->createService(\FreezyBee\Restu\Service\Restaurants::class, 'rest2', ['id' => 10000]);

            // create restaurant service (restaurantId is defined in config)
            /** @var \FreezyBee\Restu\Service\User $userService **/
            $userService = $this->api->createService(\FreezyBee\Restu\Service\User::class);
            
            $menus = $restaurantService1->getMenus();
            
            ...

        } catch (RestuException $e) {
            Debugger::log($e);
        }
    }
}
```
