<?php

namespace Innoboxrr\LaravelOptions\Http\Controllers;

use Innoboxrr\LaravelOptions\Http\Requests\Option\PoliciesRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\PolicyRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\IndexRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\ShowRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\CreateRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\UpdateRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\DeleteRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\RestoreRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\ForceDeleteRequest;
use Innoboxrr\LaravelOptions\Http\Requests\Option\ExportRequest;

class OptionController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function policies(PoliciesRequest $request)
    {
        return $request->handle($this);   
    }

    public function policy(PolicyRequest $request)
    {
        return $request->handle();
    }

    public function index(IndexRequest $request)
    {
        return $request->handle();   
    }

    public function show(ShowRequest $request)
    {
        return $request->handle();   
    }

    public function create(CreateRequest $request)
    {
        return $request->handle();   
    }

    public function update(UpdateRequest $request)
    {
        return $request->handle();   
    }

    public function delete(DeleteRequest $request)
    {
        return $request->handle();   
    }

    public function restore(RestoreRequest $request)
    {
        return $request->handle();   
    }

    public function forceDelete(ForceDeleteRequest $request)
    {
        return $request->handle();   
    }

    public function export(ExportRequest $request)
    {
        return $request->handle();   
    }

}
