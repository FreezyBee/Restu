Nápověda
========


Instalace
---------

Nejsnazší způsob instalace FreezyBee/Restu je přes [Composer](http://getcomposer.org/):

```sh
$ composer require freezy-bee/restu
```

S Nette `2.3` určitě použijte registraci přes neon config.

```yml
extensions:
	restu: FreezyBee\Restu\DI\RestuExtension
```

Minimální konfigurace
------------------

```yml
restu:
	apiKey: **vas klic k api**
```


Celková konfigurace
------------------

```yml
restu:
	apiKey: **vas klic k api**
	restaurantId: **restaurant id**
    apiUrl: https://rest-api.restu.cz/
    version: v1
```


Debugging
---------

Rozšíření monitoruje požadavky a odpovědi na restu server. Informace jsou v Tracy panelu.


Příklad
-------

```php

class HomepagePresenter extends Presenter
{
    /** @var \FreezyBee\Restu\Api @inject */
    public $api;

    public function actionTest()
    {

        try {
            // hrube pouzivani api
            $result = $this->api->call('GET', 'restaurants');

            // priklad vytvoreni a pouziti sluzeb

            // vytvori sluzbu restaurace (restaurantId musi byt definovano v configu)
            /** @var \FreezyBee\Restu\Service\Restaurants $restaurantsService1 **/
            $restaurantsService1 = $this->api->createService(\FreezyBee\Restu\Service\Restaurants::class);

            // vytvori sluzbu restaurace (restaurantId je mozno vlozit pres parametr funkce)
            // styl volani: typ sluzby, nejaky nazev(jedinecny), parametry
            /** @var \FreezyBee\Restu\Service\Restaurants $restaurantsService2 **/
            $restaurantsService2 = $this->api->createService(\FreezyBee\Restu\Service\Restaurants::class, 'rest2', ['id' => 10000]);

            // vytvori sluzbu uzivatel s nastavenym jazykem
            /** @var \FreezyBee\Restu\Service\User $userService **/
            $userService = $this->api->createService(\FreezyBee\Restu\Service\User::class, '', ['language' => 'en']);
            
            // zavolani metody sluzby
            $menus = $restaurantService1->getMenus();
            
            ...

        } catch (RestuException $e) {
            // RestuException je hlavni, da se ale chytat i jeji child - viz. kod
            Debugger::log($e);
        }
    }
}
```
