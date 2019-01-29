<?php
namespace app\admin\controller;

use think\Db;

class Sys extends BaseAdmin{
    function seting(){
        $re=db('sys')->where("id=1")->find();
        $this->assign("re",$re);
        return view('seting');
    }
    public function save(){
       if($this->request->isAjax()){

          if(!is_string(input('pclogo'))){

              $data['pclogo']=uploads("pclogo");
          }
          if(!is_string(input('waplogo'))){
              $data['waplogo']=uploads("waplogo");
          }
          if(!is_string(input('qrcode'))){
              $data['qrcode']=uploads("qrcode");
          }
          if(!is_string(input('wx'))){
              $data['wx']=uploads("wx");
          }
          
           $data['name']=input('name');
           $data['username']=input('username');
           $data['url']=input('url');
           $data['qq']=input('qq');
           $data['icp']=input('icp');
           $data['email']=input('email');
           $data['phone']=input('phone');
           $data['tel']=input('tel');
           $data['longs']=input('longs');
           $data['lats']=input('lats');
           $data['addr']=input('addr');
           $data['content']=input('content');
           $data['fax']=input('fax');
           $data['telphone']=input('telphone');
           
           $re=db('sys')->where("id=1")->update($data);
           if($re){
               $this->success("修改成功！");
           }else{
               $this->error("修改失败！");
           }
           
       }else{
           $this->error("非法提交");
       }
        
    }
    function seo(){
        $re=db('seo')->where("id=1")->find();
        $this->assign("re",$re);
        return view('seo');
    }
    function saves(){
        if($this->request->isAjax()){
            $data=input('post.');
            $res=Db::name('seo')->where("id=1")->update($data);
            if($res){
                $this->success("修改成功！",url('Sys/seo'));
            }else{
                $this->error("修改失败！");
            }
            
        }else{
            $this->error("非法操作");
        }
    }
    public function visit()
    {
        $list=db("visit")->order(["times desc","id desc"])->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function delete()
    {
        $id=input("id");
        $re=db("visit")->where("id=$id")->find();
        if($re){
            $del=db("visit")->where("id=$id")->delete();
            if($del){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo '1';
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}