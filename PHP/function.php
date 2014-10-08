<?php
// +----------------------------------------------------------------------
// | 通用函数库 与业务无关
// +----------------------------------------------------------------------
// | The project is protected by copyright, is forbidden without authorization or secondary release;
// +----------------------------------------------------------------------
// | Copyright Mako ( http://www.liuhai.org )
// +----------------------------------------------------------------------
// | Licensed ( http://www.liuhai.org )
// +----------------------------------------------------------------------
// | Author: Mako <Work@Liuhai.org>
// +----------------------------------------------------------------------
// 

/**
 * 返回经addslashes处理过的字符串或数组
 * addslashes 在指定的预定义字符前添加反斜杠。
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string){
  if(!is_array($string)) return addslashes($string);
  foreach($string as $key => $val) $string[$key] = new_addslashes($val);
  return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * stripslashes 删除由 addslashes() 函数添加的反斜杠。
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
  if(!is_array($string)) return stripslashes($string);
  foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
  return $string;
}

/**
 * 返回经htmlspecialchars处理过的字符串或数组
 * htmlspecialchars 把一些预定义的字符转换为 HTML 实体。
 * @param $obj 需要处理的字符串或数组
 * @return mixed
 */
function new_html_special_chars($string) {
  if(!is_array($string)) return htmlspecialchars($string);
  foreach($string as $key => $val) $string[$key] = new_html_special_chars($val);
  return $string;
}

/**
 * 对数组进行搜索
 * @param $list  要搜索的数组
 * @param $condition 搜索条件
 *      支持 array('name'=>$value) 或者 name=$value
 * @param $return_type true 输出一维数组, flase 输出多维数组
 * @return  array
 */
function search_array($list, $condition, $return_type=true) {
    if (is_string($condition))
        parse_str($condition, $condition);
    $resultSet = array();
    foreach ($list as $key => $data) {
        $find = false;
        foreach ($condition as $field => $value) {
            if (isset($data[$field])) {
                if (0 === strpos($value, '/')) {
                    $find = preg_match($value, $data[$field]);
                } elseif ($data[$field] == $value) {
                    $find = true;
                }
            }
        }
        if ($find)
            $return_type ? $resultSet=&$list[$key] : $resultSet[]=&$list[$key];
    }
    return $resultSet;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

// 验证数组是否完全为空 (一维或多维数组的值)
// return bool
function check_array($array){
    if(is_array($array)){
        foreach($array as $val){
           $str.=check_array($val);  
        }
    }else{
        $str=$array;
    }
    if($str==NULL || !$str || $str==false){
        return false;
    }
    return true;
}


/**
 * CURL POST 请求
 * @param  [type]  $url        [请求目标地址]
 * @param  [type]  $data       [发送数据]
 * @param  string  $cookiepath [description]
 * @param  integer $timeout    [超时]
 * @return [type]              [description]
 */
function curl_post_contents($url, $data, $cookiepath = '',$timeout=10){
    $userAgent = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1664.3 Safari/537.36';
    $referer = $url;
    if(!$data || !$url) return false;
    $post = '';
    if(is_array($data)){
      foreach($data as $key => $value){
        $post .= $key . '=' . urlencode($value) . '&';
      }
      rtrim($post, '&');
    }else{
      $post = $data;
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);        //设置访问的url地址
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);    //设置超时
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);  //用户访问代理 User-Agent
    //curl_setopt($ch, CURLOPT_REFERER, $referer);    //在HTTP请求中包含一个'referer'头的字符串
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);    //跟踪301
    curl_setopt($ch, CURLOPT_POST, 1);          //指定post数据
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);    //添加变量
    //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath); //COOKIE的存储路径,返回时保存COOKIE的路径
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //返回结果
    if(!is_array($data)){
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    }

    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
}

/**
 * CURL GET 数据请求
 * @param  [type]  $url     [description]
 * @param  integer $timeout [description]
 * @return [type]           [description]
 */
function curl_get_contents($url, $timeout = 5){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    @curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, true);
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 86400); // 缓存一天
    
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}


// 判断是否是手机
function is_mobile() {
  if(empty($_SERVER['HTTP_USER_AGENT'])) {
    $is_mobile = false;
  }
  elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
    || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
    || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
    || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
    || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
    || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
    || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
      $is_mobile = true;
  }else{
    $is_mobile = false;
  }
  return $is_mobile;
}


 /**
 * 检测输入字符串是否含有特殊字符
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
    $badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
    foreach($badwords as $value){
        if(strpos($string, $value) !== FALSE) {
            return TRUE;
        }
    }
    return FALSE;
}

/**
 * 检查用户名是否合法
 *
 * @param STRING $username 要检查的用户名
 * @return  TRUE or FALSE
 */
function is_username($username) {
  $strlen = str_len($username);
  if(is_badword($username) || !preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_]+[a-zA-Z0-9]$/", $username)){
    return false;
  } elseif ( 18 < $strlen || $strlen < 3 ) {
    return false;
  }
  return true;
}

/**
 * 检查email是否合法
 * @param $email
 */
function is_email($email){
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //hash加密随机字符串
 * @return array/password  如果传入encrypt则只输出密码, 否则输出一个数组包含密码和加密串
 */
function password($password, $encrypt='') {
  $pwd = array();
  $pwd['encrypt'] =  $encrypt ? $encrypt : rand_string(32);
  $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
  return $encrypt ? $pwd['password'] : $pwd;
}

/**
 * 检查密码长度是否符合规定
 * @param STRING $password
 * @return  bool
 */
function is_password($password) {
  $strlen = strlen($password);
  if($strlen >= 6 && $strlen <= 30) return true;
  return false;
}

/**
 * UTF-8模式下转换一个汉字按2个字符计算
 * @param $str 要进行转换的字符串
 * @return  string 输出字数
 */
function str_len($str){
    $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));
    if ($length){
        return strlen($str) - $length + intval($length / 3) * 2;
    }else{
        return strlen($str);
    }
}

// header方式url条状
function jump($url){
   header('Location:'.$url);
     exit;
}

//js方式url跳转
function js_jump($url){
  exit("<script language=\"javascript\" type=\"text/javascript\">
           window.top.location.href=\"{$url}\"; 
    </script>");
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
  if(function_exists("mb_substr"))
    $slice = mb_substr($str, $start, $length, $charset);
  elseif(function_exists('iconv_substr')) {
    $slice = iconv_substr($str,$start,$length,$charset);
  }else{
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
  }
  return $suffix ? $slice.'...' : $slice;
}


/**
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len=6,$type='',$addChars='') {
  $str ='';
  switch($type) {
    case 0:
      $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
      break;
    case 1:
      $chars= mt_rand().str_repeat('0123456789',2).time();
      break;
    case 2:
      $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
      break;
    case 3:
      $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
      break;
    case 4:
      $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
      break;
    default :
      // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
      $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
      break;
  }
  if($len>10 ) {//位数过长重复字符串一定次数
    $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
  }
  if($type!=4) {
    $chars   =   str_shuffle($chars);
    $str     =   substr($chars,0,$len);
  }else{
    // 中文随机字
    for($i=0;$i<$len;$i++){
      $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1,'utf-8',false);
    }
  }
  return $str;
}

// 随机生成一组字符串
function build_count_rand ($number,$length=4,$mode=1) {
    if($mode==1 && $length<strlen($number) ) {
        //不足以生成一定数量的不重复数字
        return false;
    }
    $rand   =  array();
    for($i=0; $i<$number; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $unqiue = array_unique($rand);
    if(count($unqiue)==count($rand)) {
        return $rand;
    }
    $count   = count($rand)-count($unqiue);
    for($i=0; $i<$count*3; $i++) {
        $rand[] =   rand_string($length,$mode);
    }
    $rand = array_slice(array_unique ($rand),0,$number);
    return $rand;
}

/**
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 * @return string
 */
function byte_format($size, $dec=2) {
  $a = array("B", "KB", "MB", "GB", "TB", "PB");
  $pos = 0;
  while ($size >= 1024) {
     $size /= 1024;
       $pos++;
  }
  return round($size,$dec)." ".$a[$pos];
}

/**
 * 检查字符串是否是UTF8编码
 * @param string $string 字符串
 * @return Boolean
 */
function is_utf8($string) {
    return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]            # ASCII
       | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
       |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
       |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
       |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $string);
}

/**
 * 代码加亮
 * @param String  $str 要高亮显示的字符串 或者 文件名
 * @param Boolean $show 是否输出
 * @return String
 */
function highlight_code($str,$show=false) {
    if(file_exists($str)) {
        $str    =   file_get_contents($str);
    }
    $str  =  stripslashes(trim($str));
    // The highlight string function encodes and highlights
    // brackets so we need them to start raw
    $str = str_replace(array('&lt;', '&gt;'), array('<', '>'), $str);

    // Replace any existing PHP tags to temporary markers so they don't accidentally
    // break the string out of PHP, and thus, thwart the highlighting.

    $str = str_replace(array('&lt;?php', '?&gt;',  '\\'), array('phptagopen', 'phptagclose', 'backslashtmp'), $str);

    // The highlight_string function requires that the text be surrounded
    // by PHP tags.  Since we don't know if A) the submitted text has PHP tags,
    // or B) whether the PHP tags enclose the entire string, we will add our
    // own PHP tags around the string along with some markers to make replacement easier later

    $str = '<?php //tempstart'."\n".$str.'//tempend ?>'; // <?

    // All the magic happens here, baby!
    $str = highlight_string($str, TRUE);

    // Prior to PHP 5, the highlight function used icky font tags
    // so we'll replace them with span tags.
    if (abs(phpversion()) < 5) {
        $str = str_replace(array('<font ', '</font>'), array('<span ', '</span>'), $str);
        $str = preg_replace('#color="(.*?)"#', 'style="color: \\1"', $str);
    }

    // Remove our artificially added PHP
    $str = preg_replace("#\<code\>.+?//tempstart\<br />\</span\>#is", "<code>\n", $str);
    $str = preg_replace("#\<code\>.+?//tempstart\<br />#is", "<code>\n", $str);
    $str = preg_replace("#//tempend.+#is", "</span>\n</code>", $str);

    // Replace our markers back to PHP tags.
    $str = str_replace(array('phptagopen', 'phptagclose', 'backslashtmp'), array('&lt;?php', '?&gt;', '\\'), $str); //<?
    $line   =   explode("<br />", rtrim(ltrim($str,'<code>'),'</code>'));
    $result =   '<div class="code"><ol>';
    foreach($line as $key=>$val) {
        $result .=  '<li>'.$val.'</li>';
    }
    $result .=  '</ol></div>';
    $result = str_replace("\n", "", $result);
    if( $show!== false) {
        echo($result);
    }else {
        return $result;
    }
}

/**
 * 输出安全的html
 * @param string $text 字符串
 * @param string $tags 要过滤的html标签， 默认为空即可
 * @return Boolean
 */
function export_safe_html($text, $tags = null) {
  $text = trim($text);
  //完全过滤注释
  $text = preg_replace('/<!--?.*-->/','',$text);
  //完全过滤动态代码
  $text = preg_replace('/<\?|\?'.'>/','',$text);
  //完全过滤js
  $text = preg_replace('/<script?.*\/script>/','',$text);

  $text = str_replace('[','&#091;',$text);
  $text = str_replace(']','&#093;',$text);
  $text = str_replace('|','&#124;',$text);
  //过滤换行符
  $text = preg_replace('/\r?\n/','',$text);
  //br
  $text = preg_replace('/<br(\s\/)?'.'>/i','[br]',$text);
  $text = preg_replace('/<p(\s\/)?'.'>/i','[br]',$text);
  $text = preg_replace('/(\[br\]\s*){10,}/i','[br]',$text);
  //过滤危险的属性，如：过滤on事件lang js
  while(preg_match('/(<[^><]+)( lang|on|action|background|codebase|dynsrc|lowsrc)[^><]+/i',$text,$mat)){
    $text=str_replace($mat[0],$mat[1],$text);
  }
  while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
    $text=str_replace($mat[0],$mat[1].$mat[3],$text);
  }
  if(empty($tags)) {
    $tags = 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a';
  }
  //允许的HTML标签
  $text = preg_replace('/<('.$tags.')( [^><\[\]]*)>/i','[\1\2]',$text);
  $text = preg_replace('/<\/('.$tags.')>/Ui','[/\1]',$text);
  //过滤多余html
  $text = preg_replace('/<\/?(html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml)[^><]*>/i','',$text);
  //过滤合法的html标签
  while(preg_match('/<([a-z]+)[^><\[\]]*>[^><]*<\/\1>/i',$text,$mat)){
    $text=str_replace($mat[0],str_replace('>',']',str_replace('<','[',$mat[0])),$text);
  }
  //转换引号
  while(preg_match('/(\[[^\[\]]*=\s*)(\"|\')([^\2=\[\]]+)\2([^\[\]]*\])/i',$text,$mat)){
    $text=str_replace($mat[0],$mat[1].'|'.$mat[3].'|'.$mat[4],$text);
  }
  //过滤错误的单个引号
  while(preg_match('/\[[^\[\]]*(\"|\')[^\[\]]*\]/i',$text,$mat)){
    $text=str_replace($mat[0],str_replace($mat[1],'',$mat[0]),$text);
  }
  //转换其它所有不合法的 < >
  $text = str_replace('<','&lt;',$text);
  $text = str_replace('>','&gt;',$text);
  $text = str_replace('"','&quot;',$text);
   //反转换
  $text = str_replace('[','<',$text);
  $text = str_replace(']','>',$text);
  $text = str_replace('|','"',$text);
  //过滤多余空格
  $text = str_replace('  ',' ',$text);
  return $text;
}

function remove_xss($val) {
   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
   // this prevents some character re-spacing such as <java\0script>
   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
   $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

   // straight replacements, the user should never need these since they're normal characters
   // this prevents like <IMG SRC=@avascript:alert('XSS')>
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      // ;? matches the ;, which is optional
      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

      // @ @ search for the hex values
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      // @ @ 0{0,7} matches '0' zero to seven times
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }

   // now the only remaining whitespace attacks are \t, \n, and \r
   $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);

   $found = true; // keep replacing as long as the previous round replaced something
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
         if ($val_before == $val) {
            // no replacements were made, so exit the loop
            $found = false;
         }
      }
   }
   return $val;
}

/**
 * 自动转换字符集 支持数组转换
 * @param string $fContents 字符串
 * @param string $from 原字符集
 * @param string $to 目标字符集
 * @return Boolean
 */
function auto_charset($fContents, $from='gbk', $to='utf-8') {
    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
        //如果编码相同或者非字符串标量则不转换
        return $fContents;
    }
    if (is_string($fContents)) {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($fContents, $to, $from);
        } elseif (function_exists('iconv')) {
            return iconv($from, $to, $fContents);
        } else {
            return $fContents;
        }
    } elseif (is_array($fContents)) {
        foreach ($fContents as $key => $val) {
            $_key = auto_charset($key, $from, $to);
            $fContents[$_key] = auto_charset($val, $from, $to);
            if ($key != $_key)
                unset($fContents[$key]);
        }
        return $fContents;
    }
    else {
        return $fContents;
    }
}


/**
 * 压缩文件
 * @param string $path 需要压缩的文件[夹]路径
 * @param string $savedir 压缩文件所保存的目录
 * @return array zip文件路径
 */
function zip($path,$savedir) {
    $path=preg_replace('/\/$/', '', $path);
    preg_match('/\/([\d\D][^\/]*)$/', $path, $matches, PREG_OFFSET_CAPTURE);
    $filename=$matches[1][0].".zip";
    set_time_limit(0);
    $zip = new ZipArchive();
    $zip->open($savedir.'/'.$filename,ZIPARCHIVE::OVERWRITE);
    if (is_file($path)) {
        $path=preg_replace('/\/\//', '/', $path);
        $base_dir=preg_replace('/\/[\d\D][^\/]*$/', '/', $path);
        $base_dir=addcslashes($base_dir, '/:');
        $localname=preg_replace('/'.$base_dir.'/', '', $path);
        $zip->addFile($path,$localname);
        $zip->close();
        return $filename;
    }elseif (is_dir($path)) {
        $path=preg_replace('/\/[\d\D][^\/]*$/', '', $path);
        $base_dir=$path.'/';//基目录
        $base_dir=addcslashes($base_dir, '/:');
    }
    $path=preg_replace('/\/\//', '/', $path);
    function addItem($path,&$zip,&$base_dir){
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if (($file!='.')&&($file!='..')){
                $ipath=$path.'/'.$file;
                if (is_file($ipath)){//条目是文件
                    $localname=preg_replace('/'.$base_dir.'/', '', $ipath);
                    var_dump($localname);
                    $zip->addFile($ipath,$localname);
                } else if (is_dir($ipath)){
                    addItem($ipath,$zip,$base_dir);
                    $localname=preg_replace('/'.$base_dir.'/', '', $ipath);
                    var_dump($localname);
                    $zip->addEmptyDir($localname);
                }
            }
        }
    }
    addItem($path,$zip,$base_dir);
    $zip->close();
    return $filename;
}

/**
 * 解压文件
 * @param string $zip 压缩包路径
 * @param string $hedef 解压到的路径
 */
function ezip($zip, $hedef = ''){
    $dirname=preg_replace('/.zip/', '', $zip);
    $root = $_SERVER['DOCUMENT_ROOT'].'/zip/';
    $zip = zip_open($root . $zip);
    @mkdir($root . $hedef . $dirname.'/'.$zip_dosya);
    while($zip_icerik = zip_read($zip)){
        $zip_dosya = zip_entry_name($zip_icerik);
        if(strpos($zip_dosya, '.')){
            $hedef_yol = $root . $hedef . $dirname.'/'.$zip_dosya;
            @touch($hedef_yol);
            $yeni_dosya = @fopen($hedef_yol, 'w+');
            @fwrite($yeni_dosya, zip_entry_read($zip_icerik));
            @fclose($yeni_dosya); 
        }else{
            @mkdir($root . $hedef . $dirname.'/'.$zip_dosya);
        };
    };
}

/**
  * 全角字符转半角字符
  * @param string $str 待转换字串
  * @return string $str 处理后字串
*/
function full2half($str){
  $arr = array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
  '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
  'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
  'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
  'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
  'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
  'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
  'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
  'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
  'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
  'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
  'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
  'ｙ' => 'y', 'ｚ' => 'z',
  '（' => '(', '）' => ')', 
  '【' => '[','】' => ']', '〖' => '[', '〗' => ']', '“' => '"', '”' => '"',
  '｛' => '{', '｝' => '}', '《' => '<','》' => '>',
  '％' => '%', '＋' => '+', '—' => '-', '－' => '-',
  '：' => ':', '。' => '.', '，' => ',',
  '；' => ';', '？' => '?', '！' => '!', '‖' => '|',
  '｜' => '|', '〃' => '"');
  return strtr($str, $arr);
}

/**
  * 计算两个坐标之间的直线距离，返回单位为米的数值
  * @param string 分别传入两个坐标的lat lng
  * @return int
*/
function beelineDistance($lat1, $lng1, $lat2, $lng2){
  //d * pi() / 180.0; 计算弧度
  $Lat1 = ($lat1 * pi()) / 180.0; 
  $Lng1 = ($lng1 * pi()) / 180.0;
  $Lat2 = ($lat2 * pi()) / 180.0;
  $Lng2 = ($lng2 * pi()) / 180.0;
  $a = $Lat1 - $Lat2;
  $b = $Lng1 - $Lng2;
  $str = 2 * asin(sqrt(pow(sin($a/2),2) + cos($Lat1)*cos($Lat2)*pow(sin($b/2),2)));
  $str = $str * 6378.137; // 6378.137 地球半径，单位为公里
  $str = round($str * 10000) / 10000; // round()四舍五入 获得直径距离的公里数
  return round($str*1000); //输出单位为米
}

