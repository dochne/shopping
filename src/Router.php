<?php


namespace Dochne\Shopping;


use Dochne\Shopping\Actions\Index\PrintAction;
use Dochne\Shopping\Actions\Index\UpdateAction;
use Dochne\Shopping\Actions\Index\ViewAction;
use Slim\App;

class Router
{
    public function apply(App $app)
    {
        $app->get('/', ViewAction::class);
        $app->put('/category/{categoryId}', UpdateAction::class);
        $app->post('/print', PrintAction::class);
    }

}