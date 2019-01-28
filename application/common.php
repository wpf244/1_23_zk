<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * 删除图片
 */
function deleteImg($oldImg){
    if($oldImg != ''){
        $path = ROOT_PATH . 'public' . DS .$oldImg;
        if ($path != ROOT_PATH . 'public' . DS) {
            if(is_file($path) == true) {
                unlink($path);
            }
        }
    }
}
/**
 * 上传图片
 * **/
function uploadss($image){
    $file = request()->file("$image");
    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
    $pa=$info->getSaveName();
    $path=str_replace("\\", "/", $pa);
    $paths='/uploads/'.$path;

    return $paths;
}
function uploads($image){
    $file = request()->file("$image");
    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
    $pa=$info->getSaveName();
    $path=str_replace("\\", "/", $pa);
    $paths=$path;
    $images=\think\Image::open(ROOT_PATH.'/public/uploads/'.$paths);
    $images->save(ROOT_PATH.'/public/uploads/'.$paths,null,60,true);

    return $paths;
}
/**
 * 检测目录读写权限
 * @param unknown $dir_path
 */
function check_dir_iswritable($dir_path) {
    // 目录路径
	$dir_path = str_replace("\\", "/", $dir_path);
	// 是否可写
	$is_writale = 1;
	// 判断是否是目录
    if(!is_dir($dir_path)){ 
		$is_writale=0; 
		return $is_writale; 
	}else{ 
	    $fp = fopen("$dir_path/test.txt", 'w');
        if($fp) {
            fclose($fp);
            unlink("$dir_path/test.txt");
            $writeable = 1; 
        } else {
            $writeable = 0;
        }
	} 
	return $is_writale;
}
/**
 * 发送短信
 * */
function Post($phone,$code){ 
    $post_data = array();
    $post_data['userid'] = 10267;
    $post_data['account'] = '游戏';
    $post_data['password'] = '123456';
    $post_data['content'] = '【天界2】您的验证码为'.$code.'，请您在5分钟内完成操作。'; //短信内容需要用urlencode编码下
    $post_data['mobile'] = "$phone";
    $post_data['sendtime'] = ''; //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
    
    $url='http://114.55.11.126:8888/sms.aspx?action=send';
    $o='';
    foreach ($post_data as $k=>$v)
    {
        $o.="$k=".urlencode($v).'&';
    }
    $post_data=substr($o,0,-1);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果需要将结果直接返回到变量里，那加上这句。
    $result = curl_exec($ch);
    var_dump($result);exit;

}
function makeArr($data,&$res,$id=0,$j=0){
    foreach($data as $v){
        if($v['parentid']==$id){
            $temp=$v;
            $temp['id']=$temp['id'];
            $temp['name']=$temp['name'];
            $temp['parentid']=$temp['parentid'];
            $temp['level']=$temp['level'];
            $temp['i']=$j;
            $res[]=$temp;
            makeArr($data,$res,$v['id'],$j+1);
        }
    }
 }
 function Code($url){
    vendor('phpqrcode.phpqrcode');
    //生成二维码图片
    $object = new \QRcode();
    $level=3;
    $size=6;
    $file_path = "./qrcode";
    if(!file_exists($file_path)){
        mkdir($file_path);
    }

    $filename = time() . '.jpg';
    $ad = $file_path . '/' . $filename;
    $wx_file_img = '/qrcode/' . $filename;
    $errorCorrectionLevel =intval($level);//容错级别
    $matrixPointSize = intval($size);//生成图片大小
    $object->png($url,$ad, $errorCorrectionLevel, $matrixPointSize, 2);
    return $wx_file_img;
}
function getYun($ip)
{
        
            
    $ipc =$this-> file_get_content('http://api.map.baidu.com/location/ip?ak=417ce9724fd28ab04c17eb88913738db&ip='.$ip);
    
    $iparr=explode("|",$ipc);
    
    $iparr[4];

   

    switch ($iparr[4])
    {

    case "CHINANET":
        
    $dx="中国电信";
    break;

    case "UNICOM":
    $dx="中国联通";
        
    break;
        
    case "CMNET":
        
    $dx="中国移动";
        
    break;
        
    case "CRTC":
        
    $dx="中国铁通";
        
    break;
        
    case "COLNET":
        
    $dx="有线通";
        
    break;
        
    case "CERNET":
        
    $dx="教育网";
        
    break;
        
    case "CNCGROUP":
        
    $dx="网通";
        
    break;
        
    default:
        
    $dx="未知电信运营商";
    }
    return $dx;
          
}