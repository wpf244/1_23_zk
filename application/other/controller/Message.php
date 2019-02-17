<?php
namespace app\other\controller;

class Message extends BaseAdmin
{
    public function news()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];

        $status=input("status");
        $start=input("start");
        $end=input("end");
        $keywords=input("keywords");
        if($status == 3){
            $where['a.status']=array('neq',-1);
        }else{
            $where['a.status']=array('eq',$status);
        }
        if($start || $keywords){
            if($start){
                $end=$end.' 23:59:59';
                $where['createtime'] = array('between', array($start,$end));
            }else{
                $start="";
                $end="";
            }
            
                
            
            if($keywords){
                $where['title']=array("like",'%'.$keywords.'%');
            }else{
                $keywords="";
            }
        }else{
            $status=3;
            $start="";
            $end="";
            $keywords="";
            $where['a.status']=array('neq',-1);
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("status",$status);
        $this->assign("keywords",$keywords);

        $list=db("msg_info")->alias("a")->field("a.id as mid,typeid,content,title,categoryid,username,createtime,shenhetime,a.status as mstatus,shenhename,name,a.siteid as msiteid")->where("typeid=0")->where("a.siteid=$siteid")->where($where)->join("category_info b","a.categoryid = b.id")->order("mid desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function delete()
    {
        $id=input("id");
        $re=db("msg_info")->where("id=$id")->find();
        if($re){
            $del=db("msg_info")->where("id=$id")->delete();
            if($del){
                echo '0';
            }else{
                echo '1';
            }
        }else{
            echo '2';
        }
    }
    public function delete_all()
    {
        $id=\input("id");
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("msg_info")->where("id",$v)->find();
            if($re){
                $del=db("msg_info")->where("id",$v)->delete(); 
            }
        }
        $this->redirect("news");
    }
    public function adopt()
    {
        $id=\input("id");
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("msg_info")->where("id",$v)->find();
            if($re){
                $del=db("msg_info")->where("id",$v)->setField("status",1); 
            }
        }
        $this->redirect("news");
    }
    public function refuse()
    {
        $id=\input("id");
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("msg_info")->where("id",$v)->find();
            if($re){
                $del=db("msg_info")->where("id",$v)->setField("status",2); 
            }
        }
        $this->redirect("news");
    }
    public function consult()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
       
        $siteid=$user['siteid'];

        $status=input("status");
        $start=input("start");
        $end=input("end");
        $keywords=input("keywords");
        if($status == 3){
            $where['status']=array('neq',-1);
        }else{
            $where['status']=array('eq',$status);
        }
        if($start || $keywords){
            if($start){
                $end=$end.' 23:59:59';
                $where['createtime'] = array('between', array($start,$end));
            }else{
                $start="";
                $end="";
            }
            
                
            
            if($keywords){
                $where['title']=array("like",'%'.$keywords.'%');
            }else{
                $keywords="";
            }
        }else{
            $status=3;
            $start="";
            $end="";
            $keywords="";
            $where['status']=array('neq',-1);
        }
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("status",$status);
        $this->assign("keywords",$keywords);

        $list=db("msg_info")->where("typeid=1 and siteid=$siteid")->where($where)->order("id desc")->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function deletec()
    {
        $id=input("id");
        $re=db("msg_info")->where("id=$id")->find();
        if($re){
            $del=db("msg_info")->where("id=$id")->delete();
            if($del){
                echo '0';
            }else{
                echo '1';
            }
        }else{
            echo '2';
        }
    }
    public function delete_allc()
    {
        $id=\input("id");
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("msg_info")->where("id",$v)->find();
            if($re){
                $del=db("msg_info")->where("id",$v)->delete(); 
            }
        }
        $this->redirect("consult");
    }
    public function adoptc()
    {
        $id=\input("id");
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("msg_info")->where("id",$v)->find();
            if($re){
                $del=db("msg_info")->where("id",$v)->setField("status",1); 
            }
        }
        $this->redirect("consult");
    }
    public function refusec()
    {
        $id=\input("id");
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("msg_info")->where("id",$v)->find();
            if($re){
                $del=db("msg_info")->where("id",$v)->setField("status",2); 
            }
        }
        $this->redirect("consult");
    }
    public function save()
    {
        $data['content']=input("content");
        $data['parentid']=input("parentid");
        $data['createtime']=date("Y-m-d H:i:s");
        $data['userid']=session("uid");
        $re=db("msg_info")->insert($data);
        if($re){
            $this->success("回复成功");
        }else{
            $this->error("回复失败");
        }
    }






















}