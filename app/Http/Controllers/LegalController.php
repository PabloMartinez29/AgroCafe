<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    public function terms(): View
    {
        return $this->document('terms');
    }

    public function privacy(): View
    {
        return $this->document('privacy');
    }

    public function cookies(): View
    {
        return $this->document('cookies');
    }

    public function refund(): View
    {
        return $this->document('refund');
    }

    private function document(string $key): View
    {
        $document = config("legal.documents.{$key}");

        return view($document['view'], [
            'document' => $document,
            'controller' => config('legal.controller'),
        ]);
    }
}
