<!DOCTYPE html>
<html>
<head>
	<title>老和山职业技术学院 数字图书馆</title>
	<meta charset="UTF-8">
	<meta name="description" content="" />
	<script type="text/javascript" src="<?php echo $JS_PATH; ?>jquery.min.js"></script>
	<!--[if lt IE 9]><script src="js/html5.js"></script><![endif]-->
	<script type="text/javascript" src="<?php echo $JS_PATH; ?>prettify.js"></script>                                   <!-- PRETTIFY -->
	<script type="text/javascript" src="<?php echo $JS_PATH; ?>kickstart.js"></script>                                  <!-- KICKSTART -->
	<link rel="stylesheet" type="text/css" href="<?php echo $CSS_PATH; ?>kickstart.css" media="all" />                  <!-- KICKSTART -->
	<link rel="stylesheet" type="text/css" href="<?php echo $CSS_PATH; ?>style.css" media="all" />                          <!-- CUSTOM STYLES -->
</head>
<body class="home">
	<a id="top-of-page"></a>
	<div id="header" class="center">
		<h3>欢迎光临</h3>
		<h2>老和山职业技术学院 数字图书馆</h2>
		<p id="slogan">Welcome to ZJU's digital library!</p>
	</div>
	
	<div id="wrap" class="clearfix">
	<!-- Menu Horizontal -->
	<div id="nav">
		<ul class="menu">
			<li <?php if ($nav_current == 'home') echo 'class="current"'; ?> ><a href="<?php echo base_url("index.php/pages/view/home"); ?>"><span class="icon" data-icon="I"></span>首页</a></li>
			<li <?php if ($nav_current == 'search') echo 'class="current"'; ?> ><a href="<?php echo base_url("index.php/pages/view/search"); ?>"><span class="icon" data-icon="s"></span>搜索</a></li>
			<?php if (isset($id) && isset($email) && isset($admin) && $admin == 1) {
						if ($nav_current == 'service')
							echo '<li class="current"; >';
						else
							echo '<li>';
						echo '<a href="'.base_url("index.php/pages/view/service").'"><span class="icon" data-icon="w"></span>图书借还</a></li>';
					}
			?>
		</ul>
	<?php
		if (isset($id) && isset($email)) {
			echo '<span><a class="btn small green" style="right: 80px;" href="'.base_url("index.php/pages/view/panel").'">电子邮箱：'.$email.' | 借书证卡号：'.$id.'</a></span>';
			echo '<span><a class="btn small pink" href="'.base_url("index.php/pages/view/logout").'">退出</a></span>';
		}
		else {?>
		<a class="btn small orange" style="right: 80px;" href="<?php echo base_url("index.php/pages/view/register"); ?>">注册</a>
		<a class="btn small green" href="<?php echo base_url("index.php/pages/view/login"); ?>">登录</a>
	<?php
		}
	?>
	</div>
<!-- ===================================== END HEADER ===================================== -->
