<?php
namespace app\admin\controller;

class Index extends BaseAdmin
  {
   public  function index(){
        
        $this->getSystemConfig(); 

        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;
        
        //全部文章数量
        $qcou=db("article_info")->where(["arttype"=>"xinwen"])->count();
        $this->assign("qcou",$qcou);

        //已审核
        $ycou=db("article_info")->where(["arttype"=>"xinwen","reviewstatus"=>1])->count();
        $this->assign("ycou",$ycou);

        $ybai=round($ycou/$qcou*100,1);
        $this->assign("ybai",$ybai);

        //待审核
        $dcou=db("article_info")->where(["arttype"=>"xinwen","reviewstatus"=>0])->count();
        $this->assign("dcou",$dcou);

        $dbai=round($dcou/$qcou*100,1);
        $this->assign("dbai",$dbai);

        //被退回
        $bcou=db("article_info")->where(["arttype"=>"xinwen","reviewstatus"=>2])->count();
        $this->assign("bcou",$bcou);

        $bbai=round($bcou/$qcou*100,1);
        $this->assign("bbai",$bbai);

        //热门文章
        $hot=db("article_info")->field("id,title,clicks")->where(["arttype"=>"xinwen","reviewstatus"=>1])->order("clicks desc")->limit("0,10")->select();
        $this->assign("hot",$hot);

        //折线图
        $dates=$this->get_weeks();
        $dates=array_values($dates);
       
        $data=$this->get_cou();

        
        $this->assign("dates",json_encode($dates));
        $this->assign("data",json_encode($data));


        return view('index');
    }
    public function get_cou()
    {
        $data = array();
        $data[] = db("article_info")->whereTime("createtime","-6 days")->count();
        $data[] = db("article_info")->whereTime("createtime","-5 days")->count();
        $data[] = db("article_info")->whereTime("createtime","-4 days")->count();
        $data[] = db("article_info")->whereTime("createtime","-3 days")->count();
        $data[] = db("article_info")->whereTime("createtime","-2 days")->count();
        $data[] = db("article_info")->whereTime("createtime","-1 days")->count();
        
        $data[] = db("article_info")->whereTime("createtime","d")->count();
        
       
        
       
       
        
     
        return $data;
    }
    function get_weeks($time = '', $format='m-d'){
        $time = $time != '' ? $time : time();
        //组合数据
        $date = [];
        for ($i=1; $i<=7; $i++){
          $date[$i] = date($format ,strtotime( '+' . $i-7 .' days', $time));
        }
        return $date;
      }
      
    private function _deleteDir($R){
        $handle = opendir($R);
        while(($item = readdir($handle)) !== false){
            if($item != '.' and $item != '..'){
                if(is_dir($R.'/'.$item)){
                    $this->_deleteDir($R.'/'.$item);
                }else{
                    if(!unlink($R.'/'.$item))
                        die('error!');
                }
            }
        }
        closedir( $handle );
        return rmdir($R);
    }
    public function clearruntime(){
        if(input('user')==1){
            if($this->_deleteDir("../runtime/")){
                echo '1';
            }
        }
    }

     /**
     * 获取系统信息
     */
    public function getSystemConfig()
    {
        $system_config['os'] = php_uname(); // 服务器操作系统
        $system_config['server_software'] = $_SERVER['SERVER_SOFTWARE']; // 服务器环境
        $system_config['upload_max_filesize'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow'; // 文件上传限制             
        $system_config['port'] = $_SERVER['SERVER_PORT']; // 端口
        $system_config['dns'] = $_SERVER['HTTP_HOST']; // 服务器域名
        $system_config['php_version'] = PHP_VERSION; // php版本
        $system_config['openssl'] = extension_loaded('openssl'); //是否支付openssl
        $system_config['curl'] = function_exists('curl_init'); // 是否支持curl功能
        $system_config['upload_dir_jurisdiction'] = check_dir_iswritable(ROOT_PATH."public/uploads/"); // upload目录读写权限
        $system_config['runtime_dir_jurisdiction'] = check_dir_iswritable(ROOT_PATH."runtime/"); // runtime目录读写权限

        $this->assign("system_config", $system_config);
    }
    function modify(){
        if (! defined('CONTROLLER_NAME')) {
            define('CONTROLLER_NAME', $this->request->controller());
        }
        if (! defined('ACTION_NAME')) {
            define('ACTION_NAME', $this->request->action());
        }
        $id=\session('uid');
        $re=db("Admin")->where("id=$id")->find();
        $this->assign("re",$re);
        return view('modify');
    }
    function save(){
        $ob=db("Admin");
        $old_pwd=md5(input('old_pwd'));
        $id=input('id');
        $re=$ob->where("id=$id and pwd='$old_pwd'")->find();
        if($re){
            $data['pwd']=md5(input('pwd'));
            $res=$ob->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功,请重新登录！",url('Login/logout'));
            }else{
                $this->error("修改失败！");
            }
        }else{
            $this->error("原密码错误！");
        }

        
    }

}