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
			.grey{
				color:#ccc;
			}
			.purple{
				color:purple;
			}
			em{
				color:red;
				margin:0px 4px;
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
		<input type="text" name="q" id="q" value="">
		<div id="wrap">
			<p>total：<?= $total?> , query: <?= $query;?>, took:<?= $took;?>ms</p>
			<?php
				foreach($data as $p){
					$highlight = "";
					if(!empty($p['highlight'])){
						foreach($p['highlight'] as $name => $hc){
							$highlight .= sprintf("<p class='sub'><span class='green'>%s</span>:%s <span class='grey'>%s</span></p>", $p['_score'], implode(',', $hc), $p['_source']['type']);
						}
					}
					echo sprintf("
					<p>%s</p>
					",
					$highlight
				);
				}
			?>
		</div>



	</body>
	<script type="text/javascript" src="https://cdn.bootcss.com/jquery/2.2.1/jquery.js"></script>
	<script type="text/javascript">
	$(function(){
		$('#q').keyup(function(e){
			if($('#q').val()){
				$.get("http://192.168.1.103:8054/admin/demo/get?q=" + $('#q').val(), function(res){
					$('#wrap').html(res);
				})
			}
		});
	});
	</script>
</html>
