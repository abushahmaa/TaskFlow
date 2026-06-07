<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserWebController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('admin'), 403);
        
        $users = User::with('roles')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }
}
