<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title></title>
		<style media="screen">
			p{
				font-size: 10px;
				margin: 0;
			}
			.green{
				color:green;
			}
			.blue{
				color:blue;
			}
			.purple{
				color:purple;
			}
			em{
				color:red;
			}
			.sub{
				margin-left: 20px;
			}
			.box{
				margin-top: 20px;
			}
		</style>
	</head>
	<body>
		<p>total：<?= $total?> , query: <?= $query;?>, took:<?= $took;?>ms</p>
		<?php
			foreach($data as $p){
				$highlight = "";
				if(!empty($p['highlight'])){
					foreach($p['highlight'] as $name => $hc){
						$highlight .= sprintf("<p class='sub'><span class='green'>%s</span>:%s</p>", $name, implode(',', $hc));
						$highlight .= sprintf("<p class='sub'><span class='purple'>%s</span>:%s</p>", $name, $p['_source'][$name]);
					}
				}
				echo sprintf("
				<div class='box'>
				<p><span class='blue'>%s</span> <span class='green'>_id</span>:%s, %s</p>
				%s
				</div>
				",
				$p['_score'],
				$p['_id'],
				$p['_source']['title'],
				$highlight
			);
			}
		?>
	</body>
</html>
