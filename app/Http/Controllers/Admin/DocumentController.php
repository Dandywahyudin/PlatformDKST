<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Download the specified document file.
     */
    public function download(Document $document): BinaryFileResponse|StreamedResponse
    {
        Gate::authorize('download', $document);

        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Berkas dokumen tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Preview/view the document in browser (for PDF files).
     */
    public function preview(Document $document): BinaryFileResponse|StreamedResponse
    {
        Gate::authorize('view', $document);

        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Berkas dokumen tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->response($document->file_path, $document->file_name);
    }
}
