<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::query()->filter($request->get('f', []))->get();

        $grid = new Datagrid($users, $request->get('f', []));

        $grid->setColumn('name', 'Full name', ['sortable' => true, 'has_filters' => true])
            ->setColumn('email', 'Email address', ['sortable' => true, 'has_filters' => true])
            ->setColumn('role', 'Role', [
                'sortable' => true,
                'has_filters' => true,
                'filters' => ['Admin' => 'Administrator', 'User' => 'Regular user'],
                'wrapper' => function ($value, $row) {
                    return match ($value) {
                        'Admin' => 'Administrator',
                        'User' => 'Regular user'
                    };
                }
            ])
            ->setColumn('created_at', 'Created at', ['sortable' => true, 'has_filters' => true])
            ->setActionColumn([
                'wrapper' => function ($value, $row) {
                    return (Auth::user()->can('update', $row->getData()) ? '<a href="' . route('user.edit', [$row->id]) . '" title="Edit" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i></a> ' : '') .
                        (Auth::user()->can('delete', $row->getData()) ? '<a href="' . route('user.delete', $row->id) . '" title="Delete" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>' : '');
                }
            ]);

        return view('user.index', [
            'grid' => $grid
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create', [
            'action' => route('user.store'),
            'method' => 'post'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
//        $request->validate([
//            'name' => 'required',
//            'email' => 'required|email|unique:users,email',
//            'password' => 'required|min:6|confirmed'
//        ]);

        $user = User::create($request->all());
        $user->save();
        return redirect()->route('user.index')->with('alert', 'User has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->password = '';
        return view('user.edit', [
            'action' => route('user.update', $user->id),
            'method' => 'put',
            'model' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
//        $request->validate([
//            'name' => 'required',
//            'email' => 'required|email',
//            'password' => 'required|min:6|confirmed'
//        ]);
        $user->update($request->all());
        return redirect()->route('user.index')->with('alert', 'User has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('alert', 'User has been deleted successfully!');
    }
}
