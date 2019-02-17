<?php

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Sever;

class BaseAdmin extends Controller{
    function _initialize(){
        if (!defined('CONTROLLER_NAME')) {
            define('CONTROLLER_NAME', $this->request->controller());
        }
        if (!defined('ACTION_NAME')) {
            define('ACTION_NAME', $this->request->action());
        }
        if(empty(session('uid'))){
            $this->redirect("login/index");
        }
        $sys=db('sys')->where("id=1")->find();
        $this->assign("sys",$sys);
        
        $uid=session('uid');
        $admin=db('manager_info')->where("id=$uid")->find();
        $this->assign("admin",$admin);

     
            $controls=db("carte")->where("pid=0")->order(['c_sort asc','cid asc'])->select();
            foreach($controls as $ks=> $vs){
                $controls[$ks]['ways']=db("carte")->where("pid={$vs['cid']}")->order(['c_sort asc','cid asc'])->select();
            }
            $this->assign("controls",$controls);
       
        
        $this->logs=new Sever();
        

        
    }
}