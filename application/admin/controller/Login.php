<?php
/**
 * Login.php
 * 朵朵科技
 * 官方网址 www.dd371.com
 * ===============================
 * @uses ECHO 
 * @date 2018.11.29
 * @version 1.0
 * 
 * **/
namespace app\admin\controller;


use think\Request;

class Login extends Common
{
    public function  index(){
        return view('index');
    }
    public function check(){
       // $data = input('post.');
        if(!captcha_check(input('post.verify'))) {
            // 校验失败
            $this->error('验证码不正确');exit;
        }
        $unm=input('post.loginname');
        $pwd=input('post.password');
        $re=db("manager_info")->where(array('loginname'=>$unm,'password'=>$pwd))->find();
        if($re){
            if($re['isenabled'] == 1){
                session('uid',$re['id']);
                
                $ip=Request::instance()->ip();
                $data['loginid']=$re['id'];
                $data['loginip']=$ip;
                $data['browserinfo']=$_SERVER['HTTP_USER_AGENT'];
                $data['lastlogintime']=date("Y-m-d H:i:s");
                
                //增加操作日志
                db("loginlog")->insert($data);
                if($re['isadmin'] == 1){
                    $this->success('登陆成功 ^_^',url('Index/index'));
                }else{
                    if($re['siteid'] == 4){
                        $this->success('登陆成功 ^_^',url('Admins/index'));
                    }else{
                        $this->success('登陆成功 ^_^',url('Other/Index/index'));
                    }
                    
                }
               

            }else{
                $this->error("此账号未启用",url("Login/index"));
            }
            
        }else{
            $this->error('登录失败：用户名或密码错误。',url('Login/index'));
        }
    }
    public function logout(){
        session("uid",null);
        $this->redirect('Login/index');
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
                $this->success("修改成功！");
            }else{
                $this->error("修改失败！");
            }
        }else{
            $this->error("原密码错误！");
        }

        
    }
    public function login()
    {
        return $this->fetch();
    }
 


}