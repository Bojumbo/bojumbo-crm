<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class GoogleOAuthController extends Controller
{
    /**
     * Перенаправлення на Google
     */
    public function redirectToGoogle()
    {
        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/documents https://www.googleapis.com/auth/drive',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);
        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$query}");
    }
    /**
     * Обробка відповіді Google
     */
    public function handleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('admin.settings.index')->with('error', 'Доступ скасовано');
        }
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'code' => $request->code,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect_uri'),
            'grant_type' => 'authorization_code',
        ]);
        $data = $response->json();
        if (isset($data['access_token'])) {
            auth()->user()->update([
                'google_access_token' => $data['access_token'],
                'google_refresh_token' => $data['refresh_token'] ?? auth()->user()->google_refresh_token,
                'google_token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);
            return redirect()->route('admin.settings.index')->with('success', 'Google Drive успішно підключено!');
        }
        return redirect()->route('admin.settings.index')->with('error', 'Помилка авторизації');
    }
}