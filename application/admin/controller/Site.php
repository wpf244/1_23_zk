<?php
namespace app\admin\controller;

class Site extends BaseAdmin
{
    public function user_login_log()
    {
        
        $uid=\session("uid");
        $list=db("loginlog")->alias("a")->field("a.id,loginid,loginip,browserinfo,lastlogintime,loginname")->where("loginid",$uid)->join("manager_info b","a.loginid=b.id")->order("a.id desc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function all_login_log()
    {
        $list=db("loginlog")->alias("a")->field("a.id,loginid,loginip,browserinfo,lastlogintime,loginname")->join("manager_info b","a.loginid=b.id")->order("a.id desc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function sensitive()
    {
        $keywords=\input("keywords");
        if($keywords){
            $map["mingan1|mingan2"]=array("like","%".$keywords."%");
        }else{
            $map=[];
            $keywords='';
        }
        $this->assign("keywords",$keywords);
        $list=db("minganci")->where($map)->order("id desc")->paginate(10,false,['query'=>request()->param()]);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function save()
    {
        $id=\input("id");
        if($id){
            $data['mingan1']=input("mingan1");
            $data['mingan2']=input("mingan2");

            $res=db("minganci")->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功",url("sensitive"));
            }else{
                $this->error("修改失败",url("sensitive"));
            }
        }else{
            $uid=session("uid");
            $user=db("manager_info")->where("id=$uid")->find();
            $data['mingan1']=input("mingan1");
            $data['mingan2']=input("mingan2");
            $data['createtime']=time();
            $data['createid']=$uid;
            $data['createname']=$user['loginname'];

            $rea=db("minganci")->insert($data);
            if($rea){
                $this->success("添加成功",url("sensitive"));
            }else{
                $this->error("添加失败",url("sensitive"));
            }
            
        }
    }
    public function modify()
    {
        $id=input("id");
        $re=db("minganci")->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete()
    {
        $id=input("id");
        $re=db("minganci")->where("id=$id")->find();
        if($re){
            $del=db("minganci")->where("id=$id")->delete();
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
            $re=db("minganci")->where("id",$v)->find();
            if($re){
                $del=db("minganci")->where("id",$v)->delete(); 
            }
        }
        $this->redirect("sensitive");
    }
    public function act_log()
    {
        $list=db("article_log")->order("id desc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function other()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];
        $re=db("other")->where("siteid",$siteid)->find();
        if($re){
            $this->assign("re",$re);
        }else{
            $data['siteid']=$siteid;
            db("other")->insert($data);
            $this->redirect("other");
        }
        
        return $this->fetch();
    }
    public function osave()
    {
        $id=input("id");
        $re=db("other")->where("id",$id)->find();
        if($re){
            $data=input("post.");
        //   var_dump($data);exit;
             if(!is_string(input('pimage'))){
                 $data['pimage']=uploads("pimage");
             }else{
                 $data['pimage']=$re['pimage'];
             }
             if(!is_string(input('jimage'))){
                $data['jimage']=uploads("jimage");
            }else{
                $data['jimage']=$re['jimage'];
            }
            if(input("pstatus")){
                $data['pstatus']=1;
            }else{
                $data['pstatus']=0;
            }
            if(input("qstatus")){
                $data['qstatus']=1;
            }else{
                $data['qstatus']=0;
            }
            if(input("jstatus")){
                $data['jstatus']=1;
            }else{
                $data['jstatus']=0;
            }
            $res=db("other")->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }

        }else{
            $this->error("系统繁忙，请稍后再试");
        }
    }
}