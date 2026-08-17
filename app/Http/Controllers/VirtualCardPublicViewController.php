<?php

namespace App\Http\Controllers;

use App\Models\VirtualIdCard;
use Illuminate\Http\Request;

class VirtualCardPublicViewController extends Controller
{
    public function show(string $uuid)
    {
        $card = VirtualIdCard::where('uuid', $uuid)
            ->orWhere('qr_token', $uuid)
            ->firstOrFail();

        return view('virtual-cards.public-view', compact('card'));
    }
}
