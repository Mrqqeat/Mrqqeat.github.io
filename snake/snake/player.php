<?php

	$name = $_REQUEST['name'];
	$score = $_REQUEST['score'];
	$food = $_REQUEST['food'];
	$move = $_REQUEST['move'];
	$exiting = false;
	$update = false;

	if($name != "" && $name != null && $score > 0){
        
        // 名称=>成绩=>记录食物=>记录移动=>最后时间=>IP	\n换行符分割
        
        //读取旧的
        $arr_data = explode("\n", file_get_contents("./player.ini")); //行分割
        //减去最后一行空行判断
        for($i = 0; $i < count($arr_data) - 1; $i++){
            $arr = explode("=>", $arr_data[$i]);
			if($name == $arr[0]) $exiting = true;
            if($name == $arr[0] && intval($score) > intval($arr[1])){ //如果新数据比旧数据分数高,则更新
                $arr[1] = $score;
                $arr[2] = $food;
                $arr[3] = $move;
                $arr[4] = date("Y-m-d H:i:s");
                $update = true; //已存在,更新记录
            }
            $all_data = $all_data.$arr[0].'=>'.$arr[1].'=>'.$arr[2].'=>'.$arr[3].'=>'.$arr[4].'=>'.$arr[5]."\n";
	    }
        
        //有已存在的玩家则更新,否则新加入
        if($exiting){
			if($update){
				$myfile = fopen("./player.ini", "w") or die("Unable to open file!");
				fwrite($myfile, $all_data);
				fclose($myfile);
			}
        } else {
            $myfile = fopen("./player.ini", "a+") or die("Unable to open file!");
            fwrite($myfile, $name.'=>'.$score.'=>'.$food.'=>'.$move.'=>'.date("Y-m-d H:i:s").'=>'.$_SERVER["REMOTE_ADDR"]."\n");
            fclose($myfile);
        }

    }
	echo "<script>document.location = 'snake.php'</script>";

?>