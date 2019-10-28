<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory
{
    /**
     * @var bool
     */
    private $status;

    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $itens;

    private $content;
    /**
     * @var bool
     */
    private $success;

    /**
     * ResponseFactory constructor.
     * @param bool $success
     * @param int $status
     * @param $content
     * @param int|null $page
     * @param int|null $itens
     */
    public function __construct(
        bool $success,
        $content,
        int $status = 200,
        int $page = null,
        int $itens = null
    ) {
        $this->status = $status;
        $this->content = $content;
        $this->page = $page;
        $this->itens = $itens;
        $this->success = $success;
    }

    /**
     * @return JsonResponse
     */
    public function getResponse(): JsonResponse
    {
        $response = [
            'success' => $this->success,
            'page' => $this->page,
            'itens' => $this->itens,
            'content' => $this->content
        ];

        if (is_null($this->page)) {
            unset($response['page']);
            unset($response['itens']);
        }

        return new JsonResponse($response, $this->status);
    }
}