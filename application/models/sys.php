<?php

class sys extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
    
	// 获取当前系统设置
	function get_config($getdata) {
		foreach ($getdata as $item) {
			$this->db->select('para');
			$this->db->from('config');
			$this->db->where('item', $item);
		
			$this->db->trans_start(); //事务开始
			$query = $this->db->get();
			$this->db->trans_complete(); //事务结束
			
			if ($query->num_rows() > 0) {
				$row = $query->first_row('array');
				$result['config'][$item] = $row['para'];
			}
		}
		return $result;
	}
	
	// 获取通知信息
	function get_notice($row) {
		$this->db->select();
		$this->db->from('notice');
		$this->db->order_by('id');
		$this->db->limit($row);
		
		$this->db->trans_start(); //事务开始
		$query = $this->db->get();
		$this->db->trans_complete(); //事务结束
		
		if ($query->num_rows() > 0) {
			$result['notice']['num'] = $query->num_rows();
			$result['notice']['instance'] = $query->result_array();
		}
		else
			$result = array();
		
		return $result;
	}
	
	// 更新系统设置
	function update_config($getdata) { //修改设置
		$this->load->model('user');
		
		if ($this->user->is_login() && $this->user->is_admin()) {
			$username = $this->user->get_login(); //管理员验证
			$data = array(
				'username' => $username['id'],
				'pwd' => $getdata['adminpwd'],
			);
			
			$result = $this->user->authorize($data); //检测管理员密码是否正确
			if (!$result['message_type']) {
			
				$this->db->trans_start(); //事务开始
				
				foreach ($getdata['config'] as $key => $value) {
					$this->db->where('item', $key);
					$data = array(
						'para' => $value,
					);
					$this->db->update('config', $data);
				}
				
				$this->db->trans_complete(); //事务结束
				
				$result['message_type'] = 1;
			}
			else
				$result['message_type'] = 3;
			
			return $result;
		}
	}
	
	// 更新通知信息
	function update_notice($getdata) { //修改设置
		$this->load->model('user');
		
		if ($this->user->is_login() && $this->user->is_admin()) {
			$username = $this->user->get_login(); //管理员验证
			$data = array(
				'username' => $username['id'],
				'pwd' => $getdata['adminpwd'],
			);
			
			$result = $this->user->authorize($data); //检测管理员密码是否正确
			if (!$result['message_type']) {
				$this->db->trans_start(); //事务开始
				
				$instance = $getdata['notice']['instance'];
				foreach ($instance as $item) {
					$this->db->where('id', $item['id']);
					$data = array(
						'title' => $item['title'],
						'content' => $item['content'],
					);
					$this->db->update('notice', $data);
				}
				
				$this->db->trans_complete(); //事务结束
				
				$result['message_type'] = 1;
			}
			else
				$result['message_type'] = 3;
			
			return $result;
		}
	}
	
	// 获取热门借阅
	function get_hot() {
		$sql = 'SELECT num, book.bno, title, category, press, year, author, price, stock
				FROM (SELECT COUNT(*) AS num, bno
				FROM borrow
				GROUP BY bno
				ORDER BY num DESC) AS temp
				INNER JOIN book
				ON book.bno = temp.bno
				ORDER BY temp.num DESC
				LIMIT 0, 10';
		
		$query = $this->db->query($sql);
		
		$result['hot'] = $query->result_array();
		return $result;
	}
	
}