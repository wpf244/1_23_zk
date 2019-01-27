<?php
namespace app\admin\controller;

class Conpeople extends BaseAdmin
{
    public function index()
    {
        $keywords=\input("keywords");
        if($keywords){
            $map["title"]=array("like","%".$keywords."%");
        }else{
            $keywords='';
            $map=[];
        }
        
        $this->assign("keywords",$keywords);
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;
        $list=db("link_info")->alias("a")->field("a.id,title,realname,managerid,createtime,linktype,parentid,orderid")->where($map)->where(["parentid"=>0,"linktype"=>"bmfw","siteid"=>$siteid])->join("manager_info b","a.managerid=b.id")->order(["orderid desc","id asc"])->paginate(10,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function save()
    {
        $id=\input("id");
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;
        $data['createtime']=date("Y-m-d H:i:s");
        
        if($id){
            $re=db("link_info")->where("id=$id")->find();
            $data['iconname']=\input("iconname");
            $data['title']=input("title");
            $data['orderid']=input("orderid");

            $res=db("link_info")->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功",url("index"));
            }else{
                $this->error("修改失败",url("index"));
            }
        }else{
            $data['iconname']=\input("iconname");
            
            $data['title']=input("title");
            $data['orderid']=input("orderid");
            
            $data['managerid']=$uid;
            $data['linktype']="bmfw";
            $data['siteid']=$siteid;

            $rea=db("link_info")->insert($data);
            if($rea){
                $this->success("添加成功",url("index"));
            }else{
                $this->error("添加失败",url("index"));
            }
            
        }
    }
    public function modify()
    {
        $id=input("id");
        $re=db("link_info")->field("id,title,iconname,orderid")->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete()
    {
        $id=input("id");
        $re=db("link_info")->where("id=$id")->find();
        if($re){
            $del=db("link_info")->where("id=$id")->delete();
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
            $re=db("link_info")->where("id",$v)->find();
            if($re){
                $del=db("link_info")->where("id",$v)->delete(); 
            }
        }
        $this->redirect("index");
    }
    public function lister()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;
        $res=db("link_info")->field("id,title,orderid")->where(["parentid"=>0,"linktype"=>"bmfw","siteid"=>$siteid])->order(["orderid desc","id asc"])->select();
        $this->assign("res",$res);

        $keywords=\input("keywords");
        if($keywords){
            $map["title"]=array("like","%".$keywords."%");
        }else{
            $keywords='';
            $map=[];
        }
        
        $this->assign("keywords",$keywords);

        $list=db("link_info")->alias("a")->field("a.id,title,url,murl,realname,managerid,createtime,linktype,parentid,orderid")->where($map)->where("parentid != 0")->where(["linktype"=>"bmfw","siteid"=>$siteid])->join("manager_info b","a.managerid=b.id")->order(["orderid desc","id asc"])->paginate(20,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function saves()
    {
        $id=\input("id");
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;
      
        $data['createtime']=date("Y-m-d H:i:s");
        if($id){
            $data['title']=input("title");
            $data['parentid']=\input("parentid");
            $data['orderid']=input("orderid");
            $data['url']=input("url");
            $data['murl']=input("murl");

            $res=db("link_info")->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败",url("lister"));
            }
        }else{
            
            $data['title']=input("title");
            $data['parentid']=\input("parentid");
            $data['orderid']=input("orderid");
            $data['url']=input("url");
            $data['murl']=input("murl");
            $data['iconname']=input("iconname");
            $data['managerid']=$uid;
            $data['linktype']="bmfw";
            $data['siteid']= $siteid;

            $rea=db("link_info")->insert($data);
            if($rea){
                $this->success("添加成功",url("lister"));
            }else{
                $this->error("添加失败",url("lister"));
            }
            
        }
    }
    public function modifys()
    {
        $id=input("id");
        $re=db("link_info")->where("id=$id")->find();
        echo \json_encode($re);
    }
    public function delete_alls()
    {
        $id=\input("id");
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("link_info")->where("id",$v)->find();
            if($re){
                $del=db("link_info")->where("id",$v)->delete(); 
            }
        }
        $this->redirect("lister");
    }
}