<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Streams a book's PDF from the private disk.
 *
 * Books are stored on the private disk so they can never be downloaded
 * by direct URL. Access rules:
 *   - admin roles: any book (preview drafts before publishing)
 *   - everyone else (readers): published books only
 *
 * BinaryFileResponse supports HTTP Range requests, so PDF.js in the
 * future Reading Room can fetch the file chunk-by-chunk instead of
 * downloading the whole book up front.
 */
class BookContentController extends Controller
{
    public function __invoke(Request $request, Book $book): BinaryFileResponse
    {
        abort_unless($book->hasContent(), 404);

        $isStaff = in_array($request->user()->role, UserRole::adminRoles(), true);

        abort_unless($isStaff || $book->status === Book::STATUS_PUBLISHED, 403);

        $path = Storage::disk('local')->path($book->content_file);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $book->slug . '.pdf"',
        ]);
    }
}
