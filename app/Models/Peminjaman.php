<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'tgl_pinjam',
        'tgl_kembali_plan',
        'status',
        'alasan_penolakan',   // ← tambah
        'ditolak_oleh',       // ← tambah
        'ditolak_pada',       // ← tambah
    ];

    protected function casts(): array
    {
        return [
            'tgl_pinjam'       => 'date',
            'tgl_kembali_plan' => 'date',
            'ditolak_pada'     => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detailPinjam(): HasMany
    {
        return $this->hasMany(DetailPinjam::class);
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }

    // ← TAMBAH INI
    public function ditolakOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditolak_oleh');
    }

    // ← TAMBAH SCOPE (opsional, biar rapi)
    public function scopeFilter($query, ?string $filter)
    {
        return match ($filter) {
            'diajukan'            => $query->where('status', 'diajukan'),
            'dipinjam'            => $query->where('status', 'dipinjam'),
            'menunggu_verifikasi' => $query->where('status', 'menunggu_verifikasi'),
            'dikembalikan'        => $query->where('status', 'dikembalikan'),
            'ditolak'             => $query->where('status', 'ditolak'),
            'telat'               => $query->where(function ($q) {
                $q->where('status', 'telat')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'dipinjam')
                         ->whereDate('tgl_kembali_plan', '<', now()->toDateString());
                  });
            }),
            default               => $query,
        };
    }

    public function scopeSort($query, ?string $sort)
    {
        return match ($sort) {
            'terlama' => $query->orderBy('created_at', 'asc'),
            'terbaru' => $query->orderBy('created_at', 'desc'),
            default   => $query->orderBy('created_at', 'desc'),
        };
    }
}