	<script type="text/javascript">
	</script>

	<div class="col_12">
		<h2 style="margin:0px;">搜索</h2>
	</div>
	<?php echo form_open('pages/view/search', array('id' => 'searchform', 'class' => 'vertical')); ?>
		<input type="hidden" name="indirect" value="1">
		<div class="col_4">
			<label for="title">书名</label>
			<input name="title" type="text" <?php if (isset($form) && isset($form['title'])) echo 'value="'.$form['title'].'";' ?> />
			
			<label for="category">类别</label>
			<select name="category" class="fancy" >
			<?php
				echo '<option value="0">-- 请选择图书种类 --</option>';
				if (isset($list) && isset($list['category'])) {
					foreach ($list['category'] as $option) {
						echo '<option value="'.$option.'" ';
						if (isset($form) && isset($form['category']) && $form['category'] == $option)
							echo 'selected="selected" ';
						echo '>'.$option.'</option>';
					}
				}
			?>
			</select>
		</div>
		
		<div class="col_4">
			<label for="author">作者</label>
			<select name="author" class="fancy" >
			<?php
				echo '<option value="0">-- 请选择作者 --</option>';
				if (isset($list) && isset($list['author'])) {
					foreach ($list['author'] as $option) {
						echo '<option value="'.$option.'" ';
						if (isset($form) && isset($form['author']) && $form['author'] == $option)
							echo 'selected="selected"';
						echo '>'.$option.'</option>';
					}
				}
			?>
			</select>
			
			<label for="press" style="margin-bottom: 3px; top: 3px;">出版社</label>
			<select name="press" class="fancy" >
			<?php
				echo '<option value="0">-- 请选择出版社 --</option>';
				if (isset($list) && isset($list['press'])) {
					foreach ($list['press'] as $option) {
						echo '<option value="'.$option.'" ';
						if (isset($form) && isset($form['press']) && $form['press'] == $option)
							echo 'selected="selected"';
						echo '>'.$option.'</option>';
					}
				}
			?>
			</select>
		</div>
		
		<div class="col_2">
			<label for="year_min">年份</label>
			<input name="year_min" type="text" placeholder="最小年份>=1900" <?php if (isset($form) && isset($form['year_min'])) echo 'value="'.$form['year_min'].'";' ?> />
			
			<label for="price_min">价格</label>
			<input name="price_min" type="text" placeholder="最低价>=0" <?php if (isset($form) && isset($form['price_min'])) echo 'value="'.$form['price_min'].'";' ?> />
		</div>
		<div class="col_2">
			<label for="year_max">&nbsp;<span class="right">年份区间</span></label>
			<input name="year_max" type="text" placeholder="最大年份<=2100" <?php if (isset($form) && isset($form['year_max'])) echo 'value="'.$form['year_max'].'";' ?> />
			
			<label for="price_max">&nbsp;<span class="right">价格区间</span></label>
			<input name="price_max" type="text" placeholder="最高价" <?php if (isset($form) && isset($form['price_max'])) echo 'value="'.$form['price_max'].'";' ?> />
			
			<button name="submit" type="submit" class="large pill orange" style="left: 15px; top: 10px;">
				<span class="icon large" data-icon="s"></span>搜索
			</button>
		</div>
		<div class="clear"></div>
	</form>
	<hr style="margin-top: 15px;"/>
	
	<?php
	if (isset($result)) {
		if (count($result) == 0) {
			echo '结果为空';
		}
		else { ?>
			<p><span style="color:red;">红色条目：</span>表示该书已无库存，鼠标停留可显示最早的归还日期</p>
			<table class="sortable striped tight" cellspacing="0" cellpadding="0">
				<thead><tr>
					<th>书号</th>
					<th>书名</th>
					<th>类别</th>
					<th>出版社</th>
					<th>年份</th>
					<th>作者</th>
					<th>价格</th>
					<th>总藏书量</th>
					<th>库存</th>
				</tr></thead>
				
		<?php
				echo '<tbody>';
				foreach ($result as $entry) {
					if (isset($entry['return_date']))
						echo '<tr class="tooltip-top" title="最早的归还日期： '.$entry['return_date'].'" style="color:red;">';
					else
						echo '<tr>';
					echo '<td>'.$entry['bno'].'</td>';
					echo '<td>'.$entry['title'].'</td>';
					echo '<td>'.$entry['category'].'</td>';
					echo '<td>'.$entry['press'].'</td>';
					echo '<td>'.$entry['year'].'</td>';
					echo '<td>'.$entry['author'].'</td>';
					echo '<td>'.$entry['price'].'</td>';
					echo '<td>'.$entry['total'].'</td>';
					echo '<td>'.$entry['stock'].'</td>';
					echo '</tr>';
				}
				echo '</tbody>';
			echo '</table>';
		}
		echo '<hr style="margin-top: 15px;"/>';
	}
	 ?>