<?php

class pages extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		$this->load->model('user');
		$this->load->model('book');
		$this->load->model('sys');
	}
	
	// 页面生成函数
	function gen_page($page, $data = array(), $header_data = array(), $footer_data = array()) {
		$header_data['CSS_PATH'] = base_url("css").'/';
		$header_data['JS_PATH'] = base_url("js").'/';
		
		switch ($page) {
			case 'login':
			case 'register':
			case 'panel':
				$header_data['nav_current'] = 'home';
				break;
			default:
				$header_data['nav_current'] = $page;
		}
		
		$path = 'pages/'.$page;
		$this->load->view('templates/header', $header_data);
		$this->load->view($path, $data);
		$this->load->view('templates/footer');
	}
	
	// 路径导向
	public function view($page = "home") {
		$main_data = array();
		$header_data = array();
		$footer_data = array();
		
		// 设置header信息
		if ($this->user->is_login()) {
			$header_data = array_merge($header_data, $this->user->get_login());
			
			if ($this->user->is_admin()) {
				$header_data = array_merge($header_data, array('admin' => 1));
			}
		}
		
		// 设置body信息
		switch ($page) {
			case "login":
				if ($this->user->is_login())
					redirect('pages/view/home');
				
				if ($this->input->post('indirect') == 1) {
					$data = array (
						'username' => $this->input->post('username'),
						'pwd' => $this->input->post('pwd'),
					);
					
					$main_data = array_merge($main_data, $this->user->login($data));
					if ($main_data['message_type'] == 0) //登录成功跳转
						redirect('pages/view/home');
				}
				break;
			
			case 'logout':
				if ($this->user->is_login())
					$this->user->logout();
				redirect('pages/view/home');
				break;
				
			case "register":
				if ($this->user->is_login())
					redirect('pages/view/home');
				
				if ($this->input->post('indirect') == 1) {
					$data = array (
						'email' => $this->input->post('email'),
						'pwd1' => $this->input->post('pwd1'),
						'pwd2' => $this->input->post('pwd2'),
					);
					
					$main_data = array_merge($main_data, $this->user->register($data));
					if ($main_data['message_type'] == 0) { //注册成功
						$page = 'login';
						$main_data['message_type'] = 1;
					}
				}
				break;
				
			case "search":
				if ($this->input->post('indirect') == 1) {
					$data = array (
						//'bno' => $this->input->post('bno'),
						'title' => $this->input->post('title'),
						'author' => $this->input->post('author'),
						'category' => $this->input->post('category'),
						'press' => $this->input->post('press'),
						'year_min' => $this->input->post('year_min'),
						'year_max' => $this->input->post('year_max'),
						'price_min' => $this->input->post('price_min'),
						'price_max' => $this->input->post('price_max'),
					);
					$form_data = $data;
					$main_data = array_merge($main_data, $this->book->search($data, 1));
					$main_data['form'] = $form_data;
				}
				$data_list['author'] = $this->book->get_list('author');
				$data_list['category'] = $this->book->get_list('category');
				$data_list['press'] = $this->book->get_list('press');
				$main_data['list'] = $data_list;
				break;
				
			case "panel":
				if (! $this->user->is_login())
					redirect('pages/view/home');
					
				if (!$this->user->is_admin()) { // 判断是否为管理员
					$data = $this->user->get_login();
					$main_data = array_merge($main_data, $this->book->search_borrow($data));
				}
				else {
				$main_data['admin'] = 1;
				
				if ($this->input->post('tab_current')) { // 有提交信息
					$main_data['tab_current'] = $this->input->post('tab_current');
					
					switch ($this->input->post('tab_current')) {
						case 'update_book':
							$upload_type = $this->input->post('upload_type');
							if ($upload_type == 0) { //添加一本书
								$data = array (
									'bno' => $this->input->post('bno'),
									'title' => $this->input->post('title'),
									'category' => $this->input->post('category'),
									'year' => $this->input->post('year'),
									'author' => $this->input->post('author'),
									'press' => $this->input->post('press'),
									'price' => $this->input->post('price'),
									'total' => $this->input->post('total'),
								);
								
								$result = $this->book->update_book($data);
								$main_data = array_merge($main_data, $result);
							}
							else if ($upload_type == 1){ //添加多本书
								$config['upload_path'] = './uploads/';
								$config['allowed_types'] = 'csv';
								$config['max_size'] = '100';
								$config['overwite'] = FALSE;
								$config['encrypt_name'] = TRUE;
								$config['remove_spaces'] = TRUE;
								$this->load->library('upload', $config);
								
								if ( ! $this->upload->do_upload()) {
									$result['message']['file_error'] = $this->upload->display_errors('', '');
									$result['message_type'] = 3;
								}
								else {
									$data = $this->upload->data();
									$result = $this->book->update_books($data);
								}
								$main_data = array_merge($main_data, $result);
							}
							break;
						
						case 'update_user':
							$data = array (
								'email' => $this->input->post('email'),
								'pwd1' => $this->input->post('pwd1'),
								'pwd2' => $this->input->post('pwd2'),
								'adminpwd' => $this->input->post('adminpwd'),
							);
							
							$result = $this->user->update_user($data);
							$main_data = array_merge($main_data, $result);
							break;
						
						case 'update_admin':
							$data = array (
								'username' => $this->input->post('username'),
								'adminpwd' => $this->input->post('adminpwd'),
							);
							
							$result = $this->user->update_admin($data);
							$main_data = array_merge($main_data, $result);
							break;
							
						case 'update_config':
							switch ($this->input->post('config')) {
								case 'config':
									$data = array (
										'config' => array(
											'max_borrow_books' => $this->input->post('max_borrow_books'),
											'max_borrow_days' => $this->input->post('max_borrow_days'),
										),
										'adminpwd' => $this->input->post('adminpwd'),
									);
									
									$result = $this->sys->update_config($data);
									$main_data = array_merge($main_data, $result);
									break;
								
								case 'notice':
									$data['notice']['num'] = $this->input->post('num');
									for ($i = 0; $i < $data['notice']['num']; $i++) {
										$new = array(
												'id' => $this->input->post('id_'.$i),
												'title' => $this->input->post('title_'.$i),
												'content' => $this->input->post('content_'.$i),
											);
										$data['notice']['instance'][$i] = $new;
									}
									$data['adminpwd'] = $this->input->post('adminpwd');
									
									$result = $this->sys->update_notice($data);
									$main_data = array_merge($main_data, $result);
									break;
							}
							break;
					}
				}
				
				// 获取配置信息
				$data = array(
					'max_borrow_books', 'max_borrow_days'
				);
				$result = $this->sys->get_config($data);
				$main_data = array_merge($main_data, $result);
				
				// 获取首页通知
				$result = $this->sys->get_notice(3);
				$main_data = array_merge($main_data, $result);
				}
				
				break;
			
			case "service":
				if (!$this->user->is_login() || !$this->user->is_admin())
					redirect('pages/view/home');
				
				switch ($this->input->post('step')) {
					case 1:
						$data = array (
							'id' => $this->input->post('id'),
						);
						$main_data = array_merge($main_data, $this->book->search_borrow($data));
						break;
						
					case 2:
						$data = array(
							'id' => $this->input->post('id'),
							'bno' => $this->input->post('bno'),
							'return_executed' => $this->session->userdata('id'),
						);
						
						$main_data = array_merge($main_data, $this->book->set_returned($data));
						
						$data = array (
							'id' => $this->input->post('id'),
						);
						$main_data = array_merge($main_data, $this->book->search_borrow($data));
						break;
						
					case 3:
						$data = array(
							'id' => $this->input->post('id'),
							'bno' => $this->input->post('bno'),
							'borrow_executed' => $this->session->userdata('id'),
						);
						
						$main_data = array_merge($main_data, $this->book->borrow($data));
					
						$data = array (
							'id' => $this->input->post('id'),
						);
						$main_data = array_merge($main_data, $this->book->search_borrow($data));
						break;
				}
				break;
			
			default:
				// 获取首页通知
				$result = $this->sys->get_notice(3);
				$main_data = array_merge($main_data, $result);
				
				// 获取热门借阅
				$result = $this->sys->get_hot();
				$main_data = array_merge($main_data, $result);
				
				$page = 'home';
		}
		
		// 生成页面
		$this->gen_page($page, $main_data, $header_data, $footer_data);
	}
}
