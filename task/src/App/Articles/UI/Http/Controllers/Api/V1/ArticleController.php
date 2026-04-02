<?php

declare(strict_types=1);

namespace App\Articles\UI\Http\Controllers\Api\V1;

use App\Shared\UI\Http\Controllers\Api\ApiController;

final class ArticleController extends ApiController
{
    public function __construct(
        private readonly CreateArticleHandler $createArticleHandler,
        private readonly UpdateArticleHandler $updateArticleHandler,
        private readonly DeleteArticleHandler $deleteArticleHandler,
        private readonly GetArticleHandler $getArticleHandler,
        private readonly ListArticlesHandler $listArticlesHandler,
    ) {
    }

    #[OA\Get(
        path: '/v1/articles',
        summary: 'List articles',
        tags: ['Articles'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Items per page', schema: new OA\Schema(type: 'integer', default: 20)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of articles'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        return $this->success($this->listArticlesHandler->handle($request));
    }

    #[OA\Get(
        path: '/v1/articles/{id}',
        summary: 'Get article by ID',
        tags: ['Articles'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Article ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [    
            new OA\Response(response: 200, description: 'Article details'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        return $this->success($this->getArticleHandler->handle($id));
    }

    #[OA\Post(
        path: '/v1/articles',
        summary: 'Create article',
        tags: ['Articles'],
        security: [['jwt' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'body', 'category', 'author'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'My Article'),
                    new OA\Property(property: 'body', type: 'string', example: 'This is my article body'),
                    new OA\Property(property: 'category', type: 'string', example: 'News'),
                    new OA\Property(property: 'author', type: 'string', example: 'John Doe'),
                ]
            )
        ),    
        responses: [
            new OA\Response(response: 201, description: 'Article created'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        return $this->created($this->createArticleHandler->handle($request));
    }

    #[OA\Put(
        path: '/v1/articles/{id}',
        summary: 'Update article',
        tags: ['Articles'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Article ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'body', 'category', 'author'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'My Article'),
                    new OA\Property(property: 'body', type: 'string', example: 'This is my article body'),
                    new OA\Property(property: 'category', type: 'string', example: 'News'),
                    new OA\Property(property: 'author', type: 'string', example: 'John Doe'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Article updated'),
        ]
    )]
    public function update(string $id, Request $request): JsonResponse
    {
        return $this->updated($this->updateArticleHandler->handle($id, $request));
    }

    #[OA\Delete(
        path: '/v1/articles/{id}',
        summary: 'Delete article',
        tags: ['Articles'],
        security: [['jwt' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'Article ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Article deleted'),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        return $this->deleted($this->deleteArticleHandler->handle($id));
    }
}