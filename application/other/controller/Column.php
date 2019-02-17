<?php
namespace app\other\controller;

class Column extends BaseAdmin
{
    public function index()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];

        $res=db("category_info")->field("id,name,parentid,level,status")->where("siteid=63 and status=1")->select();
        \makeArr($res,$arr);
        $arr=array();
        foreach($res as $k=> $v){
            $id=$v['id'];
            $ress=db("category_info")->where("parentid=$id")->find();
            if($ress){
                db("category_info")->where("parentid=$id")->setField("status",1);
            }
        }
    }
    public function indexs()
    {
        $res=db("category_info")->field("id,path")->select();
        foreach($res as $k => $v){
            $path=$v['path'];
            $arr=explode("-",$path);
            $paths=\implode(",",$arr);
            db("category_info")->where("id",$v['id'])->setField("path",$paths);
        }
    }
    public function lister()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
        foreach($res as $k => $v){
            if($v['parentid'] != 0){   
                $aa=$v;             
                $parentid=$v['parentid'];
                $re=db("category_info")->where("id=$parentid")->find();                 
                if($re['status'] == 1 || empty($re) || $re['siteid'] != $siteid){                   
                    unset($aa);
                                  
                }
  
            }
        }
        
        \makeArr($res,$arr);
        $arr=array();
        $siteinfo=db("site_info")->where("id",$siteid)->find();
        $sitename=$siteinfo['sitename'];
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"$sitename","level"=>"0");    
        $res=array_values($res);
       
        foreach($res as $kk => $vv){
            $data[$kk]['id']=(string)$vv['id'];
           if($vv['parentid'] == "-1"){
               $data[$kk]['parent']="#";
           }else{
                $data[$kk]['parent']=(string)$vv['parentid'];
           }
           
           $data[$kk]['text']=$vv['name'];
           if($vv['level'] == 0){
            $data[$kk]['state']=array("opened"=>"boolean");
        } 
            
        }
       
        
       $datas=json_encode($data);
     
       $this->assign("data",$datas);


        return $this->fetch();
    }
    public function modifys()
    {
        $id=input("id");
        
        $re=db("category_info")->where("id=$id")->find();
        $uid=session("uid");
        $user=db("manager_info")->where("id",$uid)->find();
        $siteid=$user['siteid'];
        $siteinfo=db("site_info")->where("id",$siteid)->find();
        $sitename=$siteinfo['sitename'];
      
        if($re['parentid'] == 0){
            $re['parentname']="$sitename";
        }else{
            $rep=db("category_info")->where("id",$re['parentid'])->find();
            $re['parentname']=$rep['name'];
        }
        echo json_encode($re);
    }
    public function usave()
    {
        $id=input("id");
        $re=db("category_info")->where("id=$id")->find();
        if($re){
             $data=input("post.");
             if(!is_string(input("coverimage"))){
                 $data['coverimage']=\uploads("coverimage");
             }else{
                $data['coverimage']=$re['coverimage'];
             }
             if(input("status")){
                 $data['status']=0;
             }
             $res=db("category_info")->where("id=$id")->update($data);
             if($res){
                 $this->success("修改成功");
             }else{
                 $this->error("修改失败");
             }
          //   var_dump($data);exit;
        }else{
            $this->error("非法操作");
        }
    }
    
    public function add()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
        foreach($res as $k => $v){
            if($v['parentid'] != 0){   
                $aa=$v;             
                $parentid=$v['parentid'];
                $re=db("category_info")->where("id=$parentid")->find();                 
                if($re['status'] == 1 || empty($re) || $re['siteid'] != $siteid){                   
                    unset($aa);
                                  
                }
  
            }
        }
        
        \makeArr($res,$arr);
        $arr=array();
        $siteinfo=db("site_info")->where("id",$siteid)->find();
        $sitename=$siteinfo['sitename'];
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"$sitename","level"=>"0");     
        $res=array_values($res);
       
        foreach($res as $kk => $vv){
            $data[$kk]['id']=(string)$vv['id'];
           if($vv['parentid'] == "-1"){
               $data[$kk]['parent']="#";
           }else{
                $data[$kk]['parent']=(string)$vv['parentid'];
           }          
           $data[$kk]['text']=$vv['name'];
           if($vv['level'] == 0){
               $data[$kk]['state']=array("opened"=>"boolean");
           }
            
            
        }
       
        
       $datas=json_encode($data);
     
       $this->assign("data",$datas);

       $id=input("parentid");
       if(empty($id)){
           $re["id"]=0;
           $re['name']="$sitename";
       }else{
           $re=db("category_info")->where("id=$id")->find();
       }
       $this->assign("re",$re);
       return $this->fetch();
    }
    public function find()
    {
        $id=input("id");     
        $re=db("category_info")->field("id,name")->where("id=$id")->find();
              
        echo json_encode($re);
    }
    public function save()
    {
        $data=input("post.");
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];
        $data['siteid']=$siteid;
        if(!is_string(\input("coverimage"))){
            $data['coverimage']=uploads("coverimage");
        }
        $parentid=input("parentid");
        $re=db("category_info")->where("id=$parentid")->find();
        if($re){
            $path=$re['path'];
            $data['level']=$re['level']+1;
        }else{
            $path="";
        }
        $data['path']=$path.",".$parentid;
        if(input("status")){
            $data['status']=0;
        }
        $rea=db("category_info")->insert($data);
        if($rea){
            $this->success("添加成功",url("lister"));
        }else{
            $this->error("添加失败",url("lister"));
        }
        
    }
    public function delete()
    {
        $id=input("id");
        $re=db("category_info")->where("id=$id")->find();
        if($re){
            $del=db("category_info")->where("id=$id")->setField("status",1);
            if($del){
                $res=db("category_info")->where("FIND_IN_SET($id,path)")->select();
                if($res){
                    db("category_info")->where("FIND_IN_SET($id,path)")->setField("status",1);
                }
                echo '0';
            }else{
                echo '2';
            }
        }else{
            echo '1';
        }
    }
   
    
    
}