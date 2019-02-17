<?php
namespace app\admin\controller;

class Sites extends BaseAdmin
{
    public function lister()
    {
        $uid=\session("uid");
        $list=db("loginlog")->alias("a")->field("a.id,loginid,loginip,browserinfo,lastlogintime,loginname")->where("loginid",$uid)->join("manager_info b","a.loginid=b.id")->order("a.id desc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        
        return $this->fetch();
    }
}