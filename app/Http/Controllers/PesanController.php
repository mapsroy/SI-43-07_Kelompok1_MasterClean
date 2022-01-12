<?php

namespace App\Http\Controllers;
use App\Layanan;
use App\Pesanan;
use App\PesananDetail;
use Auth;
use Alert;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PesanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
        $layanan = Layanan::where('id', $id)->first();

        return view('pesan.index', compact('layanan'));
    }
    public function pesan(Request $request, $id)
    {
        $layanan = Layanan::where('id', $id)->first();   
        $tanggal = Carbon::now();

        if($request->jumlah_pesan > $layanan->nama_layanan)
        {
           return redirect('pesan/'.$id); 
        }

        $cek_pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();

        if(empty($cek_pesanan))
        {
            $pesanan = new Pesanan;
            $pesanan->user_id = Auth::user()->id;
            $pesanan->tanggal = $tanggal;
            $pesanan->status = 0;
            $pesanan->jumlah_harga = 0;
            $pesanan->save();
        }

        $pesanan_baru = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();

        $cek_pesanan_detail = PesananDetail::where('layanan_id', $layanan->id)->where('pesanan_id', $pesanan_baru->id)->first();
        if(empty($cek_pesanan_detail))
        {
            $pesanan_detail = new PesananDetail;
            $pesanan_detail->layanan_id = $layanan->id;
            $pesanan_detail->pesanan_id = $pesanan_baru->id;
            $pesanan_detail->jumlah = $request->jumlah_pesan;
            $pesanan_detail->jumlah_harga = $layanan->harga*$request->jumlah_pesan;
            $pesanan_detail->save();
        }else{
            $pesanan_detail = PesananDetail::where('layanan_id', $layanan->id)->where('pesanan_id', $pesanan_baru->id)->first();
            $pesanan_detail->jumlah = $pesanan_detail->jumlah+$request->jumlah_pesan;
            
            $harga_pesanan_detail_baru = $layanan->harga*$request->jumlah_pesan;
            $pesanan_detail->jumlah_harga = $pesanan_detail->jumlah_harga+$harga_pesanan_detail_baru;
            $pesanan_detail->update();
        }
        
        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga+$layanan->harga*$request->jumlah_pesan;
        $pesanan->update();

        Alert::success('Pesanan Telah Berhasil DiMasukkan', 'Success!');
        return redirect('home');

    }

    public function check_out()
    {
        $pesanan = Pesanan::where('user_id', Auth::user()->id)->where('status',0)->first();
        $pesanan_details = PesananDetail::where('pesanan_id', $pesanan->id)->get();

        return view('pesan.check_out', compact('pesanan', 'pesanan_details'));
    }
    
    public function delete($id)
    {
        $pesanan_detail = PesananDetail::where('id', $id)->first();

        $pesanan = Pesanan::where('id', $pesanan_detail->pesanan_id)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga-$pesanan_detail->jumlah_harga;
        $pesanan->update();
        
        $pesanan_detail->delete();
        
        Alert::error('Pesanan Telah Berhasil Dihapus', 'Success!');
        return redirect('check-out');
    }

    public function konfirmasi()
    {
        $pesanan = Pesanan::where('id', $pesanan_detail->pesanan_id)->first();
        $pesanan-> status = 1;
        $pesanan->update();

        Alert::error('Pesanan Telah Berhasil Di Check Out', 'Success!');
        return redirect('check-out');
    }
}
