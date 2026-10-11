<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomQuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomQuote::query()->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%");
            });
        }

        $quotes = $query->paginate(20)->withQueryString();

        $statusCounts = [
            'all' => CustomQuote::count(),
            'new' => CustomQuote::where('status', 'new')->count(),
            'contacted' => CustomQuote::where('status', 'contacted')->count(),
            'approved' => CustomQuote::where('status', 'approved')->count(),
            'cancelled' => CustomQuote::where('status', 'cancelled')->count(),
        ];

        return view('admin.custom-quotes.index', [
            'quotes' => $quotes,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
            'currentSearch' => $search,
        ]);
    }

    public function download(CustomQuote $quote): StreamedResponse
    {
        abort_unless($quote->file_path && Storage::disk('public')->exists($quote->file_path), 404);

        $originalFilename = basename(str_replace('\\', '/', $quote->original_filename));
        $extension = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $extension = in_array($extension, ['stl', '3mf', 'zip'], true) ? $extension : 'bin';
        $baseName = Str::ascii(pathinfo($originalFilename, PATHINFO_FILENAME));
        $baseName = preg_replace('/[^A-Za-z0-9_-]+/', '-', $baseName) ?: '3d-model';
        $downloadFilename = trim($baseName, '-_').'.'.$extension;

        $contentType = match ($extension) {
            '3mf' => 'model/3mf',
            'stl' => 'model/stl',
            'zip' => 'application/zip',
            default => 'application/octet-stream',
        };

        return Storage::disk('public')->download($quote->file_path, $downloadFilename, [
            'Content-Type' => $contentType,
        ]);
    }

    public function update(Request $request, CustomQuote $quote)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'contacted', 'approved', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $quote->update($data);

        return back()->with('success', 'تم تحديث حالة طلب التسعير بنجاح.');
    }

    public function destroy(CustomQuote $quote)
    {
        if ($quote->file_path && Storage::disk('public')->exists($quote->file_path)) {
            Storage::disk('public')->delete($quote->file_path);
        }

        $quote->delete();

        return back()->with('success', 'تم حذف طلب التسعير.');
    }
}
