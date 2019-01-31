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
               }elseif(input("statis")){
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
           if($re['id'] == 1){
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
                    }elseif(input("statis")){
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
    public function power_save()
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
    // public function index()
    // {
    //     $res=db("manager_info")->select();
    //     foreach($res as $k => $v){
    //         $rolesjson=json_decode($v['rolesjson']);
    //         $data['siteid']=$rolesjson->SiteId;
    //         $actionid=$rolesjson->ActionId;
    //         if($actionid){
    //             $cou=count($actionid);
    //             if($cou == 1){
    //                 $data['actionid']=0;
    //             }else{
    //                 $data['actionid']=1;
    //             }
    //         }else{
    //             $data['actionid']=0;
    //         }
    //         $isadmin=$rolesjson->IsSupperAdmin;
    //         if($isadmin){
    //             $data['isadmin']=1;
    //         }else{
    //             $data['isadmin']=0;
    //         }
           
    //         $res=db("manager_info")->where("id",$v['id'])->update($data);
    //     }
    // }







    
    
    
    
    
}