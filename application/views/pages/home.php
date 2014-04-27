	<!-- Slideshow -->
	<ul class="slideshow" width="960" height="200">
	<?php
		for ($i = 0; $i < 3; $i++) {
			if ($i == 0)
				echo '<li><h2></h2><img src="'.base_url().'css\img\banner\1.jpg" /></li>';
			$item = $notice['instance'][$i];
			if ($item['title']) {
				echo '<li>';
				echo '<span style="padding:20px; display:block; width:920px;">';
				echo '<h3>'.$item['title'].'</h3>';
				echo '<p>'.str_replace(chr(13), '<br>', $item['content']).'</p>';
				echo '</span>';
				echo '</li>';
			}
			if ($i == 0)
				echo '<li><h2></h2><img src="'.base_url().'css\img\banner\2.jpg" /></li>';
			if ($i == 1)
				echo '<li><h2></h2><img src="'.base_url().'css\img\banner\3.jpg" /></li>';
		}
	?>
	</ul>
	<div class="clear"></div>
	<hr class="alt2" />
	<!-- Column -->
	<h4>热门借阅</h4>
	<div class="col_6">
		<ul class="alt">
		<?php
//			echo $hot[0]['title'];
			for ($i = 0; isset($hot[$i]) && ($i < 5); $i++) {
				$item = $hot[$i];
				if ($i < 3) {
					echo '<li><span style="font-weight: bold; font-size: 16px; ">第 '.intval($i + 1).' 名</span>';
					echo '<span class="right" style="float:right; font-size: 16px; font-weight: bold;">'.$item['title'].'</span></li>';
				}
				else {
					echo '<li>第 '.intval($i + 1).' 名';
					echo '<span class="right" style="float:right;">'.$item['title'].'</span></li>';
				}
			}
		?>
		</ul>
	</div>
	<div class="col_6">
		<ul class="alt">
		<?php
			for ($i = 5; isset($hot[$i]) && ($i < 10); $i++) {
				$item = $hot[$i];
				echo '<li><div>第 '.intval($i + 1).' 名';
				echo '<span class="right" style="float:right;">'.$item['title'].'</span><div></li>';
			}
		?>
		</ul>
	</div>
	<hr />