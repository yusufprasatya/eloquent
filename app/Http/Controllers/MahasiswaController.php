<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MahasiswaController extends Controller
{
    //Select semua data dari DB
    public function all()
    {
        $mahasiswas = DB::select('SELECT * FROM mahasiswas');
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan <br>";
        }
    }

    //Select join data dengan query where
    public function gabung1()
    {
        $mahasiswas = DB::select('SELECT * FROM mahasiswas, nilais WHERE mahasiswas.id = nilais.mahasiswa_id');
        dump($mahasiswas);
    }

    public function gabung2()
    {
        $mahasiswas = DB::select('SELECT * FROM mahasiswas, nilais WHERE mahasiswas.id = nilais.mahasiswa_id');
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan |";
            echo "$mahasiswa->sem_1 | $mahasiswa->sem_2 | $mahasiswa->sem_3 <br>";
        }
    }

    //Select dengan JOIN
    public function gabungJoin1()
    {
        $mahasiswas = DB::select('SELECT * FROM mahasiswas JOIN nilais ON mahasiswas.id = nilais.mahasiswa_id');
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan |";
            echo "$mahasiswa->sem_1 | $mahasiswa->sem_2 | $mahasiswa->sem_3 <br>";
        }
    }

    public function gabungJoin2()
    {
        $mahasiswas = DB::select('SELECT * FROM mahasiswas JOIN nilais ON mahasiswas.id = nilais.mahasiswa_id WHERE jurusan = "Ilmu Komputer"');
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan |";
            echo "$mahasiswa->sem_1 | $mahasiswa->sem_2 | $mahasiswa->sem_3 <br>";
        }
    }

    public function gabungJoin3()
    {
        $mahasiswas = DB::select('SELECT * FROM mahasiswas JOIN nilais ON mahasiswas.id = nilais.mahasiswa_id WHERE nilais.sem_2 > 3');
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan |";
            echo "$mahasiswa->sem_1 | $mahasiswa->sem_2 | $mahasiswa->sem_3 <br>";
        }
    }

    public function find()
    {
        $mahasiswa = Mahasiswa::find(3);
        echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan | ";
        echo "{$mahasiswa->nilai->sem_1} | {$mahasiswa->nilai->sem_2} | ";
        echo "{$mahasiswa->nilai->sem_3}";
    }

    public function where()
    {
        $mahasiswa = Mahasiswa::where('nama', 'Hesti Ramadan')->first();
        echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan | ";
        echo "{$mahasiswa->nilai->sem_1}, | {$mahasiswa->nilai->sem_2} | ";
        echo "{$mahasiswa->nilai->sem_3}";
    }

    public function whereChaining()
    {
        $nlai = Mahasiswa::where('nama', 'Queen Suryatmi')->first()->nilai->sem_2;
        echo $nlai;
    }

    public function allJoin()
    {
        // $mahasiswas = Mahasiswa::all();

        //Dengan mengoptimasi N+1 problem
        $mahasiswas = Mahasiswa::with('nilai')->get();
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan | ";
            echo $mahasiswa->nilai->sem_1 ?? 'N/A';
            echo " | ";
            echo $mahasiswa->nilai->sem_2 ?? 'N/A';
            echo " | ";
            echo $mahasiswa->nilai->sem_3 ?? 'N/A';
            echo " | ";
            echo "<br>";
        }
    }

    public function has()
    {
        //Hanya menampilkan data yang memiliki pasangan pada tabel kedua
        $mahasiswas = Mahasiswa::has('nilai')->get();
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan | ";
            echo $mahasiswa->nilai->sem_1 ?? 'N/A';
            echo " | ";
            echo $mahasiswa->nilai->sem_2 ?? 'N/A';
            echo " | ";
            echo $mahasiswa->nilai->sem_3 ?? 'N/A';
            echo " | ";
            echo "<br>";
        }
    }

    public function whereHas()
    {
        //Query data dengan syarat tambahan whereHas
        $mahasiswas = Mahasiswa::whereHas('nilai', function ($query) {
            $query->where('sem_1', '>=', 3);
        })->get();

        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan | ";
            echo "{$mahasiswa->nilai->sem_1} | {$mahasiswa->nilai->sem_2} | ";
            echo "{$mahasiswa->nilai->sem_3} <br>";
        }
    }

    public function doesntHave()
    {
        //Kebalikan dari whereHas yaitu menampilkan data yang tidak memiliki nilai pasanga ndari tabel pertama/utama.
        $mahasiswas = Mahasiswa::doesntHave('nilai')->get();
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan | <br> ";
        }
    }

    public function whereDoesntHave()
    {
        //dengan penambahaan kondisi ipk > 3
        $mahasiswas = Mahasiswa::whereDoesntHave('nilai', function ($query) {
            $query->where('sem_1', '>=', 3);
        })->get();
        foreach ($mahasiswas as $mahasiswa) {
            echo "$mahasiswa->nim | $mahasiswa->nama | $mahasiswa->jurusan | <br> ";
        }
    }

    public function insertSave()
    {
        $mahasiswa = new Mahasiswa;
        $mahasiswa->nim = '01180142';
        $mahasiswa->nama = 'Muhamad Yusuf';
        $mahasiswa->jurusan = 'Informatika';
        $mahasiswa->save();

        $nilai = new Nilai;
        $nilai->sem_1 = 3.11;
        $nilai->sem_2 = 3.40;
        $nilai->sem_3 = 3.77;

        //input mahasiswa id yang didapat dari id mahasiswa yang telah diinput sebelumnya.
        $mahasiswa->nilai()->save($nilai);
        echo "Penambahan $mahasiswa->nama ke database berhasil";
    }

    public function insertCreate()
    {
        $mahasiswa = Mahasiswa::create(
            [
                'nim' => '01189892',
                'nama' => 'Raju Pratama',
                'jurusan' => 'Sistem Informasi',
            ]
        );

        $mahasiswa->nilai()->create(
            [
                'sem_1' => 3.55,
                'sem_2' => 2.98,
                'sem_3' => 3.33,

            ]
        );
        echo "Penambahan $mahasiswa->nama ke database berhasil";
    }

    public function updateMassAssignment()
    {
        $mahasiswa = Mahasiswa::find(12);

        $mahasiswa->nilai()->update(
            [
                'sem_1' => 3.00,
                'sem_2' => 4.00,
                'sem_3' => 3.25,
            ]
        );
        echo "Update nilai $mahasiswa->nama berhasil";
    }

    public function updatePush()
    {
        $mahasiswa = Mahasiswa::find(11);

        $mahasiswa->nilai->sem_1 = 2.44;
        $mahasiswa->nilai->sem_2 = 3.22;
        $mahasiswa->nilai->sem_3 = 4.00;

        $mahasiswa->push();
        echo "Update nilai $mahasiswa->nama berhasil";
    }

    public function updatePushWhere()
    {
        $mahasiswa = Mahasiswa::where('nama', 'Raju Pratama')->first();

        $mahasiswa->nilai->sem_1 = 0.00;
        $mahasiswa->nilai->sem_2 = 0.00;
        $mahasiswa->nilai->sem_3 = 0.00;

        $mahasiswa->push();
        echo "Update nilai $mahasiswa->nama berhasil";
    }


    /*
    Delete relationship dengan menerapkan konsep refrential itegrity
       
    */

    public function deleteFind()
    {
        $mahasiswa = Mahasiswa::find(11);
        $mahasiswa->nilai->delete();
        $mahasiswa->delete();
        echo "Data $mahasiswa->nama berhasil di hapus";
    }

    public function deleteWhere()
    {
        $mahasiswa = Mahasiswa::where('nama', 'Kambali Mulyani')->firstOrFail();
        $mahasiswa->nilai->delete();
        $mahasiswa->delete();
        echo "Data $mahasiswa->nama berhasil di hapus";
    }

    public function deleteIf()
    {
        $mahasiswa = Mahasiswa::where('nama', 'Marsito Purnawati')->firstOrFail();
        if (!empty($mahasiswa->nilai)) {
            $mahasiswa->nilai->delete();
        }
        $mahasiswa->delete();
        echo "Data $mahasiswa->nama berhasil di hapus";
    }

    public function deleteCascade()
    {
        $mahasiswa = Mahasiswa::where('nama', 'Mustofa Simanjuntak')->firstOrFail();
        $mahasiswa->delete();
        echo "Data $mahasiswa->nama berhasil di hapus";

        $mahasiswa = Mahasiswa::where('nama', 'Ika Puspasari')->firstOrFail();
        $mahasiswa->delete();
        echo "Data $mahasiswa->nama berhasil di hapus";
    }
}
