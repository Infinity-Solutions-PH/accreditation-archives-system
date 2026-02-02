<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Response;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], Response::HTTP_CREATED);
    }
}
