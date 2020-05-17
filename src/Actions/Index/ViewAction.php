<?php


namespace Dochne\Shopping\Actions\Index;

use Dochne\Shopping\Actions\AbstractAction;
use Dochne\Shopping\Database\Database;
use Dochne\Shopping\Repository\CategoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ViewAction extends AbstractAction
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        return $this->render($response, [
            "categories" => $this->categoryRepository->all()
        ]);
    }
}