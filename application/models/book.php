<?php

class book extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }
	
	// 判断图书ISBN的位数，11位或13位
	function isbn_sum($isbn, $len) {
		$sum = 0;
		if ($len == 10) {
			for ($i = 0; $i < $len-1; $i++) {
				$sum = $sum + (int)$isbn[$i] * ($len - $i);
			}
		}
		elseif ($len == 13) {
			for ($i = 0; $i < $len-1; $i++) {
				if ($i % 2 == 0)
					$sum = $sum + (int)$isbn[$i];
				else
					$sum = $sum + (int)$isbn[$i] * 3;
			}
		}
		return $sum;
	}
	
	// 计算图书的ISBN
	function isbn_compute($isbn, $len) {
		if ($len == 10) {
			$digit = 11 - $this->isbn_sum($isbn, $len) % 11;
			
		if ($digit == 10)
			$rc = 'X';
		else if ($digit == 11)
			$rc = '0';
		else
			$rc = (string)$digit;
		}
		else if($len == 13) {
			$digit = 10 - isbn_sum($isbn, $len) % 10;
			if ($digit == 10)
				$rc = '0';
			else
				$rc = (string)$digit;
		}
		return $rc;
	}

	// 判断图书的ISBN是否合法
	function is_isbn($isbn) {
		if (is_string($isbn) && stripos($isbn, '.') == FALSE) //仅仅只检测是否为数字
			return 1;
		else
			return 0;
		
		$len = strlen($isbn);
		if ($len!=10 && $len!=13) return 0;
		$rc = $this->isbn_compute($isbn, $len);
		if ($isbn[$len-1] != $rc)   /* ISBN尾数与计算出来的校验码不符 */
			return 0;
		else
			return 1;
	}
	
	// 判断字符串是否为UTF-8编码
	function is_utf8($string) {
		return preg_match('%^(?:
          [\x09\x0A\x0D\x20-\x7E]            # ASCII
        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
		)*$%xs', $string);
	}
	
	// 判断图书是否已经存在
	function is_book_exist($getdata) {
		// -1 表示不存在
		// 非负值 表示图书库存量
		if ($getdata['bno']) {
			$this->db->select('stock');
			$this->db->from('book');
			$this->db->where('bno', $getdata['bno']);
		
			$this->db->trans_start(); //事务开始
			$query = $this->db->get();
			$this->db->trans_complete(); //事务结束
		
			if ($query->num_rows() > 0) {
				$row = $query->first_row('array');
				return $row['stock'];
			}
			else
				return -1;
		}
		else
			return -1;
	}
	
	// 判断图书是否已经归还
	function is_returned($getdata) {
		// -1 表示不存在
		if ($getdata['id'] && $getdata['bno']) {
			$this->db->trans_start(); //事务开始
			
			$this->db->select('returned');
			$this->db->from('borrow');
			$this->db->where('id', $getdata['id']);
			$this->db->where('bno', $getdata['bno']);
		
			$query = $this->db->get();
			
			$this->db->trans_complete(); //事务结束
			
			$ok = 1;
			foreach ($query->result_array() as $row)
				if ($row['returned'] == 0) {
					$ok = 0;
					break;
				}
			return $ok;
		}
		else
			return -1;
	}
	
	// 获取搜索功能中需要的下拉框的信息
	function get_list($listname) {
		$this->db->trans_start(); //事务开始
		
		$this->db->select($listname);
		$this->db->distinct();
		$this->db->from('book');
		$this->db->order_by($listname, "asc");
		
		$query = $this->db->get();
		
		$this->db->trans_complete(); //事务结束
		
		$data = array();
		foreach ($query->result_array() as $row) {
			array_push($data, $row[$listname]);
		}
		return $data;
	}
	
	// 获取某次借书记录的归还日期
	function get_return_date($getdata) {
		$this->db->trans_start(); //事务开始
		
		$this->db->select('return_date');
		$this->db->from('borrow');
		$this->db->where('bno', $getdata['bno']);
		$this->db->order_by('return_date desc');
		$this->db->limit(1);
		
		$query = $this->db->get();
		
		$this->db->trans_complete(); //事务结束
		
		$row = $query->first_row('array');
		$result = $row['return_date'];
		return $result;
	}
	
	// 增加一本书（纯insert操作）
	function add_book($getdata) {
		$this->db->trans_start(); //事务开始
		$this->db->insert('book', $getdata);
		$this->db->trans_complete(); //事务结束		
	}
	
	// 添加一本书
	function update_book($getdata) {
		$bno = trim($getdata['bno']);
		$title = trim($getdata['title']);
		$author = trim($getdata['author']);
		$category = trim($getdata['category']);
		$press = trim($getdata['press']);
		$year = trim($getdata['year']);
		$price = trim($getdata['price']);
		$total = trim($getdata['total']);
		$stock = trim($getdata['total']);
		
		if ($bno && $title && $author && $category && $press && $year && $price && $total && $stock) {
			$ok = FALSE;
			
			// 判断是否符合图书信息规范
			if ($this->is_isbn($bno) &&
				is_numeric($year) && stripos($year, '.') == FALSE &&
				is_numeric($price) &&
				is_numeric($total) && stripos($total, '.') == FALSE &&
				is_numeric($stock) && stripos($stock, '.') == FALSE) {
				
				$data = array(
					'bno' => $bno,
					'title' => $title,
					'author' => $author,
					'category' => $category,
					'press' => $press,
					'year' => $year,
					'price' => $price,
					'total' => $total,
					'stock' => $stock,
				);
				
				if ($this->is_book_exist($data) == -1) { //不存在此书
					$this->add_book($data);
					$ok = TRUE;
				}
				else { //已存在此书，返回书号
					$item = array(
						'type' => 1,
						'bno' => $bno,
						'title' => $title,
					);
				}
			}
			else // 不符合规范
				$item = array(
					'type' => 0,
					'bno' => $bno,
					'title' => $title,
				);
			
			if (isset($item))
				$result['fail_instance'][0] = $item;
			
			$result['message_type'] = 1;
			$result['message'] = array(
				'success' => $ok ? 1 : 0,
				'fail' => $ok ? 0 : 1,
				'total' => 1,
			);
		}
		else
			$result['message_type'] = 2;
			
		return $result;
	}
	
	// 添加多本书
	function update_books($getdata) {
		$params = array();
		$filename = $getdata['full_path'];
		$file = fopen($filename, 'r+');
		
		$count = 0;
		$success = 0;
		$error = 0;
		
		while(!feof($file)) {
			$data = fgetcsv($file); // 从csv文件中读取信息
			if (count($data) == 8) {
				if ($data[0] == 'bno' && $data[7] == 'total') continue;
				$count++;
				
				for ($i = 0; $i < 8; $i++) //编码转换
					if ($this->is_utf8($data[$i]) != 1)
						$data[$i] = mb_convert_encoding($data[$i], "UTF-8", "GBK");
				
				$data = array(
					'bno' => $data[0],
					'title' => $data[1],
					'author' => $data[2],
					'category' => $data[3],
					'press' => $data[4],
					'year' => $data[5],
					'price' => $data[6],
					'total' => $data[7],
					'stock' => $data[7],
				);
				
				$temp = $this->update_book($data); //添加一本书
				
				if ($temp['message_type'] == 1 && $temp['message']['success'] == 1)
					$success++;
				else {
					if (isset($temp['fail_instance']))
						$result['fail_instance'][$error++] = $temp['fail_instance'][0];
				}
			}
		}
		fclose($file);
		
		$result['message_type'] = 1;
		$result['message'] = array(
			'total' => $count,
			'success' => $success,
			'fail' => $error,
		);
		return $result;
	}
	
	// 设置图书为 已归还 状态
	function set_returned($getdata) {		
		if ($getdata['return_executed']) {
			$stock = $this->is_book_exist($getdata); // 获取当前库存
			
			$this->db->trans_start(); //事务开始
			
			//book表中相应的stock加1
			$data = array(
				'stock' => $stock + 1,
			);
		
			$this->db->where('bno', $getdata['bno']);
			$this->db->update('book', $data);
		
			$data = array(
				'returned' => 1,
				'return_executed' => $getdata['return_executed'],
			);
		
			$this->db->where('bno', $getdata['bno']);
			$this->db->where('id', $getdata['id']);
			$this->db->where('returned', 0);
			$this->db->update('borrow', $data);
				
			$this->db->trans_complete(); //事务结束
				
			$result['message_type'] = 1;
		}
		else
			$result['message_type'] = 2;
		
		return $result;
	}
	
	// 搜索功能
	function search($getdata, $opt) {
		$title = $getdata['title'];
		$author = $getdata['author'];
		$category = $getdata['category'];
		$press = $getdata['press'];
		$year_min = $getdata['year_min'];
		$year_max = $getdata['year_max'];
		$price_min = $getdata['price_min'];
		$price_max = $getdata['price_max'];
		
		if (!($title || $author || $category || $press || $year_min || $year_max || ($price_min >= 0) || $price_max)) {
			$result['result'] = array();
		}
		else {
			$this->db->trans_start(); //事务开始
			
			$this->db->select('*');
			$this->db->from('book');
			$this->db->order_by('bno');
			$this->db->limit(50);
			
			if ($opt == 1) { //模糊匹配
				if ($title) $this->db->like('title', $title);	
				if ($author) $this->db->like('author', $author);
				if ($category) $this->db->where('category', $category);
				if ($press) $this->db->where('press', $press);
				if ($year_min) $this->db->where('year >=', $year_min);
				if ($year_max) $this->db->where('year <=', $year_max);
				if ($price_min >= 0) $this->db->where('price >=', $price_min);
				if ($price_max) $this->db->where('price <=', $price_max);
			}
			else { //精确匹配
				if ($title) $this->db->where('title', $title);	
				if ($author) $this->db->where('author', $author);
				if ($category) $this->db->where('category', $category);
				if ($press) $this->db->where('press', $press);
				if ($year_min) $this->db->where('year >=', $year_min);
				if ($year_max) $this->db->where('year <=', $year_max);
				if ($price_min >= 0) $this->db->where('price >=', $price_min);
				if ($price_max) $this->db->where('price <=', $price_max);
			}
			
			$query = $this->db->get();
			
			$this->db->trans_complete(); //事务结束
			
			$result['result'] = array();
			foreach ($query->result_array() as $row) {
				if ($row['stock'] == 0) {
					$data = array(
						'bno' => $row['bno'],
					);
					$return_date = $this->get_return_date($data);
					$row['return_date'] = $return_date;
				}
				array_push($result['result'], $row);
			}
		}
		return $result;
	}
	
	// 搜索已借阅的图书信息
	function search_borrow($getdata) {
		$this->load->model('user');
		
		if ($this->user->is_exsist($getdata)) {
			$result['result'] = $getdata;
			
			$this->db->trans_start(); //事务开始
			
			//获取最大借书数量
			$this->db->select('para');
			$this->db->from('config');
			$this->db->where('item', 'max_borrow_books');
			$query = $this->db->get();
			if ($query->num_rows() > 0) {
				$row = $query->first_row('array');
				$maxbooks = (int)$row['para'];
			}
			else
				$maxbooks = 3;
			
			//查找当前借阅图书
			$this->db->select();
			$this->db->from('borrow');
			$this->db->where('id', $getdata['id']);
			$this->db->where('returned', 0);
			$this->db->order_by("return_date", "asc"); 
			$this->db->join('book', 'book.bno = borrow.bno');
			
			$query = $this->db->get();
			$this->db->trans_complete(); //事务结束
			
			//已借阅书籍信息
			if ($query->num_rows() > 0)
				$result['result']['entry'] = $query->result_array();
			else
				$result['result']['entry'] = array();
				
			//判断是否可以继续借阅
			if ($query->num_rows() < $maxbooks)
				$result['enable_borrow'] = 1;
			else
				$result['enable_borrow'] = 0;
		}
		else
			$result['message_type'] = 3;
		
		return $result;
	}
	
	// 图书借阅借阅
	function borrow($getdata) {
		$stock = $this->is_book_exist($getdata);
		if ($stock > 0) {
			if ($this->is_returned($getdata) > 0) {
				$this->db->trans_start(); //事务开始
			
				//book表中相应的stock减1
				$data = array(
					'stock' => $stock - 1,
				);
				$this->db->where('bno', $getdata['bno']);
				$this->db->update('book', $data);
				
				//获取最长借阅天数
				$this->db->select('para');
				$this->db->from('config');
				$this->db->where('item', 'max_borrow_days');
				$query = $this->db->get();
				if ($query->num_rows() > 0) {
					$row = $query->first_row('array');
					$maxdays = (int)$row['para'];
				}
				else
					$maxdays = 3;
					
				//设置借阅，归还日期
				$borrow_data = date("Y-m-d", time());
				$tmp = mktime(0, 0, 0, date("m"), date("d") + $maxdays, date("Y"));
				$return_data = date("Y-m-d", $tmp);
				
				//插入borrow表
				$data = array(
					'id' => $getdata['id'],
					'bno' => $getdata['bno'],
					'borrow_date' => $borrow_data,
					'return_date' => $return_data,
					'borrow_executed' => $getdata['borrow_executed'],
					'returned' => 0,
				);
				$this->db->insert('borrow', $data); 
				
				$this->db->trans_complete(); //事务结束
				
				$result['message_type'] = 1;
			}
			else
				$result['message_type'] = 2;
		}
		else
			$result['message_type'] = 4;
		
		return $result;
	}
	
}