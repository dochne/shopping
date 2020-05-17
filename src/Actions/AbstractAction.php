<?php

namespace Dochne\Shopping\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Twig\Environment as Twig;

abstract class AbstractAction
{
    /**
     * @var Twig
     * @Inject("twig")
     */
    private $twig;

    abstract public function __invoke(Request $request, Response $response) : Response;

    protected function render(Response $response, array $context = []) : Response
    {
        $exploded = explode("\\", static::class);
        $file = str_replace("Action", "", array_pop($exploded));
        $file = strtolower($file);
        $folder = array_pop($exploded);

        $response->getBody()->write(
            $this->twig->render($folder . "/" . $file . ".html.twig", $context)
        );

        return $response;
    }

    protected function json(Response $response, array $data, int $statusCode = 200) : Response
    {
        $response->getBody()->write(json_encode($data));

        $response = $response->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');

        return $response;
    }
}