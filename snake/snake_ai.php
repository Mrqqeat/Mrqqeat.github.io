<!DOCTYPE html>
<html>
<head>
<title>贪吃蛇</title>

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
        <h1 style="margin-bottom:-15px;" align="center">AI进行中(Shift键加速)...<a href='snake.php' align='center' >返回到主游戏</a></h1>
        <p style="font-size:25px; margin-bottom:10px;" align="center">求助算法老师来拯救这个傻瓜AI...死循环、单个空隙等...</p>
        <div style="float: left;">
			<table id="game_div" class="game_table">
			</table>
        </div>
		
		<!-- 获取排行榜 -->
    	<div style="float: left; border: 1px solid black; border-left:0px">
    		<h1 id="score" style="width:330px; height:25px;" align="center">分数：0</h1>
    		<div style="overflow-x: auto; overflow-y: auto; height: 430px; width:330px;">
				<table id="table_player_list" class="game_player_list">
					<tr><td	colspan="2">排行榜(0)</td></tr>
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

</body>

<!--<script type='text/javascript' src="player.php"></script>-->
<?php
    //玩家来自哪里(暂无什么卵用)
    //$from = $_GET['from'];
	$s = new SaeStorage();
    $arr = explode("\n", $s->read('snake','player.ini'));
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
            player_table.rows[0].cells[0].innerHTML = "排行榜(" + data.length + ")";
            if(data.length != 0) data.sort(function(obj1,obj2){return parseInt(obj1.score) - parseInt(obj2.score);});
            window.player_names = [];
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
		}while(!snake_passable(window.food.x, window.food.y, 0));
		window.table.rows[window.food.y].cells[window.food.x].style.backgroundColor = "blue";
        window.record.food.push({x:window.food.x, y:window.food.y}); //记录食物
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
    
    //坐标类
    function Point(x, y, g, h, d){
        this.x = x;	//X坐标
        this.y = y;	//Y坐标
        this.g = g;	//G
        this.h = h;	//H
        this.d = d;	//方向
    }
    
    //判断是否可通行
    function snake_passable(x, y, dir){
        x = x + (dir == 4 ? -1 : dir == 6 ? 1 : 0);
        y = y + (dir == 8 ? -1 : dir == 2 ? 1 : 0);
        if(x == window.snake[window.snake.length-1].x && y == window.snake[window.snake.length-1].y)return true;	//尾巴可以吃
		return !(y < 0 || y >= window.table.rows.length || x < 0 || x >= window.table.rows[y].cells.length || window.table.rows[y].cells[x].style.backgroundColor == "red" || (!window.gm && window.table.rows[y].cells[x].style.backgroundColor == "green"));
    }
    
    //AI寻路算法
    function AI_move(origin, target){
    	var open = [origin]; //已发现,但未被搜索的节点
        var close = [];	//已被搜索的节点
        //open是否存在某节点
        var include_open = function(pos_x, pos_y){
            for(var i in open) if(open[i].x == pos_x && open[i].y == pos_y) return i;
            return false;
        }
        //close是否存在某节点
        var include_close = function(pos_x, pos_y){
            for(var i in close) if(close[i].x == pos_x && close[i].y == pos_y) return i;
            return false;
        }
        //记录的节点不为空
        while(open.length != 0){
            //按F值最小的排序
            open.sort(function(obj1,obj2){
                if(parseInt(obj1.g + obj1.h) == parseInt(obj2.g + obj2.h))
                	return parseInt(obj1.h) > parseInt(obj2.h);
                return parseInt(obj1.g + obj1.h) > parseInt(obj2.g + obj2.h);
            });
            //删除并返回第一个元素(当前节点),因为sort排序后,F值最小的永远在前面
        	nod = open.shift();
            //遍历四个方向
            for(var d in [2,4,6,8]){
                d = [2,4,6,8][d];
                //当前方向不可通行,进入下一个方向判断
                if(!snake_passable(nod.x, nod.y, d)) continue;
                
                //获取向指定方向移动后的坐标(子节点)
                var x = nod.x + (d == 4 ? -1 : d == 6 ? 1 : 0);
                var y = nod.y + (d == 8 ? -1 : d == 2 ? 1 : 0);
                open_index = include_open(x, y);
                close_index = include_close(x, y);
                //open已存在该节点
                if(open_index !== false){
                    //更新最短距离、防止死循环
                    if(nod.g + 10 < open[open_index].g || (nod.g + 10 == open[open_index].g && randomNum(1,100) > 80)){
                    	open[open_index].d = 10 - d; //将该节点指向新的父节点
                        open[open_index].g = nod.g + 10; //以新的父节点的基础上计算新的G距离(10或14)
                    }
                //close不存在该节点
                }else if(close_index === false){
                    //既然nod的指定方向没有在close表中，也就是没有获得这个位置的节点，那么就获取它
                	child = new Point(x, y);
                    child.d = 10 - d; //方向指向父节点，从2(下)传过来的就指向8(上)
                    child.g = nod.g + 10; //与起始点的距离，只有上下左右四个面，所以都是10，斜角是14
                    child.h = Math.abs(x - target.x) + Math.abs(y - target.y); //当前节点与终点的距离
                    open.push(child); //添加节点
                }
            }
            //将上面从open取出的nod节点加入close,说明已经被探索过
            close.push(nod);
            //如果目标点已经在close表中了,说明目标点已经被探索获取到了,中断循环
            if(include_close(target.x, target.y) !== false) break;
        }
   		// 结束循环有两个可能
   		// 1.找到了终点,中断循环
   		// 2.open表空了,找不到出发点,为路径不存在
   		// 所以,这里进行判断,如果close表中没有终点,那么路径不存在,返回一个空路径
        if(include_close(target.x, target.y) === false) return [];
        var routes = []; //路线
    	// 找到目标点，然后一步一步到起点的方向
        nod = close[include_close(target.x, target.y)];
    	// 开始循环 直到抵达目标点
    	while(nod.x != origin.x || nod.y != origin.y){
    	  // 把当前节点的方向添加到路径表的尾部
    	  // 这样做 完成以后 把路径表从头到尾读一遍就是移动方向了
    	  // 类似于“上右上右右上右右下”这样
    	  routes.unshift(10 - nod.d);
    	  // 记录下来以后 向那个方向移动一步
    	  x = nod.x + (nod.d == 4 ? -1 : nod.d == 6 ? 1 : 0)
    	  y = nod.y + (nod.d == 8 ? -1 : nod.d == 2 ? 1 : 0)
    	  // 然后获取移动后所在位置的节点 作为当前节点
          nod = close[include_close(x, y)];
    	  // 回去循环
        }
        return routes;
    }
    
    //AI向尾巴走一步
    function to_tail_move(x, y){
        var tail_dir = [0,0,0]; //离食物的距离，方向，紧跟
        var tmx,tmy,tail_x,tail_y,tail_bet;
        for(var dir in [2,4,6,8]){
        	dir = [2,4,6,8][dir];
            if(!snake_passable(x, y, dir)) continue;
            tmx = x + (dir == 4 ? -1 : dir == 6 ? 1 : 0);
    	 	tmy = y + (dir == 8 ? -1 : dir == 2 ? 1 : 0);
            tail_x = window.snake[window.snake.length-1].x;
            tail_y = window.snake[window.snake.length-1].y;
            tail_bet = Math.abs(tmx - window.food.x) + Math.abs(tmy - window.food.y);
            
            //上下左右四个方向，取能找到尾巴并且离食物最远的方向
            if(tmx == tail_x && tmy == tail_y) tail_dir[2] = dir;
            if((AI_move(new Point(tmx, tmy, 0), new Point(tail_x, tail_y)).length != 0 || tail_dir[2] != 0) && tail_bet > tail_dir[0]){
              tail_dir[0] = tail_bet;
          	  tail_dir[1] = dir;
              if(tail_dir[2] != 0) tail_dir[1] = tail_dir[2];
            }
        }
    	return [tail_dir[1]];
    }
    
	//移动
	function move() {
        //获取头部到食物的路线
    	window.dirs = AI_move(new Point(window.snake[0].x, window.snake[0].y, 0), new Point(window.food.x, window.food.y));
        //检测有尾巴的情况
        if(window.snake.length > 1){
            //向方向移动后的目标坐标(虚拟蛇头)
       		var tmx = window.snake[0].x + (window.dirs[0] == 4 ? -1 : window.dirs[0] == 6 ? 1 : 0);
       		var tmy = window.snake[0].y + (window.dirs[0] == 8 ? -1 : window.dirs[0] == 2 ? 1 : 0);
            var tail_x = window.snake[window.snake.length-1].x;
            var tail_y = window.snake[window.snake.length-1].y;
            //虚拟蛇可以找得到食物 且 移动后可以找得到尾巴
            if(window.dirs.length != 0 && AI_move(new Point(tmx, tmy, 0), new Point(tail_x, tail_y)).length != 0){
                window.dirs = AI_move(new Point(window.snake[0].x, window.snake[0].y, 0), new Point(window.food.x, window.food.y));
            //下一步虚拟蛇不能到食物或者不能到尾巴，就真实蛇向可以到尾巴且离食物最远的方向走一步
            }else{
            	window.dirs = to_tail_move(window.snake[0].x, window.snake[0].y);
            }
        }
        
        //设置寻路方向
        if(window.dirs.length != 0) window.dir = parseInt(window.dirs.shift());
        window.record.move.push(window.dir); //记录方向
        
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
				window.table.rows[window.snake[i].y].cells[window.snake[i].x].style.backgroundColor = (i == window.snake.length - 1 ? "pink" : "green");
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
            alert('AI阵亡了！');
            window.init();
		}
	}
    
	//初始化贪吃蛇属性、表格重绘、设置食物
	function init() {
		this.table = document.getElementById("game_div");
		this.map_width = 10;
		this.map_height = 10;
		this.snake = [{x:5, y:5}];
		this.dir = 6;
        this.dirs = [];
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
		setInterval(move, 100);
	}
</script>

</html>
