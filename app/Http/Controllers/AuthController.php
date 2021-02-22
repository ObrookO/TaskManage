<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }

    public function doRegister(Request $request)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:16',
            're-password' => 'required|same:password'
        ];
        $message = [
            'name.required' => '请输入用户名',
            'name.unique' => '用户名已被使用',
            'email.required' => '请输入邮箱',
            'email.email' => '邮箱格式错误',
            'email.unique' => '邮箱已被使用',
            'password.required' => '请输入密码',
            'password.min' => '密码的长度为6-16位',
            'password.max' => '密码的长度为6-16位',
            're-password.required' => '请再次输入密码',
            're-password.same' => '两次输入的密码不一致'
        ];

        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors()->first());
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => md5($data['password'])
        ]);

        return redirect()->to(route('login'));
    }

    public function login()
    {
        return view('auth.login');
    }

    public function doLogin(Request $request)
    {
        $data = $request->all();
        $rules = [
            'name' => 'required',
            'password' => 'required',
        ];
        $message = [
            'name.required' => '请输入用户名',
            'password.required' => '请输入密码',
        ];

        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors()->first());
        }

        $user = User::where('name', $data['name'])->where('password', md5($data['password']))->first();
        if (empty($user)) {
            return back()->withInput()->withErrors('用户名或密码错误');
        }

        Session::put('user', ['id' => $user->id, 'name' => $user->name]);
        return redirect()->to(route('home'));
    }

    public function logout()
    {
        Session::forget('user');
        return redirect()->to(route('login'));
    }
}
