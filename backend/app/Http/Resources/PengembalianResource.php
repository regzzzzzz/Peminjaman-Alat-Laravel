<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengembalianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'peminjaman_id' => $this->peminjaman_id,
            'tgl_kembali' => $this->tgl_kembali?->format('Y-m-d'),
            'kondisi_kembali' => $this->kondisi_kembali,
            'denda' => (int) $this->denda,
            'petugas_id' => $this->petugas_id,
            'petugas' => $this->whenLoaded('petugas', fn() => [
                'id' => $this->petugas?->id,
                'name' => $this->petugas?->name,
            ]),
            'peminjaman' => $this->whenLoaded('peminjaman', fn() => [
                'id' => $this->peminjaman?->id,
                'status' => $this->peminjaman?->status,
                'peminjam' => $this->peminjaman?->user?->name,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
