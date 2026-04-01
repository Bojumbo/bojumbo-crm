<?php
namespace App\Http\Controllers;
use App\Models\Setting;
use Illuminate\Http\Request;
class SettingsController extends Controller
{
    public function index()
    {
        $currencies = [
            '₪' => 'ILS (₪)',
            '$' => 'USD ($)',
            '€' => 'EUR (€)',
            '₴' => 'UAH (₴)',
            'zł' => 'PLN (zł)',
        ];
        $currentCurrency = Setting::get('crm_currency', '₪');
        return view('admin.settings.index', compact('currencies', 'currentCurrency'));
    }
    public function update(Request $request)
    {
        if ($request->has('crm_currency')) {
            Setting::set('crm_currency', $request->input('crm_currency'));
        }
        
        if ($request->has('google_drive_root_folder_id')) {
            Setting::set('google_drive_root_folder_id', $request->input('google_drive_root_folder_id'));
        }
        return redirect()->back()->with('success', 'Settings updated!');
    }
}