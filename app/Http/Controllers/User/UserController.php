<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\SmsUser;
use App\Services\Activate\UserService;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Все пользователи
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $users = SmsUser::orderBy('id', 'DESC')->Paginate(15);

        return view('user.index', compact(
            'users',
        ));
    }

    /**
     * Значение баланса (вспомогательный)
     * @return mixed
     */
    public function balance()
    {
        $result = $this->userService->balance();

        return $result;
    }
}
