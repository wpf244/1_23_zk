<?php
namespace app\admin\controller;

class Down extends BaseAdmin
{
    public function lister()
    {
        $list=db("app_downloadinfo")->where("status=0")->order("id asc")->paginate(20);
        $this->assign("list",$list);
        $page=$list->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
    public function save()
    {
        $id=input("id");
        if($id){
            $data['title']=input("title");
            $res=db("app_downloadinfo")->where("id=$id")->update($data);
            if($res){
                $this->success("修改成功");
            }else{
                $this->error("修改失败");
            }
        }else{
            $data['title']=input("title");
            $data['downloadcode']=\md5(input("title"));
            $re=db("app_downloadinfo")->insert($data);
            if($re){
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }
    }
    public function modifys()
    {
        $id=input("id");
        $re=db("app_downloadinfo")->field("id,title")->where("id=$id")->find();
        echo json_encode($re);
    }
    public function delete()
    {
        $id=input("id");
        $re=db("app_downloadinfo")->where("id=$id")->find();
        if($re){
            $del=db("app_downloadinfo")->where("id=$id")->delete();
            if($del){
                echo '0';
            }else{
                echo '1';
            }
        }else{
            echo '2';
        }
    }
}