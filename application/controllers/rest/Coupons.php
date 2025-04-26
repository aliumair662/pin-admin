<?php
require_once(APPPATH . 'libraries/REST_Controller.php');

/**
 * REST API for About
 */
class Coupons extends API_Controller
{
	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		// call the parent
		parent::__construct('Coupon');
	}

	public function create_coupon_post()
	{
		$rules = array(
			array(
				'field' => 'item_id',
				'rules' => 'required'
			),
			array(
				'field' => 'description',
				'rules' => 'required'
			),
			array(
				'field' => 'max_coupon_per_user',
				'rules' => 'required'
			),
			array(
				'field' => 'users_required',
				'rules' => 'required'
			),
		);
		if (!$this->is_valid($rules)) exit;
		$this->load->database();
		$item_id = $this->post('item_id');
		$description = $this->post('description');
		$max_coupon = $this->post('max_coupon_per_user');
		$required_users = $this->post('users_required');
		$id = 'coupon_' . uniqid();
		$data = array(
			'coupon_id' => $id,
			'item_id' => $item_id,
			'description' => $description,
			'max_coupons_per_user' => $max_coupon,
			'users_required' => $required_users
		);
		$this->db->insert('bs_coupons', $data);
		if ($this->db->affected_rows() > 0) {
			$response = array(
				'success' => true,
				'message' => 'Coupons have been added successfully',
				'data' => $data
			);
		} else {
			$response = array(
				'success' => false,
				'message' => 'Error',
			);
		}
		return $this->response($response);
	}
	public function claim_coupon_get()
	{
		$rules = array(
			array(
				'field' => 'coupon_id',
				'rules' => 'required'
			),
			array(
				'field' => 'user_id',
				'rules' => 'required'
			),
		);
		if (!$this->is_valid($rules)) exit;
		$user_id = $this->get('user_id');
		$coupon_id = $this->get('coupon_id');

		$coupon = $this->db->get_where('bs_coupons', array('coupon_id' => $coupon_id))->row_array();

		if (!$coupon) {
			$response = array(
				'success' => false,
				'message' => 'Coupon does not exist',
				'data' => null
			);
			return $this->response($response);
		}

		$claimed_coupons = $this->db->get_where('bs_coupon_codes', array('coupon_id' => $coupon_id, 'user_id' => $user_id))->num_rows();

		if ($claimed_coupons >= $coupon['max_coupons_per_user']) {
			$response = array(
				'success' => false,
				'message' => 'You have already claimed the maximum number of coupons for this offer',
				'data' => $coupon
			);
			return $this->response($response);
		}

		$existing_coupon = $this->db->get_where('bs_coupon_codes', array('coupon_id' => $coupon_id, 'user_id' => $user_id))->row_array();

		if ($existing_coupon) {
			$response = array(
				'success' => false,
				'message' => 'You have already claimed this coupon',
				'data' => $existing_coupon
			);
			return $this->response($response);
		}

		$code = bin2hex(random_bytes(9));
		$id = 'code_' . uniqid();
		$data = array(
			'code_id' => $id,
			'coupon_id' => $coupon_id,
			'user_id' => $user_id,
			'code' => $code,
		);
		$this->db->insert('bs_coupon_codes', $data);
		if ($this->db->affected_rows() > 0) {
			$response = array(
				'success' => true,
				'message' => 'Coupon was successfully claimed',
				'data' => $data
			);
		} else {
			$response = array(
				'success' => false,
				'message' => 'Error',
			);
		}
		return $this->response($response);
	}

	public function generate_qrcode_get()
	{
		$rules = array(
			array(
				'field' => 'code',
				'rules' => 'required'
			),
		);
		if (!$this->is_valid($rules)) exit;

		$code = $this->get('code');

		$size = 100;

		$url = 'https://chart.googleapis.com/chart?cht=qr&chs=' . $size . 'x' . $size . '&chl=' . urlencode($code);
		$qrCode = file_get_contents($url);

		$folderName = 'qr_images';
		$filePath = FCPATH . $folderName . DIRECTORY_SEPARATOR . $code . '.png';
		file_put_contents($filePath, $qrCode);


		$response = array(
			'success' => true,
			'message' => 'QR code generated successfully.',
			'data' => array(
				'file_path' => base_url('qr_images/' . $code . '.png')
			)
		);

		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode($response));
	}

	public function consume_coupon_get()
	{
		$rules = array(
			array(
				'field' => 'code',
				'rules' => 'required'
			),
		);
		if (!$this->is_valid($rules)) exit;
		$code = $this->get('code');
		$this->load->database();
		$consume = $this->db->get_where('bs_coupon_codes', array('code' => $code))->row();
		if (!$consume) {
			$response = array(
				'success' => false,
				'message' => 'Coupon code not found',
			);
			return $this->response($response);
		}
		$data = array(
			'is_consumed' => 1,
		);
		$this->db->where('code', $code);
		$this->db->update('bs_coupon_codes', $data);
		$consumed = $this->db->get_where('bs_coupon_codes', array('code' => $code))->row();

		$response = array(
			'success' => true,
			'message' => 'Code Consume Successfully against that user',
			'data' => $consumed,
		);
		return $this->response($response);
	}

	public function coupons_for_item_get()
	{
		$rules = array(
			array(
				'field' => 'item_id',
				'rules' => 'required'
			),
		);
		if (!$this->is_valid($rules)) exit;

		try {
			$item_id = $this->get('item_id');
			$this->db->select('cc.code_id, cc.code, cc.user_id, cc.is_consumed, bc.coupon_id, bc.description, bc.users_required');
			$this->db->select('code');
			$this->db->from('bs_coupons bc');
			$this->db->join('bs_coupon_codes cc', 'cc.coupon_id = bc.coupon_id', 'LEFT');
			$this->db->where('bc.item_id', $item_id);
			$query = $this->db->get();
			$result = $query->result_array();
			$response = ['success' => true, 'data' => $result];
		} catch (Exception $e) {
			$response = ['success' => false, 'message' => $e->getMessage()];
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function getcoupons_get()
	{
		try {
			$this->load->database();
			$this->db->select('*');
			$this->db->from('bs_coupons');
			$query = $this->db->get();
			$result = $query->result_array();
			$response = ['success' => true, 'data' => $result];
		} catch (Exception $e) {
			$response = ['success' => false, 'message' => $e->getMessage()];
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}

	public function coupons_for_user_get()
	{
		$rules = array(
			array(
				'field' => 'user_id',
				'rules' => 'required'
			),
		);
		if (!$this->is_valid($rules)) exit;

		try {
			$user_id = $this->get('user_id');
			$this->db->select('cc.code_id, cc.code, cc.is_consumed, bc.coupon_id, bc.description,bc.item_id, bc.max_coupons_per_user');
			$this->db->from('bs_coupon_codes cc');
			$this->db->join('bs_coupons bc', 'cc.coupon_id = bc.coupon_id');
			$this->db->where('cc.user_id', $user_id);
			$query = $this->db->get();
			$result = $query->result_array();
			$response = ['success' => true, 'data' => $result];
		} catch (Exception $e) {
			$response = ['success' => false, 'message' => $e->getMessage()];
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	public function item_onlycoupon_get()
	{
		$rules = array(
			array(
				'field' => 'item_id',
				'rules' => 'required'
			),
		);
		if (!$this->is_valid($rules)) exit;
		try {
			$item_id = $this->get('item_id');
			$this->db->from('bs_coupons bc');
			$this->db->where('bc.item_id', $item_id);
			$query = $this->db->get();
			$result = $query->result_array();
			$response = ['success' => true, 'data' => $result];
		} catch (Exception $e) {
			$response = ['success' => false, 'message' => $e->getMessage()];
		}
		header('Content-Type: application/json');
		echo json_encode($response);
	}
}
