@extends('layouts.app')

@section('title', 'Test - Toolsme')
@section('header-title', 'Halaman Test')

@section('content')
    
    <h1>Halo, {{ $nama }}</h1>
    <p>Ini isi konten tengahnya.</p>
    <div class="style1">
        <h1>test memories</h1>
    </div>
    <div class="simpleCrud">
        <br>
        <h1>Alat Crud simple</h1>
        <table>
            <thead>
                <tr>
                    <th>no</th>
                    <th>nama alat</th>
                    <th>stok</th>
                    <th>kategori</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alats as $alat)
                <tr>
                    <td> {{ $loop->iteration }} </td>
                    <td> {{ $alat->nama_alat }} </td>
                    <td> {{ $alat->stok }} </td>
                    <td> {{ $alat->kategori->nama_kategori ?? "-" }} </td>
                </tr>
                 @empty
                <tr>
                    <td colspan="4">Belum ada data alat.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

        <br><br>
        <div class="CrudUser">
        <h1>CRUD USER</h1>
            <table>
                <thead>
                    <tr>
                        <th>no</th>
                        <th>nama</th>
                        <th>email</th>
                        <th>role</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $loop->iteration}}</td>
                        <td>{{ $user->name}}</td>
                        <td>{{ $user->email}}</td>
                        <td>{{ $user->role}}</td>
                    </tr>
                     @empty
                        <tr>
                            <td colspan="4">Belum ada data user.</td>
                        </tr>
                    @endforelse                
                </tbody>
            </table>
        </div>


    
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/test.css') }}">
@endpush
