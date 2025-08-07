<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Galeri;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orderBy = $request->get('order_by', 'nama_barang');
        $direction = $request->get('direction', 'asc');
        $search = $request->get('search');
        $status = $request->get('status'); // Tambahkan parameter untuk status
        $stokMinimal = $request->has('stok_minimal');
        $kategori = $request->get('kategori'); // Tambahkan parameter untuk status

        $kategoris = Kategori::all();

        $barangs = Barang::when($search, function ($query, $search) {
            return $query->where('nama_barang', 'like', "%{$search}%");
        })
        ->when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->when($kategori, function ($query, $kategori) {
            return $query->where('kategori_id', $kategori);
        })
        ->when($stokMinimal, function ($query) {
            return $query->whereColumn('stok', '<=', 'stok_minimal')
                         ->where('nama_barang', 'not like', '%second%')
                         ->where('status', '!=', 'arsip');
        })
        ->orderByRaw("status = 'aktif' DESC")
        ->orderBy('populer', 'desc')
        ->selectRaw('*, stok <= stok_minimal AS is_stok_minim, status = "aktif" AS is_aktif, nama_barang not like "%second%" AS isnot_second')
        ->orderBy($orderBy, $direction)
        ->get();

        return view('barang.index_barang', compact('barangs', 'orderBy', 'direction', 'search', 'kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoris = Kategori::all();
        return view('barang.create_barang', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:32|unique:barang',
            'kategori_id' => 'nullable|exists:kategori,id',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric|min:1',
            'deskripsi' => 'required|string|max:1024',
            'status' => 'required|string',
            'stok_minimal' => 'required|numeric',
            'gambar' => 'nullable', // Bisa kosong
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Validasi untuk setiap file dalam array 'fotos'
        ]);
        $hargaJual = (1 + ($request->harga_jual/100)) * $request->harga_beli; // kali persen request

        $kodeBarang = Barang::latest('id')->value('id');
        $kodeBarang = $kodeBarang ? $kodeBarang + 1 : 1;
        $kodeBarang = "K-" . str_pad($kodeBarang, 3, '0', STR_PAD_LEFT);

        $barang = [
            'kode_barang' => $kodeBarang,
            'nama_barang' => $request->nama_barang,
            'kategori_id' => $request->kategori_id,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $hargaJual,
            'deskripsi' => $request->deskripsi,
            'stok_minimal' => $request->stok_minimal,
            'status' => $request->status,
        ];
        $barang = Barang::create($barang);
        $message = '';
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $foto) {
                $path = $foto->store('gambar_barang', 'public');
                Galeri::create([
                    'barang_id' => $barang->id,
                    'file_path' => $path,
                ]);
            }
            $message .= 'beserta gambar ';
        }

        return redirect()->route('barang.index')->with('success', 'Barang ' . $message . 'berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $barang = Barang::with('kategori', 'galeri')->findOrFail($id);
        return view('barang.show_barang', compact('barang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barang = Barang::with('kategori')->findOrFail($id);
        $kategoris =Kategori::all();
        $persenJual = (($barang->harga_jual - $barang->harga_beli) / $barang->harga_beli) * 100 ; //display persen jual sebelumnya
        return view('barang.edit_barang', compact('barang', 'kategoris', 'persenJual'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            // 'kode_barang' => 'required|string|max:32|unique:barang,kode_barang,' . $id,
            'nama_barang' => 'required|string|max:32|unique:barang,nama_barang,'.$id,
            'kategori_id' => 'nullable|exists:kategori,id',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric|min:1',
            'deskripsi' => 'required|string|max:1024',
            'status' => 'required|string',
            'stok_minimal' => 'required|numeric',
            'gambar' => 'nullable', // Bisa kosong
            'gambar.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Validasi untuk setiap file dalam array 'fotos'
        ]);
        
        try {
            $barang = Barang::findOrFail($id);
            
            $hargaJual = (1 + ($request->harga_jual/100)) * $request->harga_beli;
            
            $barang->update([
                'harga_lama' => $barang->harga_jual,
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $request->nama_barang,
                'kategori_id' => $request->kategori_id,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $hargaJual,
                'deskripsi' => $request->deskripsi,
                'stok_minimal' => $request->stok_minimal,
                'status' => $request->status,
            ]);

            $message = '';
            if ($request->hasFile('gambar')) {
                foreach ($request->file('gambar') as $foto) {
                    $path = $foto->store('gambar_barang', 'public');
                    Galeri::create([
                        'barang_id' => $barang->id,
                        'file_path' => $path,
                    ]);
                }
                $message .= 'beserta foto ';
            }
            return redirect()->route('barang.show', $barang->id )->with('success', 'Barang ' . $message . 'berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->route('barang.edit')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);
        // Cek apakah barang sedang digunakan di penjualan / pembelian
        if ($barang->penjualans()->count() > 0 || $barang->pembelians()->count() > 0) {
            return redirect()->back()->with('error', 'Barang tidak dapat dihapus, karena digunakan di penjualan / pembelian.');
        }
        // Hapus gambar-gambar terkait
        foreach ($barang->galeri as $galeri) {
            Storage::disk('public')->delete($galeri->file_path);
        }

        // Hapus data galeri dan barang
        $barang->galeri()->delete(); // Hapus semua data galeri terkait
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }

    /**
     * Function Status Arsip dan Aktif view index.
     */
    public function toggleStatus(Request $request)
    {
        try {
            $barang = Barang::findOrFail($request->id);
            $barang->status = $request->status;
            $barang->save();

            return response()->json(['success' => true, 'message' => 'Status barang berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah status barang.']);
        }
    }

    public function deleteImage($id)
    {
        $galeri = Galeri::find($id);

        if ($galeri) {
            // Hapus file gambar dari storage
            Storage::disk('public')->delete($galeri->file_path);

            // Hapus data dari database
            $galeri->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Gambar tidak ditemukan'], 404);
    }
    
    /*  */
    /* BARANG API */
    /*  */
    public function getBarangs(Request $request)
    {
        $orderBy = $request->get('order_by', 'nama_barang');  // Default sorting by nama_barang
        $direction = $request->get('direction', 'asc');       // Default direction is ascending
        $search = $request->get('search');  // Ambil parameter pencarian

        // Query dengan pencarian dan pengurutan
        $barangs = Barang::with('kategori', 'galeri')->when($search, function ($query, $search) {
                return $query->where('nama_barang', 'like', "%{$search}%");
            })
            ->whereRaw('status != "arsip"')
            ->orderBy($orderBy, $direction)
            ->get();

        // Kembalikan sebagai JSON response
        return response()->json($barangs);
    }
    public function getBarangsKembali(Request $request)
    {
        $orderBy = $request->get('order_by', 'nama_barang');  // Default sorting by nama_barang
        $direction = $request->get('direction', 'asc');       // Default direction is ascending
        $searchWords = ['second', 'modif'];  // Kata-kata pencarian

        // Query dengan pencarian dan pengurutan
        $barangs = Barang::where(function ($query) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $query->orWhere('nama_barang', 'like', "%{$word}%");
                    }
                })
                ->where('status', '!=', 'arsip')  // Status tidak sama dengan arsip
                ->orderBy($orderBy, $direction)
                ->get();

        // Kembalikan sebagai JSON response
        return response()->json($barangs);
    }

    /*  */
    /* BARANG API */
    /*  */
    public function getBarangsPublic(Request $request)
    {
        $orderBy = $request->get('order_by', 'nama_barang');  // Default sorting by nama_barang
        $direction = $request->get('direction', 'asc');       // Default direction is ascending
        $search = $request->get('search');  // Ambil parameter pencarian

        // Query dengan pencarian dan pengurutan
        $barangs = Barang::with('kategori', 'galeri')->when($search, function ($query, $search) {
                return $query->where('nama_barang', 'like', "%{$search}%");
            })
            ->whereRaw('status != "arsip"')
            ->orderBy('populer', 'desc')
            ->orderBy($orderBy, $direction)
            ->get();

        // Kembalikan sebagai JSON response
        return response()->json(
            $barangs->map(function ($barang) {
                return [
                    'id' => $barang->id,
                    'kategori' => $barang->kategori->nama_kategori ?? null,
                    'nama_barang' => $barang->nama_barang,
                    'stok' => $barang->stok,
                    'terjual' => $barang->terjual,
                    'harga_jual' => $barang->harga_jual,
                    'harga_lama' => $barang->harga_lama,
                    'deskripsi' => $barang->deskripsi,
                    'created_at' => $barang->created_at,
                    'updated_at' => $barang->updated_at,
                    'galeri' => $barang->galeri,
                ];
            })
        );

    }
}