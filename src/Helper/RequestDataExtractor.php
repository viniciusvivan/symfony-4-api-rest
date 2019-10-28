<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Request;

Trait RequestDataExtractor
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getSort(Request $request)
    {
        $sort = $request->query->get('sort');
        return is_null($sort) ? [] : $sort;
    }

    /***
     * @param Request $request
     * @return mixed
     */
    public function getFilter(Request $request)
    {
        $filter = $request->query->get('filter');
        return is_null($filter) ? [] : $filter;
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function getPage(Request $request)
    {
        $page = $request->query->get('page');
        return is_null($page) ? 1 : $page;
    }

    /**
     * @param Request $request
     * @return int|mixed
     */
    public function getItens(Request $request)
    {
        $itens = $request->query->get('itens');
        return is_null($itens) ? 10 : $itens;
    }
}