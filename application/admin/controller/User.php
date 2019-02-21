<?php
namespace app\admin\controller;

class User extends BaseAdmin
{
   public function lister()
   {
        $list=db("manager_info")->alias("a")->field("a.id,loginname,realname,isenabled,siteid,actionid,sitename")->join("site_info b","a.siteid=b.id")->order("a.id desc")->paginate(20);
        $this->assign("list",$list);

        $page=$list->render();
        $this->assign("page",$page);    

        return  $this->fetch();
   }
   public function add()
   {
      $res=db("site_info")->where("status",1)->order("id asc")->select();
      $this->assign("res",$res);  
    
      return  $this->fetch();
   }
   public function save()
   {
       $loginname=input('loginname');
       $re=db("manager_info")->where("loginname",$loginname)->find();
       if($re){
           $this->error("此账号已存在",url('lister'));
       }else{
           if(input("statu") && input("status")){
             $data['actionid']=1;
           }else{
               if(input("statu")){
                   $data['actionid']=0;
               }elseif(input("status")){
                   $data['actionid']=2;
               }else{
                $data['actionid']=3;
               }
           }
           if(input("isenabled")){
               $data['isenabled']=1;
           }
           $data['loginname']=input("loginname");
           $data['password']=input("password");
           $data['realname']=input("realname");
           $data['siteid']=input("siteid");
           $rea=db("manager_info")->insert($data);
           if($rea){
               $this->success("添加成功",url('lister'));
           }else{
            $this->error("添加失败",url('lister'));
           }
       }
   } 
   public function change()
   {
       $id=input('id');
       $re=db("manager_info")->where("id=$id")->find();
       if($re){
           if($re['isadmin'] == 1){
               echo '0';
           }else{
               echo '1';
           }

       }else{
           echo '2';
       }
   } 
    public function modifys()
    {
        $id=input('id');
        $re=db("manager_info")->where("id",$id)->find();
        $this->assign("re",$re);

        $res=db("site_info")->where("status",1)->order("id asc")->select();
        $this->assign("res",$res); 

        return $this->fetch();
    }
    public function usave()
    {
        $id=input("id");
        $rea=db("manager_info")->where("id",$id)->find();
        if($rea){
            $loginname=input('loginname');
            $re=db("manager_info")->where("loginname",$loginname)->where("id != $id")->find();
            if($re){
                $this->error("此账号已存在",url('lister'));
            }else{
                if(input("statu") && input("status")){
                  $data['actionid']=1;
                }else{
                    if(input("statu")){
                        $data['actionid']=0;
                    }elseif(input("status")){
                        $data['actionid']=2;
                    }else{
                     $data['actionid']=3;
                    }
                }
                if(input("isenabled")){
                    $data['isenabled']=1;
                }
                $data['loginname']=input("loginname");
                $data['password']=input("password");
                $data['realname']=input("realname");
                $data['siteid']=input("siteid");
                $rea=db("manager_info")->where("id",$id)->update($data);
                if($rea){
                    $this->success("修改成功",url('lister'));
                }else{
                 $this->error("修改失败",url('lister'));
                }
            }
        }else{
            $this->error("非法操作",url('lister'));
        }
        
    }
    public function delete()
    {
        $id=input('id');
        $re=\db("manager_info")->where("id=$id")->find();
        if($re){
          if($id == 1){
              echo '1';
          }else{
              $del=\db("manager_info")->where("id=$id")->delete();
              echo '0';
          }
        }else{
            echo '2';
        }
    }
    public function power()
    {
        // $uid=session("uid");
        // $user=db("manager_info")->where("id=$uid")->find();
        // $rolesjson=json_decode($user['rolesjson']);
        // $siteid=$rolesjson->SiteId;

        $id=\input("id");
        $user=db("manager_info")->where("id=$id")->find();
        $siteid=$user['siteid'];

        $this->assign("user",$user);
        $category=$user['category'];
        $arrs=explode(",",$category);


        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
       
      //  $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
        $res=array_values($res);
       
        foreach($res as $kk => $vv){
            $data[$kk]['id']=(string)$vv['id'];
           if($vv['parentid'] == "0"){
               $data[$kk]['parent']="#";
           }else{
                $data[$kk]['parent']=(string)$vv['parentid'];
           }          
           $data[$kk]['text']=$vv['name'];
           if($vv['level'] == 0){
               $data[$kk]['state']=array("opened"=>true);
           }
           if(in_array($vv['id'],$arrs)){

               $data[$kk]['state']=array("selected"=>true);
           }
            
            
        }
       
        
       $datas=json_encode($data);
     
       $this->assign("data",$datas);

       return $this->fetch();
    }
    public function find()
    {
        $id=input("id");     
        $re=db("category_info")->field("id,name")->where("id=$id")->find();

        $uid=input("uid");
        $user=db("manager_info")->where("id",$uid)->find();
        if($user){
            $category=$user['category'];
            $arrs=explode(",",$category);
            $ids=$re['id'];
            $arrs[]=$ids;
            $re=implode(",",$arrs);
            db("manager_info")->where("id",$uid)->update(['category'=>$re]);
        }
        
        echo json_encode($re);
    }
    public function finds()
    {
        $id=input("id");     
        $re=db("category_info")->field("id,name")->where("id=$id")->find();

        $uid=input("uid");
        $user=db("manager_info")->where("id",$uid)->find();
        if($user){
            $category=$user['category'];
            $arrs=explode(",",$category);
            $ids=$re['id'];
            if(in_array($ids,$arrs)){
                $key=array_search($ids ,$arrs);
                array_splice($arrs,$key,1);    
            }
            $re=implode(",",$arrs);
            db("manager_info")->where("id",$uid)->update(['category'=>$re]);
        }
              
        echo json_encode($re);
    }
   
    public function power_save()
    {
        $id=\input('id');
        $re=\db("manager_info")->where("id=$id")->find();
        if($re){
          
            $data['category']=input("categoryid");
            
            $res=\db("manager_info")->where("id=$id")->update($data);
            if($res){
               $this->success("权限配置成功",url('lister'));
            }else{
                $this->error("权限配置成功",url('lister'));
            }
            
        }else{
            $this->error("非法操作",url('lister'));
        }

        
    }
    public function powers()
    {
        $list=db("carte")->where("pid=0")->select();
        foreach($list as $k => $v){
            $list[$k]['lists']=db("carte")->where("pid={$v['cid']}")->select();
        }
        $this->assign("list",$list);

        $id=input('id');
        $admin=db("admin")->where("id=$id")->find();
        $this->assign("admin",$admin);

        $control=$admin['control'];
        $control_arr=explode(",",$control);
        $this->assign("control_arr",$control_arr);

        $way=$admin['way'];
        $way_arr=explode(",",$way);
        $this->assign("way_arr",$way_arr);
        
        return $this->fetch();
    }
    public function power_saves()
    {
        $id=\input('id');
        $re=\db("admin")->where("id=$id")->find();
        if($re){
          
            $control=\input('controller/a');
            $way=\input('function/a');

            $data['control']=implode(",",array_keys($control));
            $data['way']=implode(",",array_keys($way));
            
            $res=\db("admin")->where("id=$id")->update($data);
            if($res){
               $this->success("权限配置成功",url('lister'));
            }else{
                $this->error("权限配置失败",url('lister'));
            }
            
        }else{
            $this->error("非法操作",url('lister'));
        }

        
    }
    public function index()
    {
        $res=db("site_info")->select();
        foreach($res as $k => $v){
            $rolesjson=json_decode($v['icpconfig']);
            $data['icp']=$rolesjson->ICP;
            $data['gongan']=$rolesjson->GongAn;
            if($rolesjson->JiGuanCode){
                $data['dang']=$rolesjson->JiGuanCode;
            }
            
            
           
            $res=db("site_info")->where("id",$v['id'])->update($data);
        }
    }







    
    
    
    
    
}