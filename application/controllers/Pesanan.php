<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pesanan extends CI_Controller {

	public function index()
	{
		//CRUD Customer
	}

	public function tambahPesanan(){

		$token = $this->input->post('token');

		$curl = curl_init();
		$authKey = "key=AAAAWm1YqKE:APA91bFSG5_X8w_jwtozk67MYdDVGR99jnjQ5CJVfviAotahclWeNKNEfzTxpnjhNP3EBpiGvNh9kMDUSQybLvKmwDWU1reTJxMPhf0WaXb_pleyZeDztwASQYNEJUdzhlkYHBLpHG2t";
		$registration_ids = '["'.$token.'"]';
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => '{
		                "registration_ids": ' . $registration_ids . ',
		                "notification": {
		                    "title": "Pesanan Masuk",
		                    "body": "Anda memiliki pesanan laundry baru."
		                }
		              }',
		  CURLOPT_HTTPHEADER => array(
		    "Authorization: " . $authKey,
		    "Content-Type: application/json",
		    "cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		//Pisah

		header('Content-Type: application/json');

		$data = [
			"id_customer" => $this->input->post("id_customer"),
			"id_mitra" => $this->input->post("id_mitra"),
			"harga" => $this->input->post("harga"),
			"ongkir" => $this->input->post("ongkir"),
			"totalHarga" => $this->input->post("totalHarga"),
			"tgl_pesanan" => date('Y-m-d H-i-s'),
			"status" => 1
		];

		$this->db->insert("tb_pesanan",$data);
		$insert_id = $this->db->insert_id();

		$arrayNamaItem = $this->input->post('idPesanan[]');
		$arrayJumlahItem = $this->input->post('jumlahItem[]');

		$i = 0;
		foreach($arrayNamaItem as $key) {
								$data2 = [
										'id_pesanan'   => $insert_id,
										'id_jenis_pakaian' => $arrayNamaItem[$i],
										'jumlah_pakaian' => $arrayJumlahItem[$i]
								];

		$this->db->insert("tb_pesanan_pakaian",$data2);
		$i++;
		};

		$status = [
			"kode" => "1",
			"pesan" => "Pesanan Berhasil Dilakukan!"
		];

		echo json_encode($status);
	}

  public function lihatPesananCustomer(){

    header('Content-Type: application/json');

		$id_customer = $this->input->post("id_customer");

    $this->db->select("tb_pesanan.*,tb_mitra.nama_mitra, tb_mitra.nomor_handphone");
    $this->db->from("tb_pesanan");
    $this->db->join('tb_mitra','tb_mitra.id = tb_pesanan.id_mitra');
		$this->db->order_by('tb_pesanan.id', 'DESC');
		$this->db->where("id_customer",$id_customer);

		$names = [1,2];
		$this->db->where_in('status', $names);
	  $data = $this->db->get()->result_array();

    $status = [
      "kode" => "1",
      "pesan" => "Data Berhasil Diambil",
      "listPesanan" => $data
    ];

    echo json_encode($status);
  }

	public function lihatHistoryCustomer(){

    header('Content-Type: application/json');

		$id_customer = $this->input->post("id_customer");

		$this->db->select("tb_pesanan.*,tb_mitra.nama_mitra");
    $this->db->from("tb_pesanan");
    $this->db->join('tb_mitra','tb_mitra.id = tb_pesanan.id_mitra');
		$this->db->join('tb_customer','tb_customer.id = tb_pesanan.id_customer');
		$this->db->where("id_customer",$id_customer);
		$this->db->order_by('id', 'DESC');

		$names = [3,4,5];
		$this->db->where_in('status', $names);

    $data = $this->db->get()->result_array();

    $status = [
      "kode" => "1",
      "pesan" => "Data Berhasil Diambil",
      "listPesanan" => $data
    ];

    echo json_encode($status);
  }

	public function hapusPesanan(){

		  header('Content-Type: application/json');

			$id = $this->input->post('id');

			$data = [
				"status" => '4'
			];

		$this->db->where('id',$id);
		$this->db->update("tb_pesanan",$data);

		$status = [
			"kode" => "1",
			"pesan" => "Pesanan telah dibatalkan."
		];

			echo json_encode($status);
	}

	public function lihatItem(){

			header('Content-Type: application/json');

			$id = $this->input->post('id');

			$this->db->select("tb_pesanan_pakaian.*, tb_jenis_pakaian.namapakaian");
			$this->db->from("tb_pesanan_pakaian");
			$this->db->join('tb_jenis_pakaian','tb_jenis_pakaian.id = tb_pesanan_pakaian.id_jenis_pakaian');
			$this->db->where('id_pesanan',$id);
			$data = $this->db->get()->result_array();

			$status = [
				"kode" => "1",
				"pesan" => "Data Berhasil Diambil",
				"listItem" => $data
			];

			echo json_encode($status);

	}

	public function lihatPesananMitra(){

			header('Content-Type: application/json');

			$id_mitra = $this->input->post('id_mitra');

			$this->db->select("tb_pesanan.*,tb_customer.nama_customer, tb_customer.alamat, tb_customer.latitude, tb_customer.longitude, tb_customer.token, tb_customer.nomor_handphone");
			$this->db->from("tb_pesanan");
			$this->db->join('tb_customer','tb_customer.id = tb_pesanan.id_customer');

			$this->db->order_by('tb_pesanan.id', 'DESC');
			$this->db->where('status',1);
			$this->db->where("id_mitra",$id_mitra);
			$data = $this->db->get()->result_array();

			$status = [
				"kode" => "1",
				"pesan" => "Data Berhasil Diambil",
				"listPesanan" => $data
			];

			echo json_encode($status);
		}

		public function searchCustomer(){

				header('Content-Type: application/json');

				$id_mitra = $this->input->post('id_mitra');
				$query = $this->input->post('query');

				$this->db->select("tb_pesanan.*,tb_customer.nama_customer, tb_customer.alamat, tb_customer.latitude, tb_customer.longitude");
				$this->db->from("tb_pesanan");
				$this->db->join('tb_customer','tb_customer.id = tb_pesanan.id_customer');

				$this->db->order_by('tb_pesanan.id', 'DESC');
				$this->db->where("id_mitra",$id_mitra);
				$this->db->like('tb_customer.nama_customer',$query);
				$this->db->where('status',2);
				$data = $this->db->get()->result_array();

				$status = [
					"kode" => "1",
					"pesan" => "Data Berhasil Diambil",
					"listPesanan" => $data
				];

				echo json_encode($status);
			}

		public function terimaPesanan(){

			$token = $this->input->post('token');

			$curl = curl_init();
			$authKey = "key=AAAAWm1YqKE:APA91bFSG5_X8w_jwtozk67MYdDVGR99jnjQ5CJVfviAotahclWeNKNEfzTxpnjhNP3EBpiGvNh9kMDUSQybLvKmwDWU1reTJxMPhf0WaXb_pleyZeDztwASQYNEJUdzhlkYHBLpHG2t";
			$registration_ids = '["'.$token.'"]';
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => '{
											"registration_ids": ' . $registration_ids . ',
											"notification": {
													"title": "Pesanan Diterima",
													"body": "Kami siap untuk mengambil pakaian anda."
											}
										}',
				CURLOPT_HTTPHEADER => array(
					"Authorization: " . $authKey,
					"Content-Type: application/json",
					"cache-control: no-cache"
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			//Pisah

			header('Content-Type: application/json');

			$id = $this->input->post('id');

			$data = [
				"status" => '2'
			];

		$this->db->where('id',$id);
		$this->db->update("tb_pesanan",$data);

			$status = [
				"kode" => "1",
				"pesan" => "Pesanan diterima."
			];

			echo json_encode($status);
		}

		public function tolakPesanan(){

			$token = $this->input->post('token');

			$curl = curl_init();
			$authKey = "key=AAAAWm1YqKE:APA91bFSG5_X8w_jwtozk67MYdDVGR99jnjQ5CJVfviAotahclWeNKNEfzTxpnjhNP3EBpiGvNh9kMDUSQybLvKmwDWU1reTJxMPhf0WaXb_pleyZeDztwASQYNEJUdzhlkYHBLpHG2t";
			$registration_ids = '["'.$token.'"]';
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => '{
											"registration_ids": ' . $registration_ids . ',
											"notification": {
													"title": "Pesanan Ditolak",
													"body": "Mohon maaf laundry kami telah menolak pesananmu."
											}
										}',
				CURLOPT_HTTPHEADER => array(
					"Authorization: " . $authKey,
					"Content-Type: application/json",
					"cache-control: no-cache"
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			//Pisah

			header('Content-Type: application/json');

			$id = $this->input->post('id');

			$data = [
				"status" => '3'
			];

		$this->db->where('id',$id);
		$this->db->update("tb_pesanan",$data);

			$status = [
				"kode" => "1",
				"pesan" => "Pesanan ditolak."
			];

			echo json_encode($status);
		}

		public function selesaiPesanan(){

			$token = $this->input->post('token');

			$curl = curl_init();
			$authKey = "key=AAAAWm1YqKE:APA91bFSG5_X8w_jwtozk67MYdDVGR99jnjQ5CJVfviAotahclWeNKNEfzTxpnjhNP3EBpiGvNh9kMDUSQybLvKmwDWU1reTJxMPhf0WaXb_pleyZeDztwASQYNEJUdzhlkYHBLpHG2t";
			$registration_ids = '["'.$token.'"]';
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => '{
											"registration_ids": ' . $registration_ids . ',
											"notification": {
													"title": "Pesanan Selesai",
													"body": "Hooray! pesanan anda sudah selesai."
											}
										}',
				CURLOPT_HTTPHEADER => array(
					"Authorization: " . $authKey,
					"Content-Type: application/json",
					"cache-control: no-cache"
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			//Pisah

			header('Content-Type: application/json');

			$id = $this->input->post('id');

			$data = [
				"status" => '5'
			];

		$this->db->where('id',$id);
		$this->db->update("tb_pesanan",$data);

			$status = [
				"kode" => "1",
				"pesan" => "Pesanan selesai."
			];

			echo json_encode($status);
		}

		public function lihatProsesMitra(){

				header('Content-Type: application/json');

				$id_mitra = $this->input->post('id_mitra');

				$this->db->select("tb_pesanan.*,tb_customer.nama_customer, tb_customer.alamat, tb_customer.latitude, tb_customer.longitude, tb_customer.token");
				$this->db->from("tb_pesanan");
				$this->db->join('tb_customer','tb_customer.id = tb_pesanan.id_customer');

				$this->db->order_by('tb_pesanan.id', 'DESC');
				$this->db->where('status',2);
				$this->db->where("id_mitra",$id_mitra);
				$data = $this->db->get()->result_array();

				$status = [
					"kode" => "1",
					"pesan" => "Data Berhasil Diambil",
					"listPesanan" => $data
				];

				echo json_encode($status);
			}

			public function lihatHistoryMitra(){

					header('Content-Type: application/json');

					$id_mitra = $this->input->post('id_mitra');

					$this->db->select("tb_pesanan.*,tb_customer.nama_customer, tb_customer.alamat, tb_customer.latitude, tb_customer.longitude");
					$this->db->from("tb_pesanan");
					$this->db->join('tb_customer','tb_customer.id = tb_pesanan.id_customer');

					$this->db->order_by('tb_pesanan.id', 'DESC');
					$this->db->where("id_mitra",$id_mitra);

					$names = [3,5];
					$this->db->where_in('status', $names);
					$data = $this->db->get()->result_array();

					$status = [
						"kode" => "1",
						"pesan" => "Data Berhasil Diambil",
						"listPesanan" => $data
					];

					echo json_encode($status);
				}
}
