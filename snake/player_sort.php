<?php

	$sort = $_GET['type'];
	$pass = $_GET['pass'];

	if($sort == 'sort' && $pass == '761205'){
        
        // 名称=>成绩=>记录食物=>记录移动=>最后时间=>IP	\n换行符分割
        
		$s = new SaeStorage();
        $arr_data = explode("\n", $s->read('snake','player.ini')); //行分割
        for($i = 0; $i < count($arr_data) - 1; $i++){
            $arr = explode("=>", $arr_data[$i]);
            $all_data = $all_data.$arr[0].'=>'.$arr[1].'=>'.$arr[2].'=>'.$arr[3].'=>'.$arr[4].'=>'.$arr[5]."\n";
	    }
		$s->write('snake', 'player.ini', $all_data);
        
        echo "<script>alert('排序成功！');document.write('排序成功！');</script>";
    }else{
        echo "<script>alert('密码错误！');document.write('密码错误！');</script>";
    }

?>