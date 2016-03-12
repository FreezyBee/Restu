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

        // get restaurants
        try {
            $result = $this->api->call('GET', 'restaurants');

            ...

        } catch (RestuException $e) {
            Debugger::log($e);
        }

        // create new Campaign
        $params = [
            "recipients" => [
                "list_id" => "aaa2**dsds"
            ],
            "type" => "regular",
            "settings" => [
                "subject_line" => "TEST",
                "reply_to" => "test@email.com",
                "from_name" => "Customer Service"
            ]
        ];

        try {
            $result = $this->api->call('POST', '/campaigns', $params);
            dump($result);

        } catch (RestuException $e) {
            Debugger::log($e);
        }
    }
}
```
