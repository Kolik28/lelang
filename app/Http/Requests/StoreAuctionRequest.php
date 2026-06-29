<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

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

            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_url' => 'nullable|url|max:500',

            'starting_price' => 'required|numeric|min:0.01',
            'bid_increment' => 'required|numeric|min:0.01',
            'starts_at' => 'required|date_format:Y-m-d\TH:i|after:now',
            'ends_at' => 'required|date_format:Y-m-d\TH:i|after:starts_at',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul lelang wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'description.min' => 'Deskripsi minimal 10 karakter',

            'image.image' => 'File yang diupload harus berupa gambar',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp',
            'image.max' => 'Ukuran gambar maksimal 2MB',

            'image_url.url' => 'URL gambar tidak valid',
            'image_url.max' => 'URL gambar terlalu panjang',

            'starting_price.required' => 'Harga awal wajib diisi',
            'starting_price.min' => 'Harga awal minimal Rp 0,01',
            'bid_increment.required' => 'Kenaikan tawaran wajib diisi',
            'bid_increment.min' => 'Kenaikan tawaran minimal Rp 0,01',

            'starts_at.required' => 'Waktu mulai wajib diisi',
            'starts_at.date_format' => 'Format waktu mulai tidak valid (gunakan format YYYY-MM-DDTHH:MM)',
            'starts_at.after' => 'Waktu mulai harus di masa depan',

            'ends_at.required' => 'Waktu berakhir wajib diisi',
            'ends_at.date_format' => 'Format waktu berakhir tidak valid',
            'ends_at.after' => 'Waktu berakhir harus setelah waktu mulai',
        ];
    }
}
