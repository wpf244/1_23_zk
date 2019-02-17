<?php
namespace app\admin\controller;

class Newss extends BaseAdmin
{
    public function lister()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];

        $actionid=$user['actionid'];
        $this->assign("actionid",$actionid);

        $category=$user['category'];
        $cate=explode(",",$category);
        if(empty($category)){
            $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
            $arr=array();
            \makeArr($res,$arr);
            
            $siteinfo=db("site_info")->where("id",$siteid)->find();
            $sitename=$siteinfo['sitename'];
            $res[]=array("id"=>"0","parentid"=>"-1","name"=>"$sitename","level"=>"0");     
            $res=array_values($res);
        //  var_dump($res);exit;
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
        }else{
            $res=db("category_info")->field("id,name,parentid,level,status,orderid")->whereIn("id",$cate)->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
            $arr=array();
            \makeArr($res,$arr);
            
            $siteinfo=db("site_info")->where("id",$siteid)->find();
            $sitename=$siteinfo['sitename'];
            
            $res[]=array("id"=>"0","parentid"=>"-1","name"=>"$sitename","level"=>"0");     
            $res=array_values($res);
        //  var_dump($res);exit;
            foreach($res as $kk => $vv){
                $data[$kk]['id']=(string)$vv['id'];
            if($vv['parentid'] == "-1"){
                $data[$kk]['parent']="#";
            }else{
                    $data[$kk]['parent']=0;
            }
            
            $data[$kk]['text']=$vv['name'];
            if($vv['level'] == 0){
                $data[$kk]['state']=array("opened"=>"boolean");
            } 
                
            }
        }
         
       
        $this->assign("sitename",$sitename);

       $datas=json_encode($data);
       $this->assign("data",$datas);

        $status=input("status");
        $start=input("start");
        $end=input("end");
        $keywords=input("keywords");
        $name=input("name");
        $categoryid=input("categoryid");
        if($status == null){
            $status=3;
        }
        if($categoryid != 0){
            
            $cate=db("article_category")->where("categoryid",$categoryid)->select();
            if($cate){
                foreach($cate as $v){
                    $arrs[]=$v['articleid'];
                }
                
                $where['a.id']=array("in",$arrs);
            }else{
                $where=[];
            }
        }else{
            $where=[];
        }
      
        if($start || $keywords || $status){
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
            
            if($status != 3){
                $where['reviewstatus']=array('eq',$status);
            }
                     
        }else{
            if($status == 0){
                $where['reviewstatus']=array('eq',0);
            }else{
                $status=3;
                $start="";
            $end="";
            $keywords="";
            $name="全部";
            $categoryid=0;
            $where=[];
            }  
        }

        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("status",$status);
        $this->assign("keywords",$keywords);
        $this->assign("name",$name);
        $this->assign("categoryid",$categoryid);   
        $arra=array();
        foreach($data as $va){
          $arrs[]=$va['id'];
       }
       $list =db("article_info")->alias("a")->field("a.id,b_banner,title,author,createtime,top,reviewstatus,shenhename,name,articleid,b.categoryid")->where("arttype","xinwen")->where($where)->where("b.categoryid","in",$arrs)->join("article_category b","a.id=b.articleid")->join("category_info c","c.id = b.categoryid")->group("b.articleid")->order(['top desc','id desc'])->paginate(20,false,['query'=>request()->param()]);
       $this->assign("list",$list);
       
       $page=$list->render();
       $this->assign("page",$page);

       return $this->fetch();
    }
    public function add()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];

        $this->assign("user",$user);

        $category=$user['category'];
        $cate=explode(",",$category);
        if(empty($category)){
            $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
            $arr=array();
            \makeArr($res,$arr);
            
            $siteinfo=db("site_info")->where("id",$siteid)->find();
            $sitename=$siteinfo['sitename'];
            $res[]=array("id"=>"0","parentid"=>"-1","name"=>"$sitename","level"=>"0");     
            $res=array_values($res);
        //  var_dump($res);exit;
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
        }else{
            $res=db("category_info")->field("id,name,parentid,level,status,orderid")->whereIn("id",$cate)->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
            $arr=array();
            \makeArr($res,$arr);
           
            $siteinfo=db("site_info")->where("id",$siteid)->find();
            $sitename=$siteinfo['sitename'];
            $res[]=array("id"=>"0","parentid"=>"-1","name"=>"$sitename","level"=>"0");    
            $res=array_values($res);
           
            foreach($res as $kk => $vv){
                $data[$kk]['id']=(string)$vv['id'];
               if($vv['parentid'] == "-1"){
                   $data[$kk]['parent']="#";
               }else{
                    $data[$kk]['parent']=0;
               }          
               $data[$kk]['text']=$vv['name'];
               if($vv['level'] == 0){
                   $data[$kk]['state']=array("opened"=>"boolean");
               }
                
                
            }
        }
       
       
       
        
       $datas=json_encode($data);
     
       $this->assign("data",$datas);

       return $this->fetch();
       
    }
    public function save()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $siteid=$user['siteid'];

      //  $data=input("post.");
        if(!is_string(input("coverimage"))){
            $data['coverimage']=uploads("coverimage");
        }
        if(input("isbold")){
            $data['isbold']=0;
        }
        if(input("bannerid")){
            $data['bannerid']=1;
        }
        $data['title']=input("title");
        $data['subtitle']=input("subtitle");
       
        $data['url']=input("url");
        $data['author']=input("author");
        $data['source']=input("source");
        $data['sourceurl']=input("sourceurl");
        $data['color']=input("color");
      //  $data['content']=input("content");
        $data['reviewtime']=input("createtime");
        $data['createtime']=input("createtime");
        $data['reviewstatus']=input("reviewstatus");
        $data['arttype']=input("pagetype");
        $data['siteid']=$siteid;
        $content=input("content");

        $re=db("article_info")->insert($data);

        $articleid=db("article_info")->getLastInsID();
        $categoryid=input("categoryid");
        $categoryid=explode(",",$categoryid);
        foreach($categoryid as $v){
            $info=db("category_info")->where("id",$v)->find();
            if($info){
                $arr['category_code']=$info['name'];
            }
            $arr['articleid']=$articleid;
            $arr['categoryid']=$v;
    
            db("article_category")->insert($arr);
        }
        

        $arrc['content']=$content;
        $arrc['articleid']=$articleid;

        db("article_content")->insert($arrc);

        $log['userid']=$user['id'];
        $log['realname']=$user['realname'];
        $log['createtime']=date("Y-m-d H:i:s");
        $log['actionname']="新建";
        $log['newsid']=$articleid;
        $log['newstitle']=input("title");

        db("article_log")->insert($log);

        if($re){
            $this->success("添加成功",url("lister"));
        }else{
            $this->error("添加失败",url("lister"));
        }

    }
    public function adopt()
    {
        $id=\input("id");
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("article_info")->where("id",$v)->find();
            $data['shenhename']=$user['realname'];
            $data['reviewstatus']=1;
            if($re){
                $del=db("article_info")->where("id",$v)->update($data); 

                
                $log['userid']=$user['id'];
                $log['realname']=$user['realname'];
                $log['createtime']=date("Y-m-d H:i:s");
                $log['actionname']="审核通过";
                $log['newsid']=$re['id'];
                $log['newstitle']=$re['title'];

                db("article_log")->insert($log);
            }
        }
        $this->redirect("lister");
    }
    public function refuse()
    {
        $id=\input("id");
       
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("article_info")->where("id",$v)->find();
            if($re){
                $del=db("article_info")->where("id",$v)->setField("reviewstatus",2); 

                $uid=session("uid");
                $user=db("manager_info")->where("id=$uid")->find();
                $log['userid']=$user['id'];
                $log['realname']=$user['realname'];
                $log['createtime']=date("Y-m-d H:i:s");
                $log['actionname']="审核拒绝";
                $log['newsid']=$re['id'];
                $log['newstitle']=$re['title'];

                db("article_log")->insert($log);
            }
        }
        $this->redirect("lister");
    }
    public function delete_all()
    {
        $id=\input("id");
       
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("article_info")->where("id",$v)->find();
            if($re){
                $del=db("article_info")->where("id",$v)->delete(); 

                $uid=session("uid");
                $user=db("manager_info")->where("id=$uid")->find();
                $log['userid']=$user['id'];
                $log['realname']=$user['realname'];
                $log['createtime']=date("Y-m-d H:i:s");
                $log['actionname']="删除";
                $log['newsid']=$re['id'];
                $log['newstitle']=$re['title'];

                db("article_log")->insert($log);
            }
        }
        $this->redirect("lister");
    }
}