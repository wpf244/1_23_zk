<?php
namespace app\admin\controller;

class Spec extends BaseAdmin
{
    public function lister()
    {
        $list=db("spec")->order("id desc")->paginate(10);
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
        $assort=input("assort");
        $rea=db("spec")->where("assort",$assort)->find();
        if(empty($rea)){
            $data=input("post.");
            if(!is_string(input('image'))){
                $data['image']=uploads("image");
            }
            if(input("status")){
                $data['status']=1;
            }
            if(input("url_status")){
                $data['url_status']=1;
            }
            $data['time']=time();
            $re=db("spec")->insert($data);
            if($re){
                $this->success("添加成功",url('lister'));
            }else{
                $this->error("添加失败",url('lister'));
            }
        }else{
            $this->error("添加失败，分类字母已存在");
        }
       
    }
    public function modifys()
    {
        $id=input("id");
        $re=db("spec")->where("id=$id")->find();
        $this->assign("re",$re);
        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $re=db("spec")->where("id=$id")->find();
        if($re){
            $data=input("post.");
            if(!is_string(input('image'))){
                $data['image']=uploads("image");
            }else{
                $data['image']=$re['image'];
            }
            if(input("status")){
                $data['status']=1;
            }
            if(input("url_status")){
                $data['url_status']=1;
            }
            $res=db("spec")->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功",url('lister'));
            }else{
                $this->error("修改失败，分类字母已存在"); 
            }
        }else{
            $this->error("非法操作",url('lister'));
        }
    }
    public function delete()
    {
        $id=input("id");
        $re=db("spec")->where("id",$id)->find();
        if($re){
           $del=db("spec")->where("id",$id)->delete();
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
            $re=db("spec")->where("id",$v)->find();
            if($re){
                $del=db("spec")->where("id",$v)->delete(); 
            }
        }
        $this->redirect("lister");
    }
}