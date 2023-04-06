<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{

		header('Content-Type: application/json');

		$email = $this->input->post("email");
		$password = $this->input->post("password");

		$this->db->select("*");
		$this->db->from("tb_customer");
		$this->db->where("email",$email);
		$data = $this->db->get()->result_array();

		$this->db->select("*");
		$this->db->from("tb_mitra");
		$this->db->where("email",$email);
		$data2 = $this->db->get()->result_array();

		$token = $this->input->post('token');

		if($data!=null || $data2!=null){
			if($data!=null){
				if(password_verify($password, $data[0]['password'])){

					$data5 = [
						"token" => $token
					];

					$this->db->where('id', $data[0]['id']);
					$this->db->update('tb_customer',$data5);

				$status = [
					"kode" => "1",
					"pesan" => "Email Ada dan Password Benar",
					"listCustomer" => $data
				];

				}else{

					$status = [
						"kode" => "2",
						"pesan" => "Password salah!"
					];

				}
			}elseif($data2!=null){
				if(password_verify($password, $data2[0]['password'])){

					$data5 = [
						"token" => $token
					];

					$this->db->where('id', $data2[0]['id']);
					$this->db->update('tb_mitra',$data5);

				$status = [
					"kode" => "4",
					"pesan" => "Email Ada dan Password Benar",
					"listMitra" => $data2
				];
				}else{

					$status = [
						"kode" => "2",
						"pesan" => "Password salah!"
					];

				}
			}
		}else{

			$status = [
				"kode" => "3",
				"pesan" => "Email tidak terdaftar!"
			];

		}
		echo json_encode($status);
	}

	public function logoutCustomer(){

			$id = $this->input->post('id');

			$data = [
				"token" => "0"
			];

			$this->db->where('id',$id);
			$this->db->update('tb_customer',$data);

			$status = [
				"kode" => "1",
				"pesan" => "Anda berhasil Logout!"
			];

		echo json_encode($status);

	}

	public function logoutMitra(){

			$id = $this->input->post('id');

			$data = [
				"token" => "0"
			];

			$this->db->where('id',$id);
			$this->db->update('tb_mitra',$data);

			$status = [
				"kode" => "1",
				"pesan" => "Anda berhasil Logout!"
			];


		echo json_encode($status);

	}
}
