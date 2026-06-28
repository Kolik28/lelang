<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAuctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'image_url' => 'nullable|url',
            'starting_price' => 'required|numeric|min:0.01',
            'bid_increment' => 'required|numeric|min:0.01',
            'starts_at' => 'required|date_format:Y-m-d\TH:i|after_or_equal:now',
            'ends_at' => 'required|date_format:Y-m-d\TH:i|after:starts_at',
        ];
    }

    public function messages(): array
    {
        return [
            'starts_at.after' => 'Start time harus di masa depan',
            'ends_at.after' => 'End time harus setelah start time',
            'starts_at.date_format' => 'Format tanggal mulai tidak valid',
            'ends_at.date_format' => 'Format tanggal berakhir tidak valid',
        ];
    }
}