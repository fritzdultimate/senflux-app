<?php

namespace App\Http\Controllers;

use App\Models\KycSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KycDocumentController extends Controller
{
    /** Fields allowed to be served — never trust the route parameter blindly. */
    private const ALLOWED_FIELDS = [
        'id_front_path',
        'id_back_path',
        'selfie_path',
        'proof_of_address_path',
    ];

    public function show(Request $request, KycSubmission $submission, string $field)
    {
        $user = Auth::user();

        abort_unless(
            $submission->user_id === $user->id || $user->is_admin,
            403,
        );

        abort_unless(in_array($field, self::ALLOWED_FIELDS, true), 404);

        $path = $submission->{$field};
        abort_if(empty($path), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path);
    }
}
