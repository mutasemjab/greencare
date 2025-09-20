<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Type;
use App\Traits\Responses;

class TypeController extends Controller
{
    use Responses;

    public function index()
    {
        $data = Type::get();
        return $this->success_response('Type retrieved successfully', $data);
    }

}