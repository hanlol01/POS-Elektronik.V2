<?php
/*
* PROSES TAMPIL
*/
class view
{
    protected $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function member()
    {
        $sql = "select member., login.
                from member inner join login on member.id_member = login.id_member";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function member_edit($id)
    {
        $sql = "select member., login.
                from member inner join login on member.id_member = login.id_member
                where member.id_member= ?";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($id));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function toko()
    {
        $sql = "select*from toko where id_toko='1'";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function kategori()
    {
        $sql = "select*from kategori";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function barang()
    {
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori 
                ORDER BY id DESC";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function barang_stok()
    {
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori 
                where stok <= 3 
                ORDER BY id DESC";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function barang_edit($id)
    {
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori
                where id_barang=?";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($id));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function barang_cari($cari)
    {
        $sql = "select barang.*, kategori.id_kategori, kategori.nama_kategori
                from barang inner join kategori on barang.id_kategori = kategori.id_kategori
                where id_barang like '%$cari%' or nama_barang like '%$cari%' or merk like '%$cari%'";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function barang_id()
    {
        $sql = 'SELECT * FROM barang ORDER BY id DESC';
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();

        $urut = substr($hasil['id_barang'], 2, 3);
        $tambah = (int) $urut + 1;
        if (strlen($tambah) == 1) {
            $format = 'BR00'.$tambah.'';
        } elseif (strlen($tambah) == 2) {
            $format = 'BR0'.$tambah.'';
        } else {
            $ex = explode('BR', $hasil['id_barang']);
            $no = (int) $ex[1] + 1;
            $format = 'BR'.$no.'';
        }
        return $format;
    }

    public function kategori_edit($id)
    {
        $sql = "select*from kategori where id_kategori=?";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array($id));
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function kategori_row()
    {
        $sql = "select*from kategori";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> rowCount();
        return $hasil;
    }

    public function barang_row()
    {
        $sql = "select*from barang";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> rowCount();
        return $hasil;
    }

    public function barang_stok_row()
    {
        $sql ="SELECT SUM(stok) as jml FROM barang";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function barang_beli_row()
    {
        $sql ="SELECT SUM(harga_beli) as beli FROM barang";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function jual_row()
    {
        $sql ="SELECT SUM(jumlah) as stok FROM nota";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function jual()
    {
        $sql ="SELECT nota.* , barang.id_barang, barang.nama_barang, barang.harga_beli, member.id_member,
                member.nm_member from nota 
                left join barang on barang.id_barang=nota.id_barang 
                left join member on member.id_member=nota.id_member 
                where nota.periode = ?
                ORDER BY id_nota DESC";
        $row = $this-> db -> prepare($sql);
        $row -> execute(array(date('m-Y')));
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function periode_jual($periode, $metode_pembayaran = '')
    {
        $sql = "SELECT nota.* , barang.id_barang, barang.nama_barang, barang.harga_beli, member.id_member,
                member.nm_member from nota 
                left join barang on barang.id_barang=nota.id_barang 
                left join member on member.id_member=nota.id_member WHERE nota.periode = ? ";
        if ($metode_pembayaran != '') {
            $sql .= " AND nota.metode_pembayaran = ?";
        }
        $sql .= " ORDER BY id_nota ASC";
        $row = $this-> db -> prepare($sql);
        if ($metode_pembayaran != '') {
            $row -> execute(array($periode, $metode_pembayaran));
        } else {
            $row -> execute(array($periode));
        }
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function hari_jual($dari_tanggal, $sampai_tanggal, $metode_pembayaran = '')
    {
        // Ubah sampai_tanggal menjadi hari berikutnya
        $sampai_tanggal = date('Y-m-d', strtotime($sampai_tanggal . ' +1 day'));

        $sql = "SELECT nota.*, barang.id_barang, barang.nama_barang, barang.harga_beli, member.id_member,
                member.nm_member FROM nota 
                LEFT JOIN barang ON barang.id_barang = nota.id_barang 
                LEFT JOIN member ON member.id_member = nota.id_member 
                WHERE nota.tanggal_input >= ? AND nota.tanggal_input < ? ";
        
        if ($metode_pembayaran != '') {
            $sql .= " AND nota.metode_pembayaran = ?";
        }
        
        $sql .= " ORDER BY id_nota ASC";
        $row = $this->db->prepare($sql);
        
        if ($metode_pembayaran != '') {
            $row->execute(array($dari_tanggal, $sampai_tanggal, $metode_pembayaran));
        } else {
            $row->execute(array($dari_tanggal, $sampai_tanggal));
        }
        
        return $row->fetchAll();
    }

    public function penjualan()
    {
        $sql ="SELECT penjualan.* , barang.id_barang, barang.nama_barang, member.id_member,
                member.nm_member from penjualan 
                left join barang on barang.id_barang=penjualan.id_barang 
                left join member on member.id_member=penjualan.id_member
                ORDER BY id_penjualan";
        $row = $this-> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetchAll();
        return $hasil;
    }

    public function jumlah()
    {
        $sql ="SELECT SUM(total) as bayar FROM penjualan";
        $row = $this -> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function jumlah_nota()
    {
        $sql ="SELECT SUM(total) as bayar FROM nota";
        $row = $this -> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function jml()
    {
        $sql ="SELECT SUM(harga_beli*stok) as byr FROM barang";
        $row = $this -> db -> prepare($sql);
        $row -> execute();
        $hasil = $row -> fetch();
        return $hasil;
    }

    public function laporan_edit($id) {
        $result = $this->db->prepare("SELECT * FROM nota WHERE id_nota = ?");
        $result->execute(array($id));
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function hapus_laporan_jual($id){
        // Mulai transaksi
        $this->db->beginTransaction();
        
        try {
            // Ambil data nota sebelum dihapus
            $sql_select = "SELECT id_barang, jumlah FROM nota WHERE id_nota = ?";
            $result_select = $this->db->prepare($sql_select);
            $result_select->execute(array($id));
            $nota = $result_select->fetch(PDO::FETCH_ASSOC);
            
            if ($nota) {
                // Update stok barang
                $sql_update = "UPDATE barang SET stok = stok + ? WHERE id_barang = ?";
                $result_update = $this->db->prepare($sql_update);
                $result_update->execute(array($nota['jumlah'], $nota['id_barang']));
                
                // Hapus nota
                $sql_delete = "DELETE FROM nota WHERE id_nota = ?";
                $result_delete = $this->db->prepare($sql_delete);
                $result_delete->execute(array($id));
                
                // Commit transaksi
                $this->db->commit();
                return true;
            } else {
                throw new Exception("Nota tidak ditemukan");
            }
        } catch (Exception $e) {
            // Rollback jika terjadi error
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function update_laporan_jual($id, $id_barang, $jumlah_lama, $jumlah_baru){
        $this->db->beginTransaction();

        try {
            // Update nota
            $sql_update_nota = "UPDATE nota SET jumlah = ? WHERE id_nota = ?";
            $result_update_nota = $this->db->prepare($sql_update_nota);
            $result_update_nota->execute(array($jumlah_baru, $id));

            // Update stok barang
            $selisih = $jumlah_lama - $jumlah_baru;
            $sql_update_barang = "UPDATE barang SET stok = stok + ? WHERE id_barang = ?";
            $result_update_barang = $this->db->prepare($sql_update_barang);
            $result_update_barang->execute(array($selisih, $id_barang));

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}