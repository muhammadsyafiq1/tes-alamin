<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use App\Models\Peserta;
use Illuminate\Http\Request;
use App\Models\DokumenPeserta;
use Barryvdh\DomPDF\Facade\Pdf;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!session()->has('jwt_token')) {
                return redirect()->route('halaman-login')->withErrors(['message' => 'Silakan login terlebih dahulu.']);
            }

            try {
                $token = session('jwt_token');
                $user = JWTAuth::setToken($token)->authenticate();

                if (!$user) {
                    Auth::logout();
                    return redirect()->route('halaman-login')->withErrors(['message' => 'Token tidak valid, silakan login ulang.']);
                }

                Auth::login($user);
            } catch (\Exception $e) {
                Auth::logout();
                return redirect()->route('halaman-login')->withErrors(['message' => 'Token kedaluwarsa atau tidak valid.']);
            }

            return $next($request);
        });
    }

    public function insertPeserta(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = Peserta::query();
                return DataTables::eloquent($query)
                    ->filter(function ($query) use ($request) {
                        if ($request->has('search') && !empty($request->search['value'])) {
                            $search = $request->search['value'];
                            $query->where(function ($q) use ($search) {
                                $q->where('nama', 'like', '%' . $search . '%')
                                    ->orWhere('tempat_lahir', 'like', '%' . $search . '%')
                                    ->orWhere('alamat', 'like', '%' . $search . '%')
                                    ->orWhere('status_peserta', 'like', '%' . $search . '%');
                            });
                        }
                    })
                    ->addIndexColumn()
                    ->addColumn('masa_asuransi', function ($row) {
                        return $row->tanggal_mulai_asuransi . ' s/d ' . $row->tanggal_selesai_asuransi;
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group" role="group">
                                    <button class="btn btn-warning btn-sm edit mr-2" data-id="' . $row->id . '">Edit</button>
                                    <button class="btn btn-danger btn-sm delete" data-id="' . $row->id . '">Hapus</button>
                                </div>';
                    })
                    ->editColumn('tanggal_lahir', function ($row) {
                        return date('d-m-Y', strtotime($row->tanggal_lahir));
                    })
                    ->editColumn('tanggal_mulai_asuransi', function ($row) {
                        return date('d-m-Y', strtotime($row->tanggal_mulai_asuransi));
                    })
                    ->editColumn('tanggal_selesai_asuransi', function ($row) {
                        return date('d-m-Y', strtotime($row->tanggal_selesai_asuransi));
                    })
                    ->rawColumns(['action', 'status_peserta', 'status_dokumen'])
                    ->make(true);
            }

            return view('insert-peserta', ['data' => Auth::user()]);
        } catch (\Exception $e) {
            Log::error('Error in insertPeserta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listPesertaDataDiterima(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Peserta::orderBy('id', 'desc')->where('status_peserta', 'diterima')
                    ->whereDoesntHave('dokumen')
                    ->where('status_dokumen', 'pending');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('masa_asuransi', function ($row) {
                        return $row->tanggal_mulai_asuransi . ' s/d ' . $row->tanggal_selesai_asuransi;
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm edit mr-2" data-id="' . $row->id . '">View</button>
                                <button class="btn btn-success btn-sm upload mr-2" data-id="' . $row->id . '">Upload</button>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('list-peserta-data-diterima', ['data' => Auth::user()]);
        } catch (\Exception $e) {
            Log::error('Error mengambil data data diterima: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data peserta yang diterima.'], 500);
        }
    }

    public function listPesertaPending(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Peserta::where('status_peserta', 'pending')
                    ->whereDoesntHave('dokumen');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('masa_asuransi', function ($row) {
                        return $row->tanggal_mulai_asuransi . ' s/d ' . $row->tanggal_selesai_asuransi;
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm edit mr-2" data-id="' . $row->id . '">Edit</button>
                                <button class="btn btn-success btn-sm approve" data-id="' . $row->id . '">Approve</button>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('list-peserta-pending', ['data' => Auth::user()]);
        } catch (\Exception $e) {
            Log::error('Error mengambil data pending: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data peserta pending.'], 500);
        }
    }

    public function listPesertaUploadDokumen(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Peserta::whereHas('dokumen')
                    ->where('status_peserta', 'diterima')
                    ->where('status_dokumen', 'pending');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('masa_asuransi', function ($row) {
                        return $row->tanggal_mulai_asuransi . ' s/d ' . $row->tanggal_selesai_asuransi;
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm edit mr-2" data-id="' . $row->id . '">Edit</button>
                                <button class="btn btn-primary btn-sm approve" data-id="' . $row->id . '">Approve</button>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('list-peserta-upload-dokumen', ['data' => Auth::user()]);
        } catch (\Exception $e) {
            Log::error('Error mengambil data upload dokumen: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data peserta upload dokumen.'], 500);
        }
    }

    public function listPesertaTerimaDataDokumen(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Peserta::whereHas('dokumen')
                    ->where('status_peserta', 'diterima')
                    ->where('status_dokumen', 'diterima');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('masa_asuransi', function ($row) {
                        return $row->tanggal_mulai_asuransi . ' s/d ' . $row->tanggal_selesai_asuransi;
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm edit mr-2" data-id="' . $row->id . '">View</button>
                                <a href="' . url('peserta/covernote/' . $row->id) . '" target="_blank" class="btn btn-success btn-sm">Cetak Covernote</a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('list-peserta-terima-data-dokumen', ['data' => Auth::user()]);
        } catch (\Exception $e) {
            Log::error('Error mengambil data terima data dokumen: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data peserta yang diterima.'], 500);
        }
    }

    public function listPesertaDataDitolak(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Peserta::where('status_peserta', 'tolak');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('masa_asuransi', function ($row) {
                        return $row->tanggal_mulai_asuransi . ' s/d ' . $row->tanggal_selesai_asuransi;
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm edit mr-2" data-id="' . $row->id . '">View</button>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('list-peserta-data-ditolak', ['data' => Auth::user()]);
        } catch (\Exception $e) {
            Log::error('Error mengambil data data ditolak: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data peserta yang ditolak.'], 500);
        }
    }

    public function listPesertaDokumenDitolak(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Peserta::where('status_dokumen', 'tolak');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('masa_asuransi', function ($row) {
                        return $row->tanggal_mulai_asuransi . ' s/d ' . $row->tanggal_selesai_asuransi;
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="btn-group" role="group">
                                <button class="btn btn-warning btn-sm edit mr-2" data-id="' . $row->id . '">View</button>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return view('list-peserta-dokumen-ditolak', ['data' => Auth::user()]);
        } catch (\Exception $e) {
            Log::error('Error mengambil data dokumen ditolak: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengambil data peserta dokumen ditolak.'], 500);
        }
    }

    public function storePeserta(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Data Diterima:', $request->all());

            $umur = Carbon::parse($request->tanggal_lahir)->age;
            $durasi_asuransi = Carbon::parse($request->tanggal_mulai_asuransi)
                ->diffInMonths(Carbon::parse($request->tanggal_selesai_asuransi));

            $request->merge([
                'durasi_asuransi' => $durasi_asuransi,
                'uuid' => Uuid::uuid4()->toString(),
                'umur' => $umur
            ]);

            $request->validate([
                'nama' => 'required',
                'tempat_lahir' => 'required',
                'tanggal_lahir' => 'required|date',
                'umur' => 'required|integer',
                'alamat' => 'required',
                'tanggal_mulai_asuransi' => 'required|date',
                'tanggal_selesai_asuransi' => 'required|date',
            ]);

            $data = $request->except('id');

            if ($request->filled('id')) {
                $peserta = Peserta::updateOrCreate(['id' => $request->id], $data);
            } else {
                $peserta = Peserta::create($data);
            }


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan!',
                'data' => $peserta
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing peserta: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data peserta.',
            ], 500);
        }
    }


    public function editPeserta($id)
    {
        $peserta = Peserta::findOrFail($id);
        return response()->json($peserta);
    }

    public function uploadDokumen(Request $request)
    {
        $request->validate([
            'peserta_id' => 'required|exists:peserta,id',
            'file_ktp' => 'required|mimes:pdf,jpg,png|max:2048',
            'file_kk' => 'required|mimes:pdf,jpg,png|max:2048',
            'file_keterangan_sehat' => 'required|mimes:pdf,jpg,png,docx,doc|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $uploadedFiles = [];
            $pesertaId = $request->peserta_id;

            // Pastikan folder penyimpanan ada
            $basePath = public_path('dokumen_peserta');
            if (!file_exists($basePath)) {
                mkdir($basePath, 0777, true);
            }

            if ($request->hasFile('file_ktp')) {
                $file = $request->file('file_ktp');
                $filename = 'file_ktp_' . $pesertaId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('dokumen_peserta/ktp');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $uploadedFiles['file_ktp'] = 'dokumen_peserta/ktp/' . $filename;
            }

            if ($request->hasFile('file_kk')) {
                $file = $request->file('file_kk');
                $filename = 'file_kk_' . $pesertaId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('dokumen_peserta/kk');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $uploadedFiles['file_kk'] = 'dokumen_peserta/kk/' . $filename;
            }

            if ($request->hasFile('file_keterangan_sehat')) {
                $file = $request->file('file_keterangan_sehat');
                $filename = 'file_keterangan_sehat_' . $pesertaId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('dokumen_peserta/surat_sehat');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $file->move($destinationPath, $filename);
                $uploadedFiles['file_keterangan_sehat'] = 'dokumen_peserta/surat_sehat/' . $filename;
            }

            DokumenPeserta::create([
                'uuid' => Uuid::uuid4()->toString(),
                'id_peserta' => $pesertaId,
                'file_ktp' => $uploadedFiles['file_ktp'],
                'file_kk' => $uploadedFiles['file_kk'],
                'file_keterangan_sehat' => $uploadedFiles['file_keterangan_sehat'],
                'created_by' => Auth::user()->name,
            ]);

            DB::commit();

            return response()->json(['message' => 'Dokumen berhasil diupload']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading dokumen: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat mengupload dokumen.'], 500);
        }
    }

    public function approvePeserta(Request $request)
    {
        try {
            DB::beginTransaction();

            $peserta = Peserta::find($request->id);

            if (!$peserta) {
                return response()->json(['success' => false, 'message' => 'Peserta tidak ditemukan.'], 404);
            }

            $peserta->status_peserta = 'diterima';
            $peserta->approved_peserta_at = Date::now();
            $peserta->approved_peserta_by = Auth::user()->name;
            $peserta->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Peserta berhasil di-approve.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving peserta: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyetujui peserta.'], 500);
        }
    }

    public function approvePesertaDokumen(Request $request)
    {
        try {
            DB::beginTransaction();

            $peserta = Peserta::find($request->id);

            if (!$peserta) {
                return response()->json(['success' => false, 'message' => 'Peserta tidak ditemukan.'], 404);
            }

            $peserta->status_dokumen = 'diterima';
            $peserta->approved_dokumen_at = Date::now();
            $peserta->approved_dokumen_by = Auth::user()->name;
            $peserta->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Dokumen peserta berhasil di-approve.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving dokumen peserta: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyetujui dokumen peserta.'], 500);
        }
    }

    public function cetakCovernote($id)
    {
        $peserta = Peserta::findOrFail($id);
        $data = Auth::user();

        $pdf = Pdf::loadView('pdf.covernote', compact('peserta', 'data'))
            ->setPaper('A4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->stream('covernote.pdf');
    }
}
