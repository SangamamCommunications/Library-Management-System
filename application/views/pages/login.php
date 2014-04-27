<?php
	if (!isset($message_type)) $message_type = -1;
	echo '<div class="col_12"><h2 style="margin:0px;">登录</h2></div>';
	//echo '<h2>登录</h2>';
	switch ($message_type) { //信息类型
		case 1: //注册成功并提示登录
			echo '<div class="col_5 notice success">';
			echo '<h4 style="margin-top:0px;"><span class="icon medium" data-icon="C"></span>注册成功</h4>';
			echo '您的帐号信息：<br>';
			echo '注册邮箱：'.$message['email'].' <br>';
			echo '借书证卡号：'.$message['id'].' <br>';
			echo '密码：'.$message['pwd'].' <br>';
			echo '请您保存好以上信息，谢谢！';
			echo '<a href="#close" class="icon close" data-icon="x"></a>';
			echo '</div>';
			break;
		case 2: //登录信息不全
			echo '<div class="col_5 notice error">';
			echo '<span class="icon medium" data-icon="X"></span>';
			echo '您输入的登录信息不完整，请重新输入！';
			echo '<a href="#close" class="icon close" data-icon="x"></a>';
			echo '</div>';
			break;
		case 3: //登录失败信息
			echo '<div class="col_5 notice error">';
			echo '<span class="icon medium" data-icon="X"></span>';
			echo '您输入的账号或密码有误，请重新输入！';
			echo '<a href="#close" class="icon close" data-icon="x"></a>';
			echo '</div>';
			break;
	}
	if ($message_type > 0)
		echo '<div class="clear"></div>';
?>
	<div class="col_5">
		<?php echo form_open('pages/view/login', array('class' => 'vertical')); ?>
			<input type="hidden" name="indirect" value="1">
			<label for="username">帐号 <span class="right">借书证卡号 / 邮箱地址</span></label>
			<input name="username" type="text" />
			<label for="pwd">密码 <span class="right">6~16个字符，区分大小写</span></label>
			<input name="pwd" type="password" maxlength="16"/>
			<button name="submit" type="submit" class="medium blue">点击登录</button>
			<button type="button" class="medium">重新填写</button>
		</form>
	</div>
	<hr />