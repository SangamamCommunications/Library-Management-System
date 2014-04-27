<?php

class user extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
	// 判断用户是否存在
	function is_exsist($getdata) {
		$this->db->trans_start(); //事务开始
		
		$id = $getdata['id'];
		$this->db->select();
		$this->db->from('user');
		$this->db->where('id', $id);
		
		$query = $this->db->get();
		
		$this->db->trans_complete(); //事务结束
		
		if ($id && $query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	// 判断用户是否为管理员
	function is_admin() {
		$this->db->trans_start(); //事务开始
		
		$id = $this->session->userdata('id');
		$this->db->select();
		$this->db->from('admin');
		$this->db->where('id', $id);
		
		$query = $this->db->get();
		
		$this->db->trans_complete(); //事务结束
		
		if ($id && $query->num_rows() > 0)
			return TRUE;
		else
			return FALSE;
	}
	
	// 判断用户是否登录
	function is_login() {
		$id = $this->session->userdata('id');
		$email = $this->session->userdata('email');
		if ($id && $email)
			return TRUE;
		else
			return FALSE;
	}
	
	// 从cookie中获取用户登录信息
	function get_login() {
		$data = array(
			'id' => $this->session->userdata('id'),
			'email' => $this->session->userdata('email'),
		);
		return $data;
	}
	
	// cookie中设置用户已登录
	function set_login($getdata) {
		$this->session->set_userdata($getdata);
	}
	
	// 添加用户
	function add_user($email, $pwd) {
		$data = array(
               'email' => $email,
               'pwd' => $pwd,
            );
		
		$this->db->trans_start(); //事务开始
		$this->db->insert('user', $data);
		$this->db->trans_complete(); //事务结束
		
		$this->db->trans_start(); //事务开始
		
		$this->db->select('id');
		$this->db->from('user');
		$this->db->where('email', $email);
		
		$query = $this->db->get();
		
		$this->db->trans_complete(); //事务结束
		
		$row = $query->first_row('array');
		$data['id'] = $row['id'];
		return $data;
	}
	
	// 增加管理员
	function add_admin($getdata) {		
		$data = array(
               'id' => $getdata['id'],
            );
		
		$this->db->trans_start(); //事务开始
		$this->db->insert('admin', $data);
		$this->db->trans_complete(); //事务结束
	}
	
	// 管理员添加用户
	function update_user($getdata) {
		if ($this->is_login() && $this->is_admin()) {
			$username = $this->get_login(); //管理员验证
			$data = array(
				'username' => $username['id'],
				'pwd' => $getdata['adminpwd'],
			);
			
			$result = $this->authorize($data); //检测管理员密码是否正确
			if (!$result['message_type']) {
				$result = $this->register($getdata);
			}
			else {
				$result['message_type'] = 5;
			}
			return $result;
		}
	}
	
	// 管理员添加其他管理员
	function update_admin($getdata) { 
		if ($this->is_login() && $this->is_admin()) {
			$username = $this->get_login(); //管理员验证
			$data = array(
				'username' => $username['id'],
				'pwd' => $getdata['adminpwd'],
			);
			
			$result = $this->authorize($data); //检测管理员密码是否正确
			if (!$result['message_type']) {
				$this->db->trans_start(); //事务开始
				
				$this->db->select('id, email');
				$this->db->from('user');
				$this->db->where('id', $getdata['username']);
				$this->db->or_where('email', $getdata['username']); 
				
				$query = $this->db->get();
				
				$this->db->trans_complete(); //事务结束
				
				if ($query->num_rows() > 0) { //检测是否存在此用户
					$data = $query->first_row('array');
					
					$this->db->trans_start(); //事务开始
					
					$this->db->select();
					$this->db->from('admin');
					$this->db->where('id', $data['id']);

					$query = $this->db->get();
					
					$this->db->trans_complete(); //事务结束
					
					if ($query->num_rows() > 0) { 
					}
					else
						$this->add_admin($data);
					
					$result['message_type'] = 1;
					$result['message'] = $data;
				}
				else
					$result['message_type'] = 2;
			}
			else {
				$result['message_type'] = 3;
			}
			return $result;
		}
	}
	
	// 验证用户信息
	function authorize($getdata) {
		$username = $getdata['username'];
		$pwd = $getdata['pwd'];
		
		if ($username && $pwd) {
			$this->db->trans_start(); //事务开始
			
			$this->db->select('id, email');
			$this->db->from('user');
			$this->db->where('id', $username);
			$this->db->where('pwd', $pwd);
			$this->db->or_where('email', $username); 
			$this->db->where('pwd', $pwd);
			
			$query = $this->db->get();
			
			$this->db->trans_complete(); //事务结束
			
			if ($query->num_rows() > 0) {
				$data['message_type'] = 0;
				$data['message'] = $query->first_row('array');
			}
			else
				$data['message_type'] = 3;
		}
		else
			$data['message_type'] = 2;
		return $data;
	}
	
	// 登录
	function login($getdata) {
		$result = $this->authorize($getdata);
		
		if (!$result['message_type']) {
			$data['message_type'] = 0;
			$this->set_login($result['message']);
		}
		else {
			$data['message_type'] = $result['message_type'];
		}
		return $data;
	}
	
	// 退出，清除cookie
	function logout() {
		$this->session->unset_userdata('id');
		$this->session->unset_userdata('email');
	}
	
	// 用户注册
	function register($getdata) {
		$email = $getdata['email'];
		$pwd1 = $getdata['pwd1'];
		$pwd2 = $getdata['pwd2'];
		
		if ($email && $pwd1 && $pwd2) {
			$this->load->helper('email');
			if (!valid_email($email))
				$data['message_type'] = 2;
			else
				if ($pwd1 != $pwd2)
					$data['message_type'] = 3;
				else {
					$this->db->trans_start(); //事务开始
					
					$this->db->select('email');
					$this->db->from('user');
					$this->db->where('email', $email);
					
					$query = $this->db->get();
					
					$this->db->trans_complete(); //事务结束
					
					if ($query->num_rows() > 0)
						$data['message_type'] = 4;
					else
						$data['message_type'] = 0;
						$data['message'] = $this->add_user($email, $pwd1);
				}
		}
		else
			$data['message_type'] = 1;
		return $data;
	}
	
}