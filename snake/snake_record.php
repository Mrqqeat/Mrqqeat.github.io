<!DOCTYPE html>
<html>
<head>
<title>贪吃蛇_回放</title>

<meta name="keywords" content="keyword1,keyword2,keyword3">
<meta name="description" content="this is my page">
<meta name="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
<link rel="stylesheet" type="text/css" href="./css/style_controls.css">
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
        <h1 align="center">回放中(Shift键加速)...<a href='snake.php' align='center' >返回到主游戏</a></h1>
        <div style="float: left;">
			<table id="game_div" class="game_table">
			</table>
        </div>
		
		<!-- 获取排行榜 -->
    	<div style="float: left; border: 1px solid black; border-left:0px">
    		<h1 id="score" style="width:330px; height:25px; color: green;" align="center">分数：0</h1>
    		<div style="overflow-x: auto; overflow-y: auto; height: 430px; width:330px;">
				<table id="table_player_list" class="game_player_list">
					<tr><td	colspan="2">排行榜(0)</td></tr>
    			    <tr><td	colspan="2"><button class="green_btn" onclick="update()" >刷新</button></td></tr>
				</table>
    		</div>
    	</div>
    </div>

</body>

<!--<script type='text/javascript' src="player.php"></script>-->
<?php
	$s = new SaeStorage();
    $arr = explode("\n", $s->read('snake','player.ini'));
    // 去掉换行符使用 | 合并到一起
    foreach ($arr as $k => $v) {
        if($k!=count($arr)-1){
            //分割数据
            $data = explode("=>", $v);
            //取数据(name,score)
        	$player_list = $player_list.$data[0].'=>'.$data[1].($k!=count($arr)-2?'|':'');
            //name相同，获取回放数据(食物,移动)
            if($data[0] === $_GET['name']){ $record_data = $data[2].'|'.$data[3]; }
        }
    }
?>
<script type="text/javascript">
    
    //两倍速播放(手机用暂定)
    function test(){
    	clearInterval(window.inter_id);
        window.inter_id = setInterval(move, 100);
    }
    
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
            player_table.rows[0].cells[0].innerHTML = "排行榜(" + data.length + ")";
            if(data.length != 0) data.sort(function(obj1,obj2){return parseInt(obj1.score) - parseInt(obj2.score);});
        	for(var i=0;i<data.length;i++){
        	    var row = player_table.insertRow(1);
        	    var name = row.insertCell();
        	    var score = row.insertCell();
                name.innerHTML = "<a href='snake_record.php?name=" + data[i].name + "'>" + data[i].name + "</a>";
        	    score.innerHTML = data[i].score;
        		//alert(player_list[i]);
        	}
        	player_table.rows[player_table.rows.length-1].cells[0].children[0].innerHTML = "刷新";
        }, 300);
    }

	//设置食物
	function set_food() {
        document.getElementById("score").innerHTML = "分数：" + (window.snake.length - 1);
        //设置回放食物,并删除
        if(window.record.food.length == 0) return;
        window.food.x = window.record.food[0].x;
        window.food.y = window.record.food[0].y;
        window.table.rows[window.food.y].cells[window.food.x].style.backgroundColor = "blue";
        window.record.food.splice(0,1);
	}
    
	//监听按键
	document.onkeydown = KeyPress;
	function KeyPress() {
		var ie;
		var firefox;
		if (document.all)
			ie = true;
        else
			ie = false; //判断是否IE
		var key;
		if (ie) {
			key = event.keyCode;
		} else {
			key = KeyPress.arguments[0].keyCode;
		}
		//alert(key); //弹出对话框显示 键盘的数字值
        if(key == 16)move(); //Shift回放加速
	}
    
    //判断是否可通行
    function snake_passable(x, y, dir){
        x = x + (dir == 4 ? -1 : dir == 6 ? 1 : 0);
        y = y + (dir == 8 ? -1 : dir == 2 ? 1 : 0);
		return !(y < 0 || y >= window.table.rows.length || x < 0 || x >= window.table.rows[y].cells.length || window.table.rows[y].cells[x].style.backgroundColor == "green");
    }

	//移动
	function move() {
        //判断最后一步为阵亡
        if(window.record.move.length == 1){alert('该玩家已阵亡,分数：' + (window.snake.length - 1));window.record.move = []}
        if(window.record.move.length == 0)return;
        //设置回放方向,并删除
        window.dir = parseInt(window.record.move.shift());
        
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
			set_food();
		}
        
        //游戏结束
		if (lose) {
            //正常流程死亡时执行,不过这是回放,所以不用在这里死亡检测,直接检测是否是最后一步,不用判断任何死亡,只管播放记录即可.
           	//alert('die');
		}
	}

	//初始化贪吃蛇属性、表格重绘、设置食物
	function init() {
		this.table = document.getElementById("game_div");
		this.map_width = 10;
		this.map_height = 10;
        this.snake = [{x:window.x, y:window.y}]; //回放时的初坐标
		this.dir = 6;
		this.food = {x:0, y:0};

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
        //取得回放数据：食物、移动
        var r = "<?php echo $record_data ?>".split('|');
        //赋值食物
        var fd = r[0].split(',');
        var fd_data = [];
        for(var i=0;i<fd.length/2;i++)fd_data.push({x:fd[i*2], y:fd[i*2+1]});
        //赋值移动
        var mo = r[1].split(',');
        window.x = parseInt(mo[0]);	//初期X坐标
        window.y = parseInt(mo[1]);	//初期Y坐标
        var mo_data = [];
        for(var i=2;i<mo.length;i++)mo_data.push(parseInt(mo[i]));
        this.record = {food:fd_data, move:mo_data};	//回放
        //很普通的初始化
		init();
        update();
		this.inter_id = setInterval(move, 200);
	}
</script>

</html>
