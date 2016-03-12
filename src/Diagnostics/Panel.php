<?php

namespace FreezyBee\Restu\Diagnostics;

use FreezyBee\Restu\Api;
use FreezyBee\Restu\Http;
use Nette\Object;
use Nette\Utils\Callback;
use Nette\Utils\Json;
use Tracy\Debugger;
use Tracy\Dumper;
use Tracy\IBarPanel;

/**
 * Class Panel
 * @package FreezyBee\Restu\Diagnostics
 */
class Panel extends Object implements IBarPanel
{
    /**
     * @var Http\Resource[]
     */
    private $resources = [];

    /**
     * Renders HTML code for custom tab.
     * @return string
     */
    public function getTab()
    {
        $output = '<span><img width="16px" height="16px" style="float: none" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAACQFBMVEUAAAAA/4AAqlVAv4CAv4AAtm0Av2Agv2Amv2ZeyYYhxGkSxGVTyn0fwWgqwW0cw2tKx3k6xXEfw2k4xXIcw2k4xXIhw2smw2spxWwsxW4ew2o3xXEfwmk2xHAXwmguw20oxGwAwF8gwmonxGsXwmgew2kiw2oew2kiwmofxGoyxW8fwmoww24lw2sfw2sew2odw2oewmkAvlQew2kuxG0fw2ouxG5YyYGM1aMAwWRNx3sAwF4AwWEuxG6D0pyK1KGM1KLF580AwFwAwWEAwWIVwmghw2svxG+65MUAvlMfw2oiw2okw2u/5ckAwV4YwmlWyYBayoKo3bfK6dP1+/f9/v3///8AwWAAwWGr3rrA5coAtAAAujkAujsAu0YAvlEAvlYAv1MAv1QAv1oAv1sAwFwAwF0AwF4AwWEAw1kAxlcAyFwAy1oAzGAAz2MA0moA0mwA0m0P03AR03AZzG4exGof1HMgzW8g0XEhyW4iyW4izXAkxmwm1HUpxG0pxW0pxm4pyG8qw2wqy3EqzHEq0XMr0XQszXIxynIyxG9CxnZUyX9VyX9WyYBYyYFZyYFe1Ih2z5N60Zd90Jh90ZiD0pyF056P1qWR1qaR1qeT2qmU16mV16md2a+e2rCl3LWp3bis37ut37yu4L2w4L6y4MC14cK24sO75Me+5cm+5crA5crF58/J6dLP69fQ69jX7t3g8uXi8ubi8+fk8+jr9u7y+fT0+vX1+vf2+/f3+/j4/Pn5/Pr6/fv8/fz///88X8OSAAAAW3RSTlMAAgMEBAcICBQmJysrMTE3N3yDhIiIjI2QkJGRlpaamqWmsLC8wsLExc7O0dHW3N3e3/T19fb29/f4+Pr6+vr6+vr7+/v7+/v7/Pz8/Pz9/f39/f39/f3+/v7+ZIjSjwAAAQpJREFUGNMFwTtLw2AUBuD3nC9pkl6IUNHBS9HZSXAq4iYFwcveX9Df46Lo4OImziI4FS+zIIgFrZdgok3aRpK0ab/j8xBYY6biFnUyiIcgIdZqYUfplGzlnnyDlBgrTe81UxPPK64HEQi1/fvlFgDg0Ns885XbenNafz9pGFn1p3z3jivGZ51YOLt5Cbc7U4ddbQMiokWxYVHVKOazBDbZbcwboUWOoUnUpFQCIN6DGmpOzF8Bet0M/avF8TTlAY9E4F/34W68p9TjeLzWZhTMj4SXgq3HjOMj2z9Gbj37OA+i24SA1YPahW3n+aiZdC+/iARze7ZlguN2VG6cEkhQdqqOTntJWmD5B9pfdi4dpnc7AAAAAElFTkSuQmCC"> ';

        if ($this->resources) {
            $totalTime = 0;
            foreach ($this->resources as $resource) {
                $totalTime += $resource->getTime();
            }
            $output .= count($this->resources) . ' call' . (count($this->resources) > 1 ? 's' : '') .
                ' / ' . sprintf('%0.2f', $totalTime) . ' s';

        } else {
            $output .= 'Restu';
        }

        return $output;
    }

    /**
     * Renders HTML code for custom panel.
     * @return string
     */
    public function getPanel()
    {
        if (!$this->resources) {
            return null;
        }

        $esc = Callback::closure('Latte\Runtime\Filters::escapeHtml');

        $dumpItems = function (Http\Resource $resource) {
            $r = $resource->getRequest();
            $s = '<h3>Headers</h3>';
            $s .= Dumper::toHtml($r->getHeaders(), ['collapse' => true]);
            $s .= '<h3>Contents</h3>';
            $request = $s . Dumper::toHtml(Json::decode((string)$r->getBody()), ['collapse' => true]);

            $r = $resource->getResponse();
            $s = '<h3>Headers</h3>';
            $s .= Dumper::toHtml($r->getHeaders(), ['collapse' => true]);
            $s .= '<h3>Contents</h3>';
            $response = $s. Dumper::toHtml($resource->getResult(), ['collapse' => true]);

            return (object) ['request' => $request, 'response' => $response];
        };

        ob_start();
        include __DIR__ . '/panel.phtml';
        return ob_get_clean();
    }

    /**
     * @param Http\Resource $resource
     */
    public function finish(Http\Resource $resource)
    {
        $this->resources[] = $resource;
    }

    /**
     * @param Api $api
     */
    public function register(Api $api)
    {
        $api->onResponse[] = $this->finish;

        Debugger::getBar()->addPanel($this);
    }
}
