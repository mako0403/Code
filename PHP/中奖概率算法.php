<?php

// 奖项设置
// ** 必须保证相同排序 即 依据概率正序排序
$prize_arr = array( 
    '0' => array('id'=>1,'prize'=>'平板电脑','probability'=>1,'already'=>0), 
    '1' => array('id'=>2,'prize'=>'数码相机','probability'=>5,'already'=>0), 
    '2' => array('id'=>3,'prize'=>'音箱设备','probability'=>10,'already'=>0), 
    '3' => array('id'=>4,'prize'=>'4G优盘','probability'=>12,'already'=>0), 
    '4' => array('id'=>5,'prize'=>'10Q币','probability'=>22,'already'=>0), 
    '5' => array('id'=>6,'prize'=>'下次没准就能中哦','probability'=>50,'already'=>0), 
); 

// 所有奖项probability总和为总投放数量
// already为该奖品已被中次数
// 无论如何 最后一个奖项的中奖概率为百分之百

// ** 实际总将概率为 probaility-already

// 必须依据概率大小正序排序  10,100,120,150
$proArr = array('id'=>'probability','id'=>'probability','id'=>'probability','id'=>'probability');

function get_prize_rand($proArr) { 
    $result = ''; 
 
    //概率数组的总概率精度 
    $proSum = array_sum($proArr);  // 总投放数量
	
	// 总奖品数量`
	$num = count($proArr);
    //奖项概率数组循环 
    foreach ($proArr as $key => $proCur) {  // proCur 即为该奖项概率
		// 当循环至最后一个奖品仍未中奖时，最后一个奖品直接中奖
		if($key != $num){
			$randNum = mt_rand(1, $proSum);  // 1到总投放数量直接的随机数。
			// 概率小于已中次数 且 随机数小于概率减已中次数，即表示中奖
			if ($proArr[$key]['already']<$proCur || $randNum <= ($proCur-$proArr[$key]['already'])){
				$result = $key;  // 奖品ID
				break; 
			}
		}else{
			$result = $key;
		}
    } 
    return $result; // 输出已中奖品ID
} 



// 前端页面请求ACTION

// 循环奖品数组， id=>概率
foreach ($prize_arr as $key => $val) { 
    $arr[$val['id']] = $val['v']; 
} 
 
$rid = get_rand($arr); //根据概率获取奖项id 
 
$res['yes'] = $prize_arr[$rid-1]['prize']; //中奖项 
unset($prize_arr[$rid-1]); //将中奖项从数组中剔除，剩下未中奖项 
shuffle($prize_arr); //打乱数组顺序 
for($i=0;$i<count($prize_arr);$i++){ 
    $pr[] = $prize_arr[$i]['prize']; 
} 
$res['no'] = $pr; 
echo json_encode($res); 



