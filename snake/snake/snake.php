<!DOCTYPE html>
<html>
<head>
<title>贪吃蛇</title>

<meta name="keywords" content="snake,贪吃蛇,蛇">
<meta name="description" content="this is my page">
<meta name="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">

<link rel="stylesheet" type="text/css" href="./css/style.css">
<link rel="stylesheet" type="text/css" href="./css/button_control.css">
<script src="js/fastclick.js" type="text/javascript"></script>
    
<style type="text/css">
	
    body{
        margin: 0px;
        padding: 0px;
    }
    
	/*游戏表格*/
	.game_table{
		width: 500px;
		height: 500px;
		border: 1px solid black;
	}
	
	/*排行榜*/
	.game_player_list{
	    border: 1px solid black;
	    border-collapse: collapse;
        margin: 0 auto;
        margin-top: 1px
	}
    
    /*排行榜表格*/
	.game_player_list tr td{
        text-align: center;
		padding: 10px;
        border: 1px solid black;
	}
</style>
    
</head>

<body>
    
    <!-- 游戏表格 -->
    <div style="width:850px; margin:100px auto;">
        <h1 align="center">贪吃蛇小游戏<a href='http://www.verysd.com/' align='center' >返回到论坛</a></h1>
        <div style="float: left;">
			<table id="game_div" class="game_table">
			</table>
        </div>
		
		<!-- 获取排行榜 -->
    	<div style="float: left; border: 1px solid black; border-left:0px">
    		<h1 id="score" style="width:330px; height:25px;" align="center">分数：0</h1>
    		<div style="overflow-x: auto; overflow-y: auto; height: 430px; width:330px;">
				<table id="table_player_list" class="game_player_list">
					<tr><td	colspan="2">排行榜(0/0)</td></tr>
    			    <tr><td	colspan="2"><button class="green_btn" onclick="update()" >刷新</button></td></tr>
				</table>
    		</div>
    	</div>
    </div>
    
	<!-- 提交排行榜 -->
	<form name="form_list" action="player.php" method="post">
		<input type="hidden" name="name" />
        <input type="hidden" name="score" />
        <input type="hidden" name="food" />
        <input type="hidden" name="move" />
	</form>
    
    
    <div id="mobile" style="width:850px;" align="center">
        <table>
		<tr><td></td><td><a id="move_up" class="button white">↑</a></td><td></td></tr>
		<tr><td><a id="move_left" class="button white">←</a></td><td></td><td><a id="move_right" class="button white">→</a></td></tr>
		<tr><td></td><td><a id="move_down" class="button white">↓</a></td><td></td></tr>
		</table>
    </div>
    
    <script type="text/javascript" language="javascript">
    	var system ={};
    	var p = navigator.platform;
    	system.win = p.indexOf("Win") == 0;
    	system.mac = p.indexOf("Mac") == 0;
    	system.x11 = (p == "X11") || (p.indexOf("Linux") == 0);
    	if(system.win || system.mac || system.xll){	//如果是电脑
            //隐藏按钮
            window.document.getElementById("mobile").style.display = "none";
    	}else{  //如果是手机
            //手机布局
            var oMeta = document.createElement('meta');
			oMeta.name = 'viewport';
            oMeta.content = 'width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no';
			document.getElementsByTagName('head')[0].appendChild(oMeta);
            document.getElementsByTagName('div')[0].style.marginLeft = '12px';
            
            //按钮监听
            var left_btn = window.document.getElementById("move_left");
            left_btn.addEventListener('touchstart', function () {
            	window.KeyButtonPress(37);
            });
            var right_btn = window.document.getElementById("move_right");
            right_btn.addEventListener('touchstart', function () {
            	window.KeyButtonPress(39);
            });
            var up_btn = window.document.getElementById("move_up");
            up_btn.addEventListener('touchstart', function () {
            	window.KeyButtonPress(38);
            });
            var down_btn = window.document.getElementById("move_down");
            down_btn.addEventListener('touchstart', function () {
            	window.KeyButtonPress(40);
            });
            
    	}
	</script>

</body>

<?php
    
	if($_COOKIE['MQdK_2132_auth'] == null){
 		echo '<script>alert(\'您还未登录,请先登录！\');window.location.href = "http://verysd.com/member.php?mod=logging&action=login";</script>';
	}
    
    function _cut($begin,$end,$str){
                $b = mb_strpos($str,$begin) + mb_strlen($begin);
                $e = mb_strpos($str,$end) - $b;
                return mb_substr($str,$b,$e);
    }
    $uid = explode('|', $_COOKIE['MQdK_2132_lastcheckfeed'])[0];
    $getcontent = _cut("<title>", "的个人资料", file_get_contents("http://www.verysd.com/?".$uid));
    
    $arr = explode("\n", file_get_contents("./player.ini"));
    // 去掉换行符使用 | 合并到一起
    foreach ($arr as $k => $v) {
        if($k!=count($arr)-1){
            //分割数据
            $data = explode("=>", $v);
            //取数据(name,score)
        	$player_list = $player_list.$data[0].'=>'.$data[1].($k!=count($arr)-2?'|':'');
        }
    }
?>
<script type="text/javascript">
    
    //更新排行榜
    function update(){
        var player_table = document.getElementById("table_player_list");
        player_table.rows[player_table.rows.length-1].cells[0].children[0].innerHTML = "刷新中...";
        setTimeout(function () {
        	while(player_table.rows.length > 2)player_table.deleteRow(1);	//清空表格
        	var player_list = "<?php echo $player_list ?>".split('|');
            var data = [];	//排行榜数据
        	for(var i=0;i<player_list.length;i++){
        	    var ns = player_list[i].split('=>');
        	    data.push({name:ns[0], score:ns[1]});
        		//alert(player_list[i]);
        	}
            //排序及绘制
            if(data.length != 0)data.sort(function(obj1,obj2){return parseInt(obj1.score) - parseInt(obj2.score);});
        	for(var i=0;i<data.length;i++){
        	    var row = player_table.insertRow(1);
        	    var name = row.insertCell();
        	    var score = row.insertCell();
                name.innerHTML = "<a href='snake_record.php?name=" + data[i].name + "'>" + data[i].name + "</a>";
        	    score.innerHTML = data[i].score;
                if(data[i].name == window.userinfo.name){
                    window.userinfo.ranking =  data.length - i;
                    window.userinfo.score = data[i].score;
                }
        		//alert(player_list[i]);
        	}
            player_table.rows[0].cells[0].innerHTML = "排行榜(" + window.userinfo.ranking + "/" + data.length + ")";
        	player_table.rows[player_table.rows.length-1].cells[0].children[0].innerHTML = "刷新";
        }, 300);
    }

	//随机数
	function randomNum(minNum, maxNum) {
		switch (arguments.length) {
		case 1:
			return parseInt(Math.random() * minNum + 1, 10);
			break;
		case 2:
			return parseInt(Math.random() * (maxNum - minNum + 1) + minNum, 10);
			break;
		default:
			return 0;
			break;
		}
	}

	//设置食物
	function set_food() {
        document.getElementById("score").innerHTML = "分数：" + (window.snake.length - 1);
		do{
			window.food.x = randomNum(0, window.map_width - 1);
			window.food.y = randomNum(0, window.map_height - 1);
		}while(window.table.rows[window.food.y].cells[window.food.x].style.backgroundColor == "green" || window.table.rows[window.food.y].cells[window.food.x].style.backgroundColor == "red");
		window.table.rows[window.food.y].cells[window.food.x].style.backgroundColor = "blue";
        window.record.food.push({x:window.food.x, y:window.food.y}); //记录食物
	}

	//监听按键
	document.onkeydown = KeyPress;
	function KeyPress() {
		var e;
		if (document.all) //判断是否是IE
			e = event;
        else
			e = KeyPress.arguments[0];
        var key = e.keyCode;
		//alert(key); //弹出对话框显示 键盘的数字值
        if(window.last_dir != window.dir) return;
		switch (key) {
		case 37: //←
			if (window.dir != 6 || window.gm){
                if(window.dir == 4 && e.shiftKey) move();
				window.dir = 4;
            }
			break;
		case 38: //↑
			if (window.dir != 2 || window.gm){
                if(window.dir == 8 && e.shiftKey) move();
				window.dir = 8;
            }
			break;
		case 39: //→
			if (window.dir != 4 || window.gm){
                if(window.dir == 6 && e.shiftKey) move();
				window.dir = 6;
            }
			break;
		case 40: //↓
			if (window.dir != 8 || window.gm){
                if(window.dir == 2 && e.shiftKey) move();
				window.dir = 2;
            }
			break;
		}
	}
    
    //监听按钮(手机)
	function KeyButtonPress(key) {
        if(window.last_dir != window.dir) return;
		switch (key) {
		case 37: //←
			if (window.dir != 6 || window.gm){
                //if(window.dir == 4) move();
				window.dir = 4;
            }
			break;
		case 38: //↑
			if (window.dir != 2 || window.gm){
                //if(window.dir == 8) move();
				window.dir = 8;
            }
			break;
		case 39: //→
			if (window.dir != 4 || window.gm){
                //if(window.dir == 6) move();
				window.dir = 6;
            }
			break;
		case 40: //↓
			if (window.dir != 8 || window.gm){
                //if(window.dir == 2) move();
				window.dir = 2;
            }
			break;
		}
	}
    
    //判断是否可通行
    function snake_passable(x, y, dir){
        x = x + (dir == 4 ? -1 : dir == 6 ? 1 : 0);
        y = y + (dir == 8 ? -1 : dir == 2 ? 1 : 0);
		return !(y < 0 || y >= window.table.rows.length || x < 0 || x >= window.table.rows[y].cells.length || (!window.gm && window.table.rows[y].cells[x].style.backgroundColor == "green"));
    }

	//移动
	function move() {
        window.record.move.push(window.dir); //记录方向
        
       	window.last_dir = window.dir;
        var direction = window.dir;
        var head = {
			x : window.snake[0].x,
			y : window.snake[0].y
		};
		var lose = false;

        //移动后的位置
        head.x = head.x + (direction == 4 ? -1 : direction == 6 ? 1 : 0);
        head.y = head.y + (direction == 8 ? -1 : direction == 2 ? 1 : 0);
        //移动后的位置是否可通行，相撞(撞墙、撞身体)判断
        if (!snake_passable(head.x, head.y)) lose = true;
        
		//蛇身移动
        var tail_pos; //定义尾巴
		if ([2,4,6,8].indexOf(direction) != -1 && !lose) {
            window.snake.unshift({x:head.x, y:head.y});	//头部填充一格
            tail_pos = window.snake.pop();	//尾部删除一格
            window.table.rows[tail_pos.y].cells[tail_pos.x].style.backgroundColor = "white";
            
			//重绘数组，先蛇身、后蛇头，蛇头可重叠在蛇身上方
			for (var i = 1; i < window.snake.length; i++) {
				window.table.rows[window.snake[i].y].cells[window.snake[i].x].style.backgroundColor = "green";
			}
            window.table.rows[head.y].cells[head.x].style.backgroundColor = "red";
		}
        
		//吃食物(添加身体,设置食物)
		if (head.x == window.food.x && head.y == window.food.y) {
			window.snake.push({x:tail_pos.x, y:tail_pos.y});
            window.table.rows[window.snake[window.snake.length-1].y].cells[window.snake[window.snake.length-1].x].style.backgroundColor = "green";
			//100分为全屏,否则设置食物
            if(window.snake.length == 100){window.record.move.push(window.dir);lose = true;}
            else set_food();
		}
        
        //游戏结束
		if (lose) {
            //分数大于旧分数，可以提交
            if(window.snake.length - 1 >= window.userinfo.score){//confirm("GameOver，分数：" + (window.snake.length - 1) + ",是否上传更新排行榜(分数大可覆盖)？")
				document.form_list.name.value = window.userinfo.name;
            	document.form_list.score.value = window.snake.length - 1;
               	var food_data = '';
               	for(var i in window.record.food){
               	    food_data += window.record.food[i].x + ',' + window.record.food[i].y + (i != window.record.food.length - 1 ? ',' : '');
               	}
                document.form_list.food.value = food_data;
                document.form_list.move.value = window.record.move.toString();
				document.form_list.submit();
            }
			window.init();
		}
	}

	//初始化贪吃蛇属性、表格重绘、设置食物
	function init() {
        if(this.userinfo == null) this.userinfo = {name:"<?php echo $getcontent ?>", score:0, ranking:0};
		this.table = document.getElementById("game_div");
		this.map_width = 10;
		this.map_height = 10;
		this.snake = [{x:2, y:2}];
        this.last_dir = 6;
		this.dir = 6;
		this.food = {x:0, y:0};
        this.record = {food:[],move:[]}; //录制回放
        window.record.move.unshift(window.snake[0].x, window.snake[0].y); //将初始点添加到移动记录
        this.gm = false;	//管理员测试,四方向任意行走、无视身体相撞

		if (window.table.rows.length == 0) {
			for (var r = 0; r < window.map_width; r++) {
				var tr = window.table.insertRow();
				for (var d = 0; d < window.map_height; d++) {
					var td = tr.insertCell();
				}
			}
		} else {
			for (var r = 0; r < window.map_width; r++) {
				for (var d = 0; d < window.map_height; d++) {
					window.table.rows[r].cells[d].style.backgroundColor = "white";
				}
			}
		}
		window.set_food();
	}

	//起始点
	window.onload = function() {
		init();
        update();
		setInterval(move, 200);
	}
</script>

</html>
