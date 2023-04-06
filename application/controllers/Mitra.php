<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mitra extends CI_Controller {

	public function index()
	{
		//CRUD Mitra
	}

	public function lihatMitra(){

		header('Content-Type: application/json');

		$this->db->select("*");
		$this->db->from("tb_mitra");
		$data = $this->db->get()->result_array();

		$status = [
			"kode" => "1",
			"pesan" => "Data Berhasil Diambil",
			"listMitra" => $data
		];

		echo json_encode($status);
	}

public function lihatLayanan(){

	header('Content-Type: application/json');

	$id = $this->input->post('id');

	$this->db->select("*");
	$this->db->from("tb_jenis_pakaian");
	$this->db->where('id_mitra',$id);
	$data = $this->db->get()->result_array();

	$status = [
		"kode" => "1",
		"pesan" => "Data Berhasil Diambil",
		"listPakaian" => $data
	];

	echo json_encode($status);
}

public function searchMitra(){

	header('Content-Type: application/json');

	$query = $this->input->post('query');

	$this->db->select("*");
	$this->db->from("tb_mitra");
	$this->db->like('nama_mitra',$query);
	$data = $this->db->get()->result_array();

	$status = [
		"kode" => "1",
		"pesan" => "Data Berhasil Ditemukan",
		"listMitra" => $data
	];

	echo json_encode($status);
}

public function tambahLayanan(){

	header('Content-Type: application/json');

	$foto = $_FILES['foto']['name'];
	$id_mitra = $this->input->post('id_mitra');

	$namapakaian = $this->input->post('namapakaian');
	$hargapakaian = $this->input->post('hargapakaian');

	$config = array();
	$config['allowed_types'] = '*';
	$config['max_size'] = '2048';
	$config['upload_path'] = 'assets/img/pakaian';
	$config['file_name'] = $id_mitra.$foto;

	$this->load->library('upload', $config, 'coverupload');
	$this->coverupload->initialize($config);
	$this->coverupload->do_upload('foto');

	$data = [
		"id_mitra" => $id_mitra,
		"namapakaian" => $namapakaian,
		"hargapakaian" => $hargapakaian,
		"foto" => 'http://hovercout.net/assets/img/pakaian/'.$id_mitra.$foto
	];

	$this->db->insert('tb_jenis_pakaian',$data);

	$status = [
		"kode" => "1",
		"pesan" => "Layanan Berhasil Ditambahkan",
	];

	echo json_encode($status);
}

public function tambahLayananNoFoto(){

	header('Content-Type: application/json');

	$id_mitra = $this->input->post('id_mitra');
	$namapakaian = $this->input->post('namapakaian');
	$hargapakaian = $this->input->post('hargapakaian');

	$data = [
			"id_mitra" => $id_mitra,
			"namapakaian" => $namapakaian,
			"hargapakaian" => $hargapakaian,
			"foto" => 'http://hovercout.net/assets/img/pakaian/defaultmitra.jpg'
	];

	$this->db->insert('tb_jenis_pakaian',$data);

	$status = [
		"kode" => "1",
		"pesan" => "Layanan Berhasil Ditambahkan."
	];

	echo json_encode($status);
}

public function hapusLayanan(){

	header('Content-Type: application/json');

	$id = $this->input->post('id');

	$this->db->where('id',$id);
	$this->db->delete('tb_jenis_pakaian');

	$status = [
		"kode" => "1",
		"pesan" => "Layanan Berhasil Dihapus",
	];

	echo json_encode($status);
}

public function ubahLayanan(){

	header('Content-Type: application/json');

	$foto = $_FILES['foto']['name'];
	$id = $this->input->post('id');
	$id_mitra = $this->input->post("idMitra");
	$namapakaian = $this->input->post('namapakaian');
	$hargapakaian = $this->input->post('hargapakaian');

	$this->db->select("foto");
	$this->db->from("tb_jenis_pakaian");
	$this->db->where("id",$id);
	$row = $this->db->get()->result_array();

	$fotoLama = substr($row[0]['foto'],21);
	if($row[0]['foto']=="http://hovercout.net/assets/img/pakaian/defaultmitra.jpg"){

	}else{
	unlink(FCPATH . $fotoLama);
	}

	$config = array();
	$config['allowed_types'] = '*';
	$config['max_size'] = '2048';
	$config['upload_path'] = 'assets/img/pakaian';
	$config['file_name'] = $id_mitra.$foto;

	$this->load->library('upload', $config, 'coverupload');
	$this->coverupload->initialize($config);
	$this->coverupload->do_upload('foto');

	$data = [
			"namapakaian" => $namapakaian,
			"hargapakaian" => $hargapakaian,
			"foto" => 'http://hovercout.net/assets/img/pakaian/'.$id_mitra.$foto
	];

	$this->db->where('id',$id);
	$this->db->update("tb_jenis_pakaian",$data);

	$status = [
		"kode" => "1",
		"pesan" => "Perubahan Berhasil Dilakukan."
	];

	echo json_encode($status);
}

public function ubahLayananNoFoto(){

	header('Content-Type: application/json');

	$id = $this->input->post('id');
	$namapakaian = $this->input->post('namapakaian');
	$hargapakaian = $this->input->post('hargapakaian');

	$data = [
			"namapakaian" => $namapakaian,
			"hargapakaian" => $hargapakaian
	];

	$this->db->where('id',$id);
	$this->db->update("tb_jenis_pakaian",$data);

	$status = [
		"kode" => "1",
		"pesan" => "Perubahan Berhasil Dilakukan."
	];

	echo json_encode($status);
}

public function ubahPassword(){

	header('Content-Type: application/json');

	$id = $this->input->post('id');

	$data = [
			"password" => password_hash($this->input->post("password"), PASSWORD_DEFAULT)
	];

	$this->db->where('id',$id);
	$this->db->update("tb_mitra",$data);

	$status = [
		"kode" => "1",
		"pesan" => "Perubahan Berhasil Dilakukan. Silahkan login kembali."
	];

	echo json_encode($status);
}

public function ubahMitra(){

	header('Content-Type: application/json');

	$id = $this->input->post('id');

	$data = [
		"nama_mitra" => $this->input->post("nama_mitra"),
		"email" => $this->input->post("email"),
		"alamat" => $this->input->post("alamat"),
		"latitude" => $this->input->post("latitude"),
		"longitude" => $this->input->post("longitude"),
		"jam_buka" => $this->input->post("jam_buka"),
		"jam_tutup" => $this->input->post("jam_tutup"),
		"nomor_handphone" => "0".$this->input->post("nomor_handphone")
	];

	$this->db->where('id',$id);
	$this->db->update("tb_mitra",$data);

	$status = [
		"kode" => "1",
		"pesan" => "Perubahan Berhasil Dilakukan. Silahkan login kembali."
	];

	echo json_encode($status);
}

public function uploadFoto(){

	header('Content-Type: application/json');

	$foto = $_FILES['foto']['name'];
	$id = $this->input->post('id');

	$this->db->select("foto");
	$this->db->from("tb_mitra");
	$this->db->where("id",$id);
	$row = $this->db->get()->result_array();

	$fotoLama = substr($row[0]['foto'],21);
	if($row[0]['foto']=="http://hovercout.net/assets/img/mitra/defaultmitra.jpg"){

	}else{
	unlink(FCPATH . $fotoLama);
	}

	$config = array();
	$config['allowed_types'] = '*';
	$config['max_size'] = '2048';
	$config['upload_path'] = 'assets/img/mitra';
	$config['file_name'] = $id.$foto;

	$this->load->library('upload', $config, 'coverupload');
	$this->coverupload->initialize($config);
	$this->coverupload->do_upload('foto');

	$data = [
			"foto" => 'http://hovercout.net/assets/img/mitra/'.$id.$foto
	];

	$this->db->where('id',$id);
	$this->db->update("tb_mitra",$data);

	$status = [
		"kode" => 'http://hovercout.net/assets/img/mitra/'.$id.$foto,
		"pesan" => "Foto Berhasil Diubah."
	];

	echo json_encode($status);
}

public function tambahMitra(){

	header('Content-Type: application/json');

	$data = [
		"nama_mitra" => $this->input->post("nama_mitra"),
		"email" => $this->input->post("email"),
		"password" => password_hash($this->input->post("password"), PASSWORD_DEFAULT),
		"alamat" => $this->input->post("alamat"),
		"latitude" => $this->input->post("latitude"),
		"longitude" => $this->input->post("longitude"),
		"jam_buka" => $this->input->post("jam_buka"),
		"jam_tutup" => $this->input->post("jam_tutup"),
		"nomor_handphone" => "0".$this->input->post("nomor_handphone"),
		"bergabung" => date('Y-m-d'),
		"foto" => "http://hovercout.net/assets/img/mitra/defaultmitra.jpg"
	];

	$this->db->where('email',$this->input->post('email'));
	$cek = $this->db->count_all_results('tb_mitra');

	$this->db->where('email',$this->input->post('email'));
	$cek2 = $this->db->count_all_results('tb_customer');

	if($cek > 0 || $cek2 > 0){
		$status = [
			"kode" => "10",
			"pesan" => "Email sudah terdaftar!"
		];
	}else{
		$this->db->insert("tb_mitra",$data);
		$status = [
			"kode" => "1",
			"pesan" => "Registrasi Berhasil! Silahkan Login."
		];
	}

	echo json_encode($status);
	}
}
