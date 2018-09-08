<?php

	$name = $_REQUEST['name'];
	$score = $_REQUEST['score'];
	$food = $_REQUEST['food'];
	$move = $_REQUEST['move'];
	$ip = $_SERVER["REMOTE_ADDR"];
	$illegal = false;

	$name = preg_replace('/^(&nbsp;|\s)*|(&nbsp;|\s)*$/', '', $name);
	if($name != "" && $name != null && $score > 0){
        
        // 写入初始化
        $s = new SaeStorage();
        
        // 检测是否被封
        $arr = explode("\n", $s->read('snake','ban_ip.ini'));
    	foreach($arr as $k => $v){
    		if($ip == explode('=>', $v)[1]){ die('<script>document.location = "about:blank";window.alert("您已被封停，无法提交数据！");</script>'); }
		}
        
        // 检测名称非法
		$words = ['屄','肏','屌','婊','淫','尻','贱','腚','嫖','娼','妓','妈','娘','爸','爹','爷','奶','操','滚','骚','逼','睾','乳','茎','裸','尿','屎',
                  '煞笔','傻逼','狗逼','垃圾','智障','鸡巴','我草','尼玛','孙子','儿子','废物','子宫','阴毛','阴水','处女','精液','精子','狗养','自慰','肥逼','粉穴','颜射','肉棒','肉棍','阴核','插阴','乱伦','性交','做爱','色诱','叫床','人妻','内射','群交','幼女',
                  '萝莉','写真','艳照','18禁','荡妇','吸精','无码','逼毛','性爱','小便','大便','生殖','猥琐','女干','口交','开房','外挂','成人','小B','崽子',
                  '苍井空',
                  'SB','JB','J8','fuck'];
    	for($i = count($words) - 1; $i >= 0; $i--){
            if(strstr($name, $words[$i])){ $illegal = true; break; }
		}
        
        // 处理非法
        if($illegal){
            // 名称=>IP \n换行符分割
        	$old_data = $s->read('snake','ban_ip.ini');
            $s->write('snake', 'ban_ip.ini', $old_data.$name.'=>'.$ip."\n");
        }else{
        	// 名称=>成绩=>记录食物=>记录移动=>最后时间=>IP	\n换行符分割
        	$old_data = $s->read('snake','player.ini');
            // 名称的特殊字符的一些处理。
            $name = rtrim($name);
			$s->write('snake', 'player.ini', $old_data.str_replace('=>', '', $name).'=>'.str_replace('=>','',$score).'=>'.str_replace('=>','',$food).'=>'.str_replace('=>','',$move).'=>'.date("Y-m-d H:i:s").'=>'.$ip."\n");
        }
        
    }
	echo "<script>document.location = 'snake.php';".($illegal ? 'window.alert("名称含有敏感词汇！");' : '')."</script>";

?>