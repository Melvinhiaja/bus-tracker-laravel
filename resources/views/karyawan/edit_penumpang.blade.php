@extends('layouts.app')

@section('title', 'Edit Penumpang')

@section('content')
<div class="container">
    <h2>Edit Penumpang</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('karyawan.updatePenumpang', $penumpang->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>NIK</label>
            <input type="text" name="nik" class="form-control" value="{{ $penumpang->nik }}" required>
        </div>

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $penumpang->nama }}" required>
        </div>

        <div class="mb-3">
            <label>Tempat & Tanggal Lahir</label>
            <input type="text" name="tempat_tgl_lahir" class="form-control" value="{{ $penumpang->tempat_tgl_lahir }}" required>
        </div>

        <div class="mb-3">
            <label>Jenis Kelamin</label>
            <input type="text" name="jenis_kelamin" class="form-control" value="{{ $penumpang->jenis_kelamin }}" required>
        </div>

        <div class="mb-3">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" value="{{ $penumpang->alamat }}" required>
        </div>

        <div class="mb-3">
            <label>RT/RW</label>
            <input type="text" name="rt_rw" class="form-control" value="{{ $penumpang->rt_rw }}" required>
        </div>

        <div class="mb-3">
            <label>Kelurahan/Desa</label>
            <input type="text" name="kelurahan_desa" class="form-control" value="{{ $penumpang->kelurahan_desa }}" required>
        </div>

        <div class="mb-3">
            <label>Kecamatan</label>
            <input type="text" name="kecamatan" class="form-control" value="{{ $penumpang->kecamatan }}" required>
        </div>

        <div class="mb-3">
            <label>Agama</label>
            <input type="text" name="agama" class="form-control" value="{{ $penumpang->agama }}" required>
        </div>

        <div class="mb-3">
            <label>Status Perkawinan</label>
            <input type="text" name="status_perkawinan" class="form-control" value="{{ $penumpang->status_perkawinan }}" required>
        </div>

        <div class="mb-3">
            <label>Pekerjaan</label>
            <input type="text" name="pekerjaan" class="form-control" value="{{ $penumpang->pekerjaan }}" required>
        </div>

        <div class="mb-3">
            <label>Kewarganegaraan</label>
            <input type="text" name="kewarganegaraan" class="form-control" value="{{ $penumpang->kewarganegaraan }}" required>
        </div>

        <div class="mb-3">
    <label>Berlaku Hingga</label>
    <input type="text" name="berlaku_hingga" class="form-control" value="{{ $penumpang->berlaku_hingga }}" required>
</div>


        <div class="mb-3">
            <label>Foto KTP</label>
            <input type="file" name="foto_ktp" class="form-control">
            @if($penumpang->foto_ktp)
                <p>Foto KTP Saat Ini:</p>
                <img src="{{ asset('storage/' . $penumpang->foto_ktp) }}" width="100">
            @endif
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection
