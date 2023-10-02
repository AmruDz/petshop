<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['username', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();
        if ($user->role !== 'cashier') {
            auth('api')->logout();
            return response()->json(['error' => 'Unauthorized, Please change account to another role'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            // 'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function update(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'username' => 'nullable|string|min:4|unique:users,username',
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,jfif|max:2048',
        ])->validate();

        $user = auth()->user();

        if ($request->has('username')) {
            $user->username = $validatedData['username'];
        }

        if ($request->has('password')) {
            $user->password = $validatedData['password'];
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('avatars')->putFileAs('', $file, $fileName);
            $validatedData['avatar'] = $fileName;

            if ($user->avatar) {
                Storage::disk('avatars')->delete($user->avatar);
            }
        }

        $user->update([
            'username' => $validatedData['username'],
            'password' => bcrypt($validatedData['password']),
            'avatar' => $validatedData['avatar'],
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user,
        ], 200);
    }

    //web controller
    public function loginMaster()
    {
        $credentials = request(['username', 'password']);

        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('')->with('success', '');
            } else {
                return back()->with('error', 'Unauthorized access');
            }
        }

        return back()->with('error', 'Login failed. Please check your credentials.');
    }
    public function logoutMaster()
    {
        Auth::logout();

        return redirect()->route('')->with('success', 'Successfully logged out');
    }
    public function indexMaster()
    {
        $users = User::orderBy('username', 'asc')->get();

        return view('' ,compact('users'));
    }
    public function createMaster()
    {
        return view('', compact('user'));
    }
    public function registMember(Request $request)
    {
        $validatedData = Validator::make($request->all(),[
            'username' => 'required|string|min:4|unique:users,username',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,jfif|max:2048',
        ])->validate();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('avatars')->putFileAs('', $file, $fileName);
            $validatedData['avatar'] = $fileName;
        }

        $user = User::create([
            'username' => $validatedData['username'],
            'password' => bcrypt($validatedData['password']),
            'avatar' => $validatedData['avatar'],
        ]);

        return redirect()->route('')->with('success', '');
    }
    public function editMember($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return redirect()->route('')->with('error', '');
        }
        return view('', compact('user'));
    }
    public function updateMember(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(),[
            'username' => 'required|string|min:4|unique:users,username',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,jfif|max:2048',
        ])->validate();

        $user = User::findOrFail($id);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('avatars')->putFileAs('', $file, $fileName);
            $validatedData['avatar'] = $fileName;

            if ($user->avatar) {
                Storage::disk('avatars')->delete($user->avatar);
            }
        }

        $user = User::update([
            'username' => $validatedData['username'],
            'password' => bcrypt($validatedData['password']),
            'avatar' => $validatedData['avatar'],
        ]);

        return redirect()->route('')->with('success', '');
    }
    public function destroyMember($id)
    {
        $user = User::findOrFail($id);
        if (!$user) {
            return redirect()->route('')->with('error', '');
        } else {
            if ($user->avatar) {
                Storage::disk('avatars')->delete($user->avatar);
            }
            $user->delete();
            return redirect()->route('')->with('success', '');
        }
    }
}
