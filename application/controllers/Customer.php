<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer extends CI_Controller {

	public function index()
	{
		//CRUD Customer
	}

	public function tambahCustomer(){

		header('Content-Type: application/json');

		$data = [
			"nama_customer" => $this->input->post("nama_customer"),
			"email" => $this->input->post("email"),
			"password" => password_hash($this->input->post("password"), PASSWORD_DEFAULT),
			"alamat" => $this->input->post("alamat"),
			"latitude" => $this->input->post("latitude"),
			"longitude" => $this->input->post("longitude"),
			"nomor_handphone" => "0".$this->input->post("nomor_handphone"),
			"bergabung" => date('Y-m-d')
		];

		$this->db->where('email',$this->input->post('email'));
		$cek = $this->db->count_all_results('tb_customer');

		$this->db->where('email',$this->input->post('email'));
		$cek2 = $this->db->count_all_results('tb_mitra');

		if($cek > 0 || $cek2 > 0){
			$status = [
				"kode" => "10",
				"pesan" => "Email sudah terdaftar!"
			];
		}else{
			$this->db->insert("tb_customer",$data);

			$status = [
				"kode" => "1",
				"pesan" => "Registrasi Berhasil! Silahkan Login."
			];
		}

		echo json_encode($status);
	}

	public function ubahCustomer(){

		header('Content-Type: application/json');

		$id = $this->input->post('id');

		$data = [
			"nama_customer" => $this->input->post("nama_customer"),
			"email" => $this->input->post("email"),
			"alamat" => $this->input->post("alamat"),
			"latitude" => $this->input->post("latitude"),
			"longitude" => $this->input->post("longitude"),
			"nomor_handphone" => "0".$this->input->post("nomor_handphone")
		];

	$this->db->where('id',$id);
	$this->db->update("tb_customer",$data);

		$status = [
			"kode" => "1",
			"pesan" => "Perubahan Berhasil Dilakukan. Silahkan login kembali!"
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
	$this->db->update("tb_customer",$data);

		$status = [
			"kode" => "1",
			"pesan" => "Perubahan Berhasil Dilakukan. Silahkan login kembali!"
		];

		echo json_encode($status);
	}
}
