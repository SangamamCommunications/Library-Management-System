<?php
	if (!isset($message_type)) $message_type = -1;
	echo '<div class="col_12"><h2 style="margin:0px;">注册</h2></div>';
	if ($message_type > 0) {
		echo '<div class="col_5 notice error">';
		echo '<span class="icon medium" data-icon="X"></span>';
		switch ($message_type) { //信息类型
			case 1:
				echo '您输入的注册信息不完整，请重新输入！';
				break;
			case 2:
				echo '您输入的邮箱地址有误，请重新输入！';
				break;
			case 3:
				echo '您输入的两次密码不匹配，请重新输入！';
				break;
			case 4:
				echo '您输入的邮箱地址已被注册，请重新输入！';
				break;
		}
		echo '<a href="#close" class="icon close" data-icon="x"></a>';
		echo '</div>';
		echo '<div class="clear"></div>';
	}	
?>
	<div class="col_5">
		<?php echo form_open('pages/view/register', array('class' => 'vertical')); ?>
			<input type="hidden" name="indirect" value="1">
			<label for="email">邮箱 <span class="right">请填写常用邮箱地址</span></label>
			<input name="email" type="text" />
			<label for="pwd1">密码 <span class="right">6~16个字符，区分大小写</span></label>
			<input name="pwd1" type="password" maxlength="16"/>
			<label for="pwd2">确认密码 <span class="right">请再次填写密码</span></label>
			<input name="pwd2" type="password" maxlength="16"/>
			<button name="submit" type="submit" class="medium blue">点击注册</button>
			<button type="button" class="medium">重新填写</button>
		</form>
	</div>
	<hr />