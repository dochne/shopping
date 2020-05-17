<?php


namespace Dochne\Shopping\Actions\Index;


use Dochne\Shopping\Actions\AbstractAction;
use Dochne\Shopping\Service\PrintService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PrintAction extends AbstractAction
{
    /**
     * @var PrintService
     */
    private $printService;

    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        if ($this->printService->print()) {
            return $this->json($response, [], 200);
        }

        return $this->json($response, [
            "error" => "No content to print"
        ], 400);
    }
}