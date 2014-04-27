<?php
	if (!isset($admin)) $admin = 0;
	if (!isset($tab_current)) $tab_current = 'update_book';
	if (!isset($message_type)) $message_type = -1;
	
	if (!$admin) {
		echo '<div class="col_12"><h2 style="margin:0px;">我的主页</h2></div>';
		
		echo '<h4>在借图书</h4>';
		if (isset($result)) { ?>
			<p><span style="color:red;">红色条目：</span>表示该书超过规定借阅时间，请尽快归还！</p>
			<table class="sortable striped tight" cellspacing="0" cellpadding="0">
				<thead><tr>
					<th>书号</th>
					<th>书名</th>
					<th>借出日期</th>
					<th>归还日期</th>
				</tr></thead>
			<?php
				echo '<tbody>';
				$now = date("Y-m-d",time());
				foreach ($result['entry'] as $item) {
				 
					if (date("Y-m-d", strtotime($item['return_date'])) < date("Y-m-d", strtotime($now)))
						echo '<tr class="tooltip-top" title="超过规定借阅时间，请尽快归还！" style="color:red;">';
					else
						echo '<tr>';
					echo '<td>'.$item['bno'].'</td>';
					echo '<td>'.$item['title'].'</td>';
					echo '<td>'.$item['borrow_date'].'</td>';
					echo '<td>'.$item['return_date'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
		}
		else {
			echo '<p>无在借图书</p>';
		}
	}
	else {
		echo '<div class="col_12"><h2 style="margin:0px;">我的主页</h2></div>';
?>
		<ul class="tabs left">
			<li <?php if ($tab_current == 'update_book') echo 'class="current"'; ?> ><a href="#update_book">批量入库</a></li>
			<li <?php if ($tab_current == 'update_user') echo 'class="current"'; ?> ><a href="#update_user">增加借书证</a></li>
			<li <?php if ($tab_current == 'update_admin') echo 'class="current"'; ?> ><a href="#update_admin">增加管理员</a></li>
			<li <?php if ($tab_current == 'update_config') echo 'class="current"'; ?> ><a href="#update_config">系统设置</a></li>
		</ul>
		
		<div id="update_book" class="tab-content" <?php if ($tab_current != 'update_book') echo 'style="display: none;"'; ?> >
		<?php
			if ($tab_current == 'update_book') {
				switch ($message_type) { //信息类型
					case 1: //添加记录
						echo '<h4>共提交 '.$message['total'].' 条有效的图书信息</h4>';
						if ($message['success']) {
							echo '<div class="col_6 notice success">';
							echo '<span class="icon medium" data-icon="C"></span>';
							echo $message['success'].' 条图书信息入库成功！';
							echo '<a href="#close" class="icon close" data-icon="x"></a>';
							echo '</div>';
						}
						if ($message['fail']) {
							echo '<div class="col_6 notice error">';
							echo '<span class="icon medium" data-icon="X"></span>';
							echo $message['fail'].' 条图书信息入库失败！';
							echo '<a href="#close" class="icon close" data-icon="x"></a>';
							echo '</div>';
							
							if (isset($fail_instance)) {
								foreach ($fail_instance as $item) {
									if ($item['bno'] == 'bno') continue;
									echo '<div class="col_6 notice error">';
									echo '<span class="icon medium" data-icon="X"></span>';
									echo '书号为 '.$item['bno'].' 的图书 《'.$item['title'].'》 ';
									switch ($item['type']) {
										case 0:
											echo '信息错误！';
											break;
										case 1:
											echo '已经入库！';
											break;
									}
									echo '<a href="#close" class="icon close" data-icon="x"></a>';
									echo '</div>';
								}
							}
						}
						break;
					case 2: //信息不完整
						echo '<div class="col_5 notice error">';
						echo '<span class="icon medium" data-icon="X"></span>';
						echo '图书信息不完整，请重新输入！';
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
					case 3: //csv文件上传错误
						echo '<div class="col_5 notice error">';
						echo '<span class="icon medium" data-icon="X"></span>';
						echo 'csv文件上传错误，请重新上传！<br>';
						echo $message['file_error'];
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
				}
				//echo '<hr />';
				echo '<div class="clear"></div>';
			}
		?>
			<div class="col_6">
				<h3>单本入库</h3>
				<?php echo form_open('pages/view/panel', array('class' => 'vertical')); ?>
					<input type="hidden" name="tab_current" value="update_book">
					<input type="hidden" name="upload_type" value="0">
					
					<label for="bno">书号</label>
					<input name="bno" type="text">
					<label for="title">书名</label>
					<input name="title" type="text">
					<label for="category">类别</label>
					<input name="category" type="text">
					<label for="year">年份</label>
					<input name="year" type="text">
					<label for="author">作者</label>
					<input name="author" type="text">
					<label for="press">出版社</label>
					<input name="press" type="text">
					<label for="price">价格</label>
					<input name="price" type="text">
					<label for="total">数量</label>
					<input name="total" type="text">
					<button type="submit" class="medium blue">点击提交</button>
					<button type="reset" class="medium">重新填写</button>
				</form>
			</div>
			
			<div class="col_6">
				<h3>多本入库</h3>
				<div class="col_12 notice warning">
					<span style="font-size:28px; font-weight:bold;">
					<span class="icon large" data-icon="!"></span>
					<span style="margin-left: -10px;">提示</span>
					</span>
					<p>这里可以上传包含所要添加的图书信息的csv文件</p>
					<p>信息的标准格式应包括以下 8 类信息：</p>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;书号, &nbsp;&nbsp;书名, &nbsp;&nbsp;类别, &nbsp;&nbsp;年份, &nbsp;&nbsp;作者, &nbsp;&nbsp;出版社, &nbsp;&nbsp;价格, &nbsp;&nbsp;数量
					<p>可以参照提供的标准csv文件（可使用Excel编辑）<br>
					<a href="http://localhost:8080/library/uploads/book.csv">点击下载标准csv文件</a></p>
				</div>
				<div class="clear"></div>
				
				<?php echo form_open_multipart('pages/view/panel', array('class' => 'vertical'));?>
					<input type="hidden" name="tab_current" value="update_book">
					<input type="hidden" name="upload_type" value="1">
					
					<h5>请选择cvs文件</h5>
					<input name="userfile" type="file" />
					<button type="submit" class="medium blue">点击提交</button>
					<button type="reset" class="medium">重新填写</button>
				</form>
			</div>
		</div>
		
		<div id="update_user" class="tab-content" <?php if ($tab_current != 'update_user') echo 'style="display: none;"'; ?> >
		<?php
			if ($tab_current == 'update_user') {
				switch ($message_type) { //信息类型
					case 0: //添加成功
						echo '<div class="col_5 notice success">';
						echo '<h4 style="margin-top:0px;"><span class="icon medium" data-icon="C"></span>添加成功</h4>';
						echo '新添加的帐号信息：<br>';
						echo '注册邮箱：'.$message['email'].' <br>';
						echo '借书证卡号：'.$message['id'].' <br>';
						echo '密码：'.$message['pwd'].' <br>';
						echo '请您保存好以上信息，谢谢！';
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
					
					case 1: //输入信息不完整
					case 2: //邮箱地址有误
					case 3: //密码不匹配
					case 4: //邮箱已注册
					case 5: //当前管理员密码错误
						echo '<div class="col_5 notice error">';
						echo '<span class="icon medium" data-icon="X"></span>';
						switch ($message_type) {
							case 1: //输入信息不完整
								echo '您输入的注册信息不完整，请重新输入！';
								break;
							case 2: //邮箱地址有误
								echo '您输入的邮箱地址有误，请重新输入！';
								break;
							case 3: //密码不匹配
								echo '您输入的两次密码不匹配，请重新输入！';
								break;
							case 4: //邮箱已注册
								echo '您输入的邮箱地址已经注册过，请重新输入！';
								break;
							case 5: //当前管理员密码错误
								echo '当前管理员密码有误，请重新输入！';
								break;
						}
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
				}
				echo '<div class="clear"></div>';
			}
		?>
			<div class="col_5">
				<?php echo form_open('pages/view/panel', array('class' => 'vertical')); ?>
					<input type="hidden" name="tab_current" value="update_user">
					
					<label for="id">借书证卡号 <span class="right">新增借书证卡号由系统分配</span></label>
					<input name="id" type="text" disabled="disabled" />
					<label for="email">邮箱 <span class="right">请填写常用邮箱地址</span></label>
					<input name="email" type="text" />
					<label for="pwd1">密码 <span class="right">6~16个字符，区分大小写</span></label>
					<input name="pwd1" type="password" maxlength="16" />
					<label for="pwd2">确认密码 <span class="right">请再次填写密码</span></label>
					<input name="pwd2" type="password" maxlength="16" />
					<label for="adminpwd">当前管理员密码 <span class="right">为保证安全请输入当前管理员密码</span></label>
					<input name="adminpwd" type="password" maxlength="16" />
					<button type="submit" class="medium blue">点击添加</button>
					<button type="reset" class="medium">重新填写</button>
				</form>
			</div>
		</div>
	
		<div id="update_admin" class="tab-content" <?php if ($tab_current != 'update_admin') echo 'style="display: none;"'; ?>>
		<?php
			if ($tab_current == 'update_admin') {
				switch ($message_type) { //信息类型
					case 1: //添加成功
						echo '<div class="col_5 notice success">';
						echo '帐号（邮箱/卡号）：'.$message['email'].' / '.$message['id'].' <br>';
						echo '已成功设置为管理员！';
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
					case 2: //被操作的帐号不存在
						echo '<div class="col_5 notice error">';
						echo '<span class="icon medium" data-icon="X"></span>';
						echo '被操作的帐号不存在，请重新输入！';
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
					case 3: //当前管理员密码错误
						echo '<div class="col_5 notice error">';
						echo '<span class="icon medium" data-icon="X"></span>';
						echo '当前管理员密码有误，请重新输入！';
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
				}
				echo '<div class="clear"></div>';
			}
		?>
		
			<div class="col_5">
				<?php echo form_open('pages/view/panel', array('class' => 'vertical')); ?>
					<input type="hidden" name="tab_current" value="update_admin">
					
					<label for="username">帐号<span class="right">借书证卡号 / 邮箱地址</span></label>
					<input name="username" type="text" />
					<label for="adminpwd">当前管理员密码 <span class="right">为保证安全请输入当前管理员密码</span></label>
					<input name="adminpwd" type="password" />
					<button type="submit" class="medium blue">点击添加</button>
					<button type="reset" class="medium">重新填写</button>
				</form>
			</div>
		</div>
		
		<div id="update_config" class="tab-content" <?php if ($tab_current != 'update_config') echo 'style="display: none;"'; ?> >
		<?php
			if ($tab_current == 'update_config') {
				switch ($message_type) { //信息类型
					case 1: //设置成功
						echo '<div class="col_5 notice success">';
						echo '设置成功！';
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
					case 2: //设置失败
						echo '<div class="col_5 notice error">';
						echo '<span class="icon medium" data-icon="X"></span>';
						echo '设置失败！';
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
					case 3: //当前管理员密码错误
						echo '<div class="col_5 notice error">';
						echo '<span class="icon medium" data-icon="X"></span>';
						echo '当前管理员密码有误，请重新输入！';
						echo '<a href="#close" class="icon close" data-icon="x"></a>';
						echo '</div>';
						break;
				}
				echo '<div class="clear"></div>';
			}
		?>
			<div class="col_5">
				<h3>参数设置</h3>
			<?php
				if (isset($config)) {
					echo form_open('pages/view/panel', array('class' => 'vertical'));
					echo '<input type="hidden" name="tab_current" value="update_config">'; ?>
					<input type="hidden" name="config" value="config">
					<label for="max_borrow_books">设置最长借书数量<span class="right">如不变更请勿更改</span></label>
					<input name="max_borrow_books" type="text" value="<?php echo $config['max_borrow_books']; ?>"/>
					<label for="max_borrow_days">设置最长借书时间<span class="right">如不变更请勿更改</span></label>
					<input name="max_borrow_days" type="text" value="<?php echo $config['max_borrow_days']; ?>"/>
					
					<label for="adminpwd">当前管理员密码 <span class="right">为保证安全请输入当前管理员密码</span></label>
					<input name="adminpwd" type="password" />
					
					<button type="submit" class="medium blue">点击添加</button>
					</form>
			<?php
				} ?>
			</div>
			
			<div class="col_7">
				<h3>首页通知设置</h3>
			<?php
					
				if (isset($notice)) {
					echo form_open('pages/view/panel', array('class' => 'vertical'));
					echo '<input type="hidden" name="tab_current" value="update_config">';
					echo '<input type="hidden" name="config" value="notice">';
					echo '<input type="hidden" name="num" value="'.$notice['num'].'">';
					for ($i = 0; $i < $notice['num']; $i++) {
						$item = $notice['instance'][$i];
						echo '<input type="hidden" name="id_'.$i.'" value="'.$item['id'].'">';
						echo '<label for="title_'.$i.'">通知 '.(string)($i + 1).' <span class="right">如不变更请勿改动</span></label>';
						echo '<input name="title_'.$i.'" type="text" placeholder="通知标题" value="'.$item['title'].'"/>';
						echo '<textarea name="content_'.$i.'" placeholder="通知正文" rows="3">'.$item['content'].'</textarea>';
					} ?>
					<label for="adminpwd">当前管理员密码 <span class="right">为保证安全请输入当前管理员密码</span></label>
					<input name="adminpwd" type="password"/>
					
					<button type="submit" class="medium blue">点击添加</button>
					</form>
			<?php
				} ?>
			</div>
		</div>
<?php
	}
?>