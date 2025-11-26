<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ==================== ĐĂNG KÝ ====================
    public function showRegisterForm()
    {
        return view('auth.dangky');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'sdt'      => ['required', 'regex:/^0[0-9]{9,10}$/'],
            'email'    => ['required', 'email', 'unique:nguoi_dung,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required'  => 'Họ và tên không được để trống.',
            'sdt.required'   => 'Số điện thoại không được để trống.',
            'sdt.regex'      => 'Số điện thoại phải gồm 10-11 số và bắt đầu bằng số 0.',
            'email.required' => 'Email không được để trống.',
            'email.unique'   => 'Email này đã được sử dụng.',
            'password.required'  => 'Mật khẩu không được để trống.',
            'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $user = User::create([
            'ho_ten'   => $request->name,
            'email'    => $request->email,
            'sdt'      => $request->sdt,
            'mat_khau' => Hash::make($request->password),
            'vai_tro'  => 'KHACH_HANG',
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký thành công');
    }

    // ==================== ĐĂNG NHẬP ====================
    public function showLoginForm()
    {
        return view('auth.dangnhap');
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::attempt(['email' => $request->email, 'mat_khau' => $request->password])) {
    //         $request->session()->regenerate();

    //         $user = Auth::user();

    //         if ($user->bi_chan) {
    //             Auth::logout();
    //             return back()->withErrors(['email' => 'Tài khoản đã bị khóa.']);
    //         }

    //         if ($user->vai_tro === 'ADMIN') {
    //             return redirect()->route('admin.dashboard');
    //         } elseif ($user->vai_tro === 'NHAN_VIEN') {
    //             return redirect()->route('staff.dashboard');
    //         } else {
    //             return redirect()->route('home');
    //         }
    //     }

    //     return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->onlyInput('email');
    // }
    public function login(Request $request)
    {
        // Validate
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // BƯỚC 1: Tìm user
        $user = User::where('email', $request->email)->first();

        // Debug 1: User có tồn tại không?
        if (!$user) {
            return back()->withErrors(['email' => '❌ Email không tồn tại trong hệ thống.'])->onlyInput('email');
        }

        // Debug 2: Kiểm tra mật khẩu
        if (!Hash::check($request->password, $user->mat_khau)) {
            return back()->withErrors(['email' => '❌ Mật khẩu không đúng. Bạn vừa nhập: ' . $request->password])->onlyInput('email');
        }

        // Debug 3: User bị chặn không?
        if ($user->bi_chan) {
            return back()->withErrors(['email' => '❌ Tài khoản đã bị khóa.']);
        }

        // BƯỚC 2: Đăng nhập thủ công
        Auth::login($user);
        $request->session()->regenerate();

        // Debug 4: Kiểm tra đã đăng nhập chưa?
        if (!Auth::check()) {
            return back()->withErrors(['email' => '❌ Không thể đăng nhập vào hệ thống. Vui lòng kiểm tra lại cấu hình.']);
        }

        // BƯỚC 3: Chuyển hướng theo vai trò
        Log::info('User logged in: ' . $user->email . ' - Role: ' . $user->vai_tro);

        if ($user->vai_tro === 'ADMIN') {
            return redirect()->route('admin.dashboard')->with('success', '✅ Đăng nhập thành công với quyền ADMIN!');
        } elseif ($user->vai_tro === 'NHAN_VIEN') {
            return redirect()->route('staff.dashboard')->with('success', '✅ Đăng nhập thành công với quyền NHÂN VIÊN!');
        } else {
            return redirect()->route('home')->with('success', '✅ Đăng nhập thành công!');
        }
    }

    // ==================== ĐĂNG XUẤT ====================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ==================== QUÊN MẬT KHẨU ====================
    public function showForgotPasswordForm()
    {
        return view('auth.quenmatkhau');
    }

    public function updatePasswordSimple(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.']);
        }

        $user->mat_khau = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Đổi mật khẩu thành công!');
    }

    // ==================== GOOGLE/FACEBOOK LOGIN ====================
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    // public function handleProviderCallback($provider)
    // {
    //     try {
    //         $socialUser = Socialite::driver($provider)->user();

    //         $user = User::where('email', $socialUser->email)->first();

    //         if ($user) {
    //             // Cập nhật ID nếu chưa có
    //             if ($provider === 'google' && !$user->google_id) {
    //                 $user->google_id = $socialUser->id;
    //                 $user->save();
    //             }
    //             if ($provider === 'facebook' && !$user->facebook_id) {
    //                 $user->facebook_id = $socialUser->id;
    //                 $user->save();
    //             }
    //         } else {
    //             // Tạo user mới
    //             $user = User::create([
    //                 'ho_ten'     => $socialUser->name,
    //                 'email'      => $socialUser->email ?? $socialUser->id . "@{$provider}.com",
    //                 'mat_khau'   => Hash::make(Str::random(16)),
    //                 'vai_tro'    => 'KHACH_HANG',
    //                 'bi_chan'    => 0,
    //                 $provider . '_id' => $socialUser->id,
    //             ]);
    //         }

    //         Auth::login($user, true);

    //         if ($user->vai_tro === 'ADMIN') {
    //             return redirect()->route('admin.dashboard');
    //         } elseif ($user->vai_tro === 'NHAN_VIEN') {
    //             return redirect()->route('staff.dashboard');
    //         } else {
    //             return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    //         }
    //     } catch (\Exception $e) {
    //         // Log lỗi
    //         Log::error('Social Login Error: ' . $e->getMessage());

    //         return redirect()->route('login')
    //             ->with('error', 'Đăng nhập ' . ucfirst($provider) . ' thất bại! Vui lòng thử lại.');
    //     }
    // }

    public function handleProviderCallback($provider)
    {
        try {
            // BƯỚC 1: Lấy thông tin từ Google
            $socialUser = Socialite::driver($provider)->user();

            // DEBUG: Xem thông tin user từ Google
            Log::info('Google User Info:', [
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'id' => $socialUser->id,
            ]);

            // BƯỚC 2: Tìm user trong DB
            $user = User::where('email', $socialUser->email)->first();

            if ($user) {
                Log::info('User found in DB:', ['email' => $user->email, 'id' => $user->id]);

                // Cập nhật google_id nếu chưa có
                if ($provider === 'google' && !$user->google_id) {
                    $user->google_id = $socialUser->id;
                    $user->save();
                    Log::info('Updated google_id for user');
                }
                if ($provider === 'facebook' && !$user->facebook_id) {
                    $user->facebook_id = $socialUser->id;
                    $user->save();
                }
            } else {
                Log::info('User not found, creating new user');

                // Tạo user mới
                $user = User::create([
                    'ho_ten'     => $socialUser->name,
                    'email'      => $socialUser->email ?? $socialUser->id . "@{$provider}.com",
                    'mat_khau'   => Hash::make(Str::random(16)),
                    'vai_tro'    => 'KHACH_HANG',
                    'bi_chan'    => 0,
                    $provider . '_id' => $socialUser->id,
                ]);

                Log::info('New user created:', ['email' => $user->email, 'id' => $user->id]);
            }

            // BƯỚC 3: Đăng nhập
            Auth::login($user, true);

            // Kiểm tra đã đăng nhập chưa
            if (!Auth::check()) {
                Log::error('Auth::login failed!');
                return redirect()->route('login')->with('error', 'Không thể đăng nhập. Vui lòng thử lại.');
            }

            Log::info('User logged in successfully:', ['email' => $user->email]);

            // BƯỚC 4: Chuyển hướng
            if ($user->vai_tro === 'ADMIN') {
                return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập Google thành công!');
            } elseif ($user->vai_tro === 'NHAN_VIEN') {
                return redirect()->route('staff.dashboard')->with('success', 'Đăng nhập Google thành công!');
            } else {
                return redirect()->route('home')->with('success', 'Đăng nhập Google thành công!');
            }
        } catch (\Exception $e) {
            // Log chi tiết lỗi
            Log::error('Google Login Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->with('error', 'Lỗi đăng nhập Google: ' . $e->getMessage());
        }
    }

}
