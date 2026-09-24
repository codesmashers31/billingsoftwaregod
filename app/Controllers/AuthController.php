<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Session;
use App\Core\Database;
use App\Models\User;

class AuthController extends Controller {
    public function showLogin(Request $request): void {
        if (Auth::check()) {
            Response::redirect('/dashboard');
        }
        $this->render('auth.login', [
            'pageTitle' => 'Login - ' . getSetting('company_name', 'Divya Murti ERP'),
        ], 'auth');
    }

    public function login(Request $request): void {
        $username = $request->input('username');
        $password = $request->input('password');
        $remember = (bool)$request->input('remember');

        if (empty($username) || empty($password)) {
            if ($request->isAjax()) {
                Response::error('Please enter both username and password.');
            }
            Session::flash('error', 'Please enter both username and password.');
            Response::redirect('/login');
        }

        $db = Database::getInstance();
        $sql = "SELECT * FROM `users` WHERE (username = ? OR email = ?) LIMIT 1";
        $users = $db->query($sql, [$username, $username]);

        if (empty($users)) {
            if ($request->isAjax()) {
                Response::error('Invalid credentials entered.');
            }
            Session::flash('error', 'Invalid credentials entered.');
            Response::redirect('/login');
        }

        $user = $users[0];

        if ($user['status'] !== 'active') {
            if ($request->isAjax()) {
                Response::error('Your account is inactive. Please contact system administrator.');
            }
            Session::flash('error', 'Your account is inactive. Please contact system administrator.');
            Response::redirect('/login');
        }

        if (!password_verify($password, $user['password'])) {
            if ($request->isAjax()) {
                Response::error('Invalid username or password.');
            }
            Session::flash('error', 'Invalid username or password.');
            Response::redirect('/login');
        }

        // Successfully authenticated
        Auth::login($user, $remember);

        if ($request->isAjax()) {
            Response::success('Login successful! Redirecting to dashboard...', [
                'redirect' => url('dashboard')
            ]);
        }

        Response::redirect('/dashboard');
    }

    public function logout(): void {
        Auth::logout();
        Session::flash('success', 'You have been safely logged out.');
        Response::redirect('/login');
    }

    public function showForgotPassword(Request $request): void {
        $this->render('auth.forgot-password', [
            'pageTitle' => 'Forgot Password - ' . getSetting('company_name', 'Divya Murti ERP')
        ], 'auth');
    }

    public function sendResetLink(Request $request): void {
        $email = $request->input('email');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($request->isAjax()) {
                Response::error('Please provide a valid email address.');
            }
            Session::flash('error', 'Please provide a valid email address.');
            Response::redirect('/forgot-password');
        }

        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM `users` WHERE email = ? AND status = 'active' LIMIT 1", [$email]);

        if (empty($user)) {
            if ($request->isAjax()) {
                Response::error('No active account found with this email.');
            }
            Session::flash('error', 'No active account found with this email.');
            Response::redirect('/forgot-password');
        }

        $token = bin2hex(random_bytes(32));
        $db->query("INSERT INTO `password_resets` (`email`, `token`) VALUES (?, ?)", [$email, $token]);

        $resetUrl = url("reset-password/{$token}");
        $message = "Password reset instructions have been generated. Demo link: <a href='{$resetUrl}' class='text-amber-600 font-semibold underline'>Click Here to Reset Password</a>";

        if ($request->isAjax()) {
            Response::success('Password reset link generated.', ['reset_url' => $resetUrl]);
        }

        Session::flash('success_html', $message);
        Response::redirect('/forgot-password');
    }

    public function showResetPassword(Request $request, array $params): void {
        $token = $params['token'] ?? '';
        $db = Database::getInstance();
        $reset = $db->query("SELECT * FROM `password_resets` WHERE token = ? LIMIT 1", [$token]);

        if (empty($reset)) {
            Session::flash('error', 'This password reset link is invalid or expired.');
            Response::redirect('/login');
        }

        $this->render('auth.reset-password', [
            'token' => $token,
            'email' => $reset[0]['email'],
            'pageTitle' => 'Reset Password - ' . getSetting('company_name', 'Divya Murti ERP')
        ], 'auth');
    }

    public function processResetPassword(Request $request): void {
        $token = $request->input('token');
        $password = $request->input('password');
        $confirm = $request->input('password_confirmation');

        if (empty($password) || strlen($password) < 6) {
            if ($request->isAjax()) Response::error('Password must be at least 6 characters.');
            Session::flash('error', 'Password must be at least 6 characters.');
            Response::redirect('/reset-password/' . $token);
        }

        if ($password !== $confirm) {
            if ($request->isAjax()) Response::error('Password confirmation does not match.');
            Session::flash('error', 'Password confirmation does not match.');
            Response::redirect('/reset-password/' . $token);
        }

        $db = Database::getInstance();
        $reset = $db->query("SELECT * FROM `password_resets` WHERE token = ? LIMIT 1", [$token]);

        if (empty($reset)) {
            if ($request->isAjax()) Response::error('Invalid token.');
            Session::flash('error', 'Invalid or expired token.');
            Response::redirect('/login');
        }

        $email = $reset[0]['email'];
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $db->query("UPDATE `users` SET password = ? WHERE email = ?", [$hash, $email]);
        $db->query("DELETE FROM `password_resets` WHERE email = ?", [$email]);

        if ($request->isAjax()) {
            Response::success('Password has been successfully updated! You may now login.');
        }

        Session::flash('success', 'Password reset successfully! Please login with your new password.');
        Response::redirect('/login');
    }
}
