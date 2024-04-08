<?php

namespace App\Http\Controllers;

use Aginev\Datagrid\Datagrid;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return view('user.index', [
            'model' => $user
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

    }

    public function create()
    {
        return view('user.create', [
            'action' => route('user.store'),
            'method' => 'post'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required',
        ]);

        $user = User::create($request->all());
        $user->save();
        return redirect()->route('user.users_admin')->with('alert', 'User has been created successfully!');
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
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $oldImage = null;
        $avatarName = $user->profile_photo;



        if ($request->hasFile('profile_photo') && $request->file('profile_photo')->isValid()) {
            $oldImage = $user->profile_photo;
            $avatarName = time() . '.' . $request->profile_photo->getClientOriginalExtension();
            $request->profile_photo->move(public_path('images'), $avatarName);
        }
        $user->update($request->all());
        $user->update(['profile_photo' => $avatarName]);

        if ($oldImage) {
            $oldImagePath = public_path('images') . '/' . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        if (auth()->user()->can('view', User::class) && auth()->user()->id != $user->id) {
            return redirect()->route('user.users_admin')->with('alert', 'Profile has been updated successfully!');
        }
        return redirect()->route('user.index')->with('alert', 'User has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $recipes = Recipe::where('user_id', $user->id)->get();

        foreach ($recipes as $recipe) {
            $recipe->delete();
        }
        $oldImage = $user->profile_photo;
        if ($oldImage) {
            $oldImagePath = public_path('images') . '/' . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        if (Auth::user()->id != $user->id) {
            $user->delete();
            return redirect()->route('user.users_admin')->with('alert', 'Profile with all recipes has been deleted successfully!');
        }

        $user->delete();
        return redirect()->route('user.index')->with('alert', 'Profile with all recipes has been deleted successfully!');


    }


    public function users_admin(Request $request)
    {
        if (!auth()->user()->can('view', User::class)) {
            abort(403, 'Unauthorized');
        }
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
                        (Auth::user()->can('delete', $row->getData()) ? '<a href="' . route('user.delete', $row->id) . '" title="Delete" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure ?\')"><i class="bi bi-trash"></i></a>' : '');
                }
            ]);

        return view('user.users_admin', [
            'grid' => $grid
        ]);
    }
}
