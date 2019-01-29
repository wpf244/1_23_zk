<?php
namespace app\index\controller;

use think\Controller;
use think\Request;


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

        //联系我们
        $call=db("article_info")->where("title","联系我们")->find();
        $this->assign("call",$call);

        //关于我们
        $about=db("article_info")->where("title","关于我们")->find();
        $this->assign("about",$about);

        //网站声明
        $sm=db("article_info")->where("title","网站声明")->find();
        $this->assign("sm",$sm);

        $other=db("other")->where("id=1")->find();
        $this->assign("other",$other);

        $ip=Request::instance()->ip();
      //  $ip="1.192.37.212";
      //  $yun=$this->findCityByIp($ip);
        // if($yun['code'] == 0){
        //     $data=$yun['data'];
        //     $ip=$data['ip'];
        //     $re=db("visit")->where("ip",$ip)->find();
        //     if($re){
        //         $dats['time']=time();
        //          db("visit")->where("ip",$ip)->setInc("times",1);
        //          db("visit")->where("ip",$ip)->update($dats);
        //     }else{
        //         $datas['ip']=$data['ip'];
        //         $datas['addrs']=$data['country'].$data['region'].$data['city'];
        //         $datas['isp']=$data['isp'];
        //         $datas['time']=time();
        //         db("visit")->insert($datas);
        //     }
            
           
        // }
        
        
    }

    public function findCityByIp($ip){
        $data = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);
        return json_decode($data,$assoc=true);
       }
      
 
      
    // public function _empty()
    // {
    //     $this->redirect("Index/index");
    // }

    

    
}