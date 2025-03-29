@extends('layouts.app')

@section('content')

    <body>

        <div class="container mt-4">
            <h2 class="text-center">List Peserta Diterima</h2>


            <!-- Tabel Data Peserta -->
            <table id="pesertaTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tempat Lahir</th>
                        <th>Tanggal Lahir</th>
                        <th>Umur</th>
                        <th>Alamat</th>
                        <th>Tgl Asuransi</th>
                        <th>Durasi Asuransi</th>
                        <th>Status Peserta</th>
                        <th>Status Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Modal Upload -->
        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadModalLabel">Upload Dokumen</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="uploadForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="peserta_id" id="peserta_id">
                            <div class="form-group">
                                <label for="dokumen">Pilih Dokumen</label>
                                <input type="file" name="dokumen" id="dokumen" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Modal Tambah/Edit Peserta -->
        <div class="modal fade" id="modalPeserta" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Tambah Peserta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formPeserta">
                        <div class="modal-body">
                            <input type="hidden" id="peserta_id">
                            <div class="mb-3">
                                <label>Nama</label>
                                <input type="text" class="form-control" id="nama" required>
                            </div>
                            <div class="mb-3">
                                <label>Tempat Lahir</label>
                                <input type="text" class="form-control" id="tempat_lahir" required>
                            </div>
                            <div class="mb-3">
                                <label>Tanggal Lahir</label>
                                <input type="date" class="form-control" id="tanggal_lahir" required>
                            </div>
                            <div class="mb-3">
                                <label>Alamat</label>
                                <textarea class="form-control" id="alamat" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Tanggal mulai asuransi</label>
                                <input type="date" class="form-control" id="tanggal_mulai_asuransi" required>
                            </div>
                            <div class="mb-3">
                                <label>Tanggal selesai asuransi</label>
                                <input type="date" class="form-control" id="tanggal_selesai_asuransi" required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('includes.script')

        <script>
            $(document).ready(function() {

                var table = $('#pesertaTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ url('list-peserta-terima-data-dokumen') }}",
                        type: "GET",
                        dataType: "json",
                        beforeSend: function(xhr) {
                            if (typeof this.currentXHR !== 'undefined') {
                                this.currentXHR.abort();
                            }
                            this.currentXHR = xhr;
                        },
                        complete: function() {
                            this.currentXHR = null;
                        },
                        error: function(xhr) {
                            if (xhr.statusText !== 'abort') {
                                console.log(xhr.responseText);
                                alert('Terjadi kesalahan, coba refresh halaman.');
                            }
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'tempat_lahir',
                            name: 'tempat_lahir'
                        },
                        {
                            data: 'tanggal_lahir',
                            name: 'tanggal_lahir'
                        },
                        {
                            data: 'umur',
                            name: 'umur'
                        },
                        {
                            data: 'alamat',
                            name: 'alamat'
                        },
                        {
                            data: 'masa_asuransi',
                            name: 'masa_asuransi'
                        },
                        {
                            data: 'durasi_asuransi',
                            name: 'durasi_asuransi'
                        },
                        {
                            data: 'status_peserta',
                            name: 'status_peserta'
                        },
                        {
                            data: 'status_dokumen',
                            name: 'status_dokumen'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                var searchDelay;
                $('#pesertaTable_filter input').unbind().keyup(function() {
                    clearTimeout(searchDelay);
                    searchDelay = setTimeout(function() {
                        table.search(this.value).draw();
                    }.bind(this), 500);
                });

                // Tambah Peserta
                $('#btn-add').click(function() {
                    $('#modalLabel').text('Tambah Peserta');
                    $('#formPeserta')[0].reset();
                    $('#peserta_id').val('');
                    $('#modalPeserta').modal('show');
                });

                // Simpan Peserta
                $('#formPeserta').submit(function(e) {
                    e.preventDefault();
                    var id = $('#peserta_id').val();
                    var url = id ? '/peserta/update/' + id : '/peserta/store';

                    $.ajax({
                        url: url,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id, // Untuk update
                            nama: $('#nama').val(),
                            tempat_lahir: $('#tempat_lahir').val(),
                            tanggal_lahir: $('#tanggal_lahir').val(),
                            alamat: $('#alamat').val(),
                            tanggal_mulai_asuransi: $('#tanggal_mulai_asuransi').val(),
                            tanggal_selesai_asuransi: $('#tanggal_selesai_asuransi').val()
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $('#modalPeserta').modal('hide');
                                table.ajax.reload();
                                alert(response.message);
                            } else {
                                alert('Terjadi kesalahan saat menyimpan data.');
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Gagal simpan.');
                        }
                    });
                });



                // Edit Peserta
                $(document).on('click', '.edit', function() {
                    var id = $(this).attr('data-id');
                    console.log("ID Peserta:", id);

                    $.get('/peserta/edit/' + id, function(data) {
                        $('#modalLabel').text('View Peserta');
                        $('#peserta_id').val(data.id);
                        $('#nama').val(data.nama);
                        $('#tempat_lahir').val(data.tempat_lahir);
                        $('#tanggal_lahir').val(data.tanggal_lahir);
                        $('#umur').val(data.umur);
                        $('#alamat').val(data.alamat);
                        $('#tanggal_mulai_asuransi').val(data.tanggal_mulai_asuransi);
                        $('#tanggal_selesai_asuransi').val(data.tanggal_selesai_asuransi);
                        $('#modalPeserta').modal('show');
                    }).fail(function(xhr) {
                        alert('Terjadi kesalahan: ' + xhr.responseText);
                    });
                });

                // Upload Dokumen
                $(document).on('click', '.upload', function() {
                    let id = $(this).data('id');
                    $('#uploadModal').modal('show');
                    $('#peserta_id').val(id);
                });

                // Hapus Peserta
                $(document).on('click', '.delete', function() {
                    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                        var id = $(this).data('id');
                        $.ajax({
                            url: '/peserta/delete/' + id,
                            method: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                table.ajax.reload();
                                alert(response.message);
                            }
                        });
                    }
                });

                // Upload Dokumen
                $(document).on('submit', '#uploadForm', function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);

                    $.ajax({
                        url: '/peserta/upload-dokumen',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#uploadModal').modal('hide');
                            alert(response.message);
                        },
                        error: function(xhr) {
                            alert('Error: ' + JSON.stringify(xhr.responseJSON));
                        }
                    });
                });

            });
        </script>

    </body>
@endsection
