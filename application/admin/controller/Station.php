<?php
namespace app\admin\controller;

class Station extends BaseAdmin
{
    public function lister()
    {
        $keywords=\input("keywords");
        if($keywords){
            $map["sitename"]=array("like","%".$keywords."%");
        }else{
            $map=[];
            $keywords='';
        }
        $this->assign("keywords",$keywords);
        $list=db("site_info")->where("status=1")->where($map)->order("id asc")->paginate(10,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function add()
    {
        return $this->fetch();
    }
    public function save()
    {
        $data=input("post.");
        if(!is_string(input("sitelogo"))){
            $data['sitelogo']=uploads("sitelogo");
        }
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $data['createname']=$user['loginname'];
        $data['createtime']=date("Y-m-d H:i:s");
        $re=db("site_info")->insert($data);
        if($re){
            $this->success("添加成功",url('lister'));
        }else{
            $this->error("添加失败",url('lister'));
        }

    }
    public function modifys()
    {
        $id=input("id");
        $re=db("site_info")->where("id=$id")->find();
        $this->assign("re",$re);
        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("site_info")->where("id=$id")->find();
        if($re){
            $data=input("post.");
            if(!is_string(input("sitelogo"))){
                $data['sitelogo']=uploads("sitelogo");
            }else{
                $data['sitelogo']=$re["sitelogo"];
            }
            $uid=session("uid");
            $user=db("manager_info")->where("id=$uid")->find();
            $data['createname']=$user['loginname'];
            $data['createtime']=date("Y-m-d H:i:s");
            $res=db("site_info")->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功",url('lister'));
            }else{
                $this->error("修改失败",url('lister'));
            }

        }else{
            $this->error("非法操作",url('lister'));
        }
    }
    public function delete()
    {
        $id=input("id");
        $re=db("site_info")->where("id=$id")->find();
        if($re){
            $del=db("site_info")->where("id=$id")->delete();
            if($del){
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo '1';
        }
    }
    public function delete_all()
    {
        $id=\input("id");
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("site_info")->where("id",$v)->find();
            if($re){
                $del=db("site_info")->where("id",$v)->delete(); 
            }
        }
        $this->redirect("lister");
    }



}