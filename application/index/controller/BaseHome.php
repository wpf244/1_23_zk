<?php
namespace app\index\controller;

use think\Controller;


class BaseHome extends Controller
{
    public function _initialize(){
        $siteid = 4 ;
        $logo = db("site_info")->where("id",$siteid)->find();
        $this->assign("logo",$logo);
      
        //底部友情链接
        $footer_link=db("link_info")->where(["parentid"=>0,"linktype"=>"youlian","siteid"=>$siteid])->order("orderid asc")->select();
        foreach($footer_link as $k => $v){
            $footer_link[$k]['list']=db("link_info")->where(["linktype"=>"youlian","parentid"=>$v['id']])->select();
        }
        $this->assign("footer_link",$footer_link);
        
    }
}