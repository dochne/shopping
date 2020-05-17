<?php

namespace Dochne\Shopping\Actions\Index;

use Dochne\Shopping\Actions\AbstractAction;
use Dochne\Shopping\Repository\CategoryRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UpdateAction extends AbstractAction
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string|null $categoryId - should never be null, but Liskov Substitution Principle yooooo!
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $categoryId = null): Response
    {
        ["content" => $content, "expectedHash" => $expectedHash] = $request->getParsedBody();

        $category = $this->categoryRepository->find($categoryId);
        if ($category->getHash() !== $expectedHash) {
            return $this->json(
                $response,
                [
                    "error" => "This shopping category has been altered elsewhere. Please refresh the page if you wish to make changes to this categories shopping"
                ],
                400
            );
        }

        $category->shopping = $content;
        $this->categoryRepository->save($category);

        return $this->json(
            $response,
            [
                "hash" => $category->getHash()
            ]
        );
    }

}