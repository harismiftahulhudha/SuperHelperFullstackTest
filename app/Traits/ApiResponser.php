<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['msg' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200, $defaultPage = 20)
    {
        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection], $code);
        }

        $collection = $this->sortData($collection);
        $collection = $this->paginate($collection, $defaultPage);

        return $this->successResponse($collection, $code);
    }

    protected function showData($data, $code = 200)
    {
        $d = [
            'data' => $data
        ];
        return $this->successResponse($d, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['msg' => $message, 'code' => $code], $code);
    }

    protected function sortData(Collection $collection)
    {
        if (request()->has('sort_by')) {
            $attribute = request()->sort_by;
            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    protected function paginate(Collection $collection, $defaultPage = 20)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50'
        ];

        Validator::validate(request()->all(), $rules);

        $page = LengthAwarePaginator::resolveCurrentPage();

        $perPage = $defaultPage;

        if (request()->has('per_page')) {
            $perPage = (int)request()->per_page;
        }

        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    protected function checkLoginType($data)
    {
        $loginType = preg_replace('/[^0-9]/', '', $data);
        if ($loginType == '') {
            $loginType = -1;
        } else if ($loginType == '-1') {
            $loginType = -1;
        } else {
            $loginType = intval($loginType);
            if ($loginType > 1 || $loginType < 0) {
                $loginType = -1;
            }
        }
        if ($loginType == -1) {
            return -1;
        }
        return 1;
    }
}
