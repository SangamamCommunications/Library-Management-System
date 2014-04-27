	<script type="text/javascript">
	</script>

	<div class="col_12">
		<h2 style="margin:0px;">图书借还</h2>
	</div>
	<?php
		if (!isset($message_type)) $message_type = -1;
		if (!isset($result)) $result = array();
		if (!isset($enable_borrow)) $enable_borrow = 0;
		
		if ($message_type > 0) {
			switch ($message_type) { //信息类型
				case 1:
					echo '<div class="col_5 notice success">';
					echo '<span class="icon medium" data-icon="C"></span>';
					echo '操作完成！';
					echo '<a href="#close" class="icon close" data-icon="x"></a>';
					echo '</div>';
					break;
				case 2:
					echo '<div class="col_5 notice error">';
					echo '<span class="icon medium" data-icon="X"></span>';
					echo '操作失败！';
					echo '<a href="#close" class="icon close" data-icon="x"></a>';
					echo '</div>';
					break;
				case 3:
					echo '<div class="col_5 notice error">';
					echo '<span class="icon medium" data-icon="X"></span>';
					echo '您输入的借书证号不存在，请重新输入！';
					echo '<a href="#close" class="icon close" data-icon="x"></a>';
					echo '</div>';
					break;
				case 4:
					echo '<div class="col_6 notice error">';
					echo '<span class="icon medium" data-icon="X"></span>';
					echo '您输入的书号不正确，或该书已无库存，请重新输入！';
					echo '<a href="#close" class="icon close" data-icon="x"></a>';
					echo '</div>';
					break;
			}
			echo '<div class="clear"></div>';
		}
		
		if ($result == array()) {
			echo '<hr />';
			echo form_open('pages/view/service', array('class' => 'vertical')); ?>
			<input type="hidden" name="step" value="1">
				<div class="col_4">
					<label for="id">借书证卡号</label>
					<input name="id" type="text" placeholder="请输入要操作的借书证卡号"/>
			
					<button type="submit" class="medium blue">点击提交</button>
					<button type="reset" class="medium">重新填写</button>
				</div>
			</form>
			<div class="clear"></div>
	<?php
		}
		else {
			echo '<p>当前操作借书证卡号：<span style="font-weight:bold;">'.$result['id'].'</span>';
			echo '<hr style="margin-top: 5px; margin-bottom: 20px;"/>'; 
			echo '<h3>还书</h3>';
			if ($result['entry'] == array()) {
				echo '<p>该用户无在借图书</p>';
			}
			else {
				 ?>
				<table class="sortable striped tight" cellspacing="0" cellpadding="0">
					<thead><tr>
						<th>书号</th>
						<th>书名</th>
						<th>借出日期</th>
						<th>归还日期</th>
						<th>操作</th>
					</tr></thead>
			<?php
				echo '<tbody>';
				foreach ($result['entry'] as $item) {
					echo '<tr>';
					echo '<td>'.$item['bno'].'</td>';
					echo '<td>'.$item['title'].'</td>';
					echo '<td>'.$item['borrow_date'].'</td>';
					echo '<td>'.$item['return_date'].'</td>';
					echo '<td>';
					echo form_open('pages/view/service', array('class' => 'vertical'));
					echo '<input type="hidden" name="step" value="2">';
					echo '<input type="hidden" name="id" value="'.$result['id'].'">';
					echo '<input type="hidden" name="bno" value="'.$item['bno'].'">';
					echo '<button type="submit" class="small red">点击归还</button>';
					echo '</form>';
					echo '</td>';
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			}
			
			echo '<hr style="margin-top: 5px; margin-bottom: 20px;"/>'; 
			
			echo '<h3>借书</h3>';
			if ($enable_borrow) {
				echo form_open('pages/view/service', array('class' => 'vertical')); ?>
					<input type="hidden" name="step" value="3">
					<input type="hidden" name="id" value="<?php echo $result['id']; ?>">
					<div class="col_4">
						<label for="bno">书号</label>
						<input name="bno" type="text" placeholder="请输入要借阅的图书书号"/>
						
						<button type="submit" class="medium blue">点击提交</button>
						<button type="reset" class="medium">重新填写</button>
					</div>
				</form>
		<?php
			}
			else {
				echo '<div class="col_5 notice error">';
				echo '<span class="icon medium" data-icon="X"></span>';
				echo '该用户借阅的图书数量已超过限额，请先归还图书!';
				echo '<a href="#close" class="icon close" data-icon="x"></a>';
				echo '</div>';
			}
		}
	?>
	<hr style="margin-top: 15px;"/>