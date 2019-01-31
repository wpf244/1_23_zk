<?php
namespace app\admin\controller;

class News extends BaseAdmin
{
    public function lister()
    {
        
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
        
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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

        $status=input("status");
        $start=input("start");
        $end=input("end");
        $keywords=input("keywords");
        $name=input("name");
        $categoryid=input("categoryid");
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
            
            if($status != 3 || $status != 4){
                $where['reviewstatus']=array('eq',$status);
            }
            if($status == 4){
                $where['reviewstatus']=array('eq',0);
            }

          

        }else{
            $status=3;
            $start="";
            $end="";
            $keywords="";
            $name="全部";
            $categoryid=0;
            $where=[];
           
        }
        
     // var_dump($status,$where);
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("status",$status);
        $this->assign("keywords",$keywords);
        $this->assign("name",$name);
        $this->assign("categoryid",$categoryid);

        

     //  var_dump($where);exit;

       $list =db("article_info")->alias("a")->field("a.id,b_banner,title,author,createtime,top,reviewstatus,shenhename,name,articleid,b.categoryid")->where("arttype","xinwen")->where($where)->join("article_category b","a.id=b.articleid")->join("category_info c","c.id = b.categoryid")->group("b.articleid")->order(['top desc','id desc'])->paginate(20,false,['query'=>request()->param()]);
       $this->assign("list",$list);
       
       $page=$list->render();
       $this->assign("page",$page);
       
        return $this->fetch();
    }
    public function add()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $this->assign("user",$user);

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
       
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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

    //    $id=input("parentid");
    //    if($id == 0){
    //        $re["id"]=0;
    //        $re['name']="周口政府";
    //    }else{
    //        $re=db("category_info")->where("id=$id")->find();
    //    }
    //    $this->assign("re",$re);
       return $this->fetch();
    }
    public function save()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

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
    public function modifys()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
        
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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

       $ids=input("id");
       
       $rem=db("article_info")->field("id,title,subtitle,createtime,endtime,url,author,source,sourceurl,isbold,bannerid,color,coverimage")->where("id",$ids)->order("id desc")->limit(1)->find();
       
       $this->assign("re",$rem);

      $info=db("article_category")->where("articleid",$ids)->find();

      $content=db("article_content")->where("articleid",$ids)->find();
      $this->assign("info",$info);
      $this->assign("content",$content);
     
       return $this->fetch();
    }
    public function usave()
    {
        
        $id=input("id");
        $re=db("article_info")->where("id",$id)->order("id desc")->find();

        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        
        if($re){

            if(!is_string(input("coverimage"))){
                $data['coverimage']=uploads("coverimage");
            }else{
                $data['coverimage']=$re["coverimage"];
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
            $data['createtime']=input("createtime");
            $data['endtime']=input("endtime");
          //  $data['content']=input("content");
           
            $content=input("content");
    
            $res=db("article_info")->where("id",$id)->update($data);
    
            $articleid=db("article_info")->getLastInsID();
            $categoryid=input("categoryid");
            $info=db("category_info")->where("id",$categoryid)->order("id desc")->find();
            if($info){
                $arr['category_code']=$info['name'];
            }
            $arr['articleid']=$id;
            $arr['categoryid']=$categoryid;

        
            $rea=db("article_category")->where("articleid",$id)->update($arr);
           
    
            $arrc['content']=$content;
            $arrc['articleid']=$id;
           
           $rec=db("article_content")->where("articleid",$id)->update($arrc);
            
    
            
    
            $log['userid']=$user['id'];
            $log['realname']=$user['realname'];
            $log['createtime']=date("Y-m-d H:i:s");
            $log['actionname']="编辑";
            $log['newsid']=$id;
            $log['newstitle']=input("title");
    
            db("article_log")->insert($log);
    
            if($res || $rea || $rec){
                $this->success("修改成功",url("lister"));
            }else{
                $this->error("修改失败",url("lister"));
            }

        }else{
            $this->error("非法操作",url("lister"));
        }
       
    }
    public function delete()
    {
        $id=input("id");
        $re=db("article_info")->where("id",$id)->find();
        if($re){
           $del=db("article_info")->where("id",$id)->delete();
           $uid=session("uid");
           $user=db("manager_info")->where("id=$uid")->find();
           $log['userid']=$user['id'];
           $log['realname']=$user['realname'];
           $log['createtime']=date("Y-m-d H:i:s");
           $log['actionname']="删除";
           $log['newsid']=$re['id'];
           $log['newstitle']=$re['title'];

           db("article_log")->insert($log);
           if($del){
               echo '0';
           }else{
               echo '2';
           }
        }else{
            echo '1';
        }
    }
    public function change()
    {
        $id=input("id");
        $re=db("article_info")->where("id",$id)->find();
        if($re){
           $top=input("top");
           if($top == 1){
               $res=db("article_info")->where("id",$id)->setField("top",0);
               if($res){
                   echo '0';
               }else{
                   echo '2';
               }
           }else{
            $res=db("article_info")->where("id",$id)->setField("top",1);
            if($res){
                echo '0';
            }else{
                echo '2';
            }
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
    public function adopt()
    {
        $id=\input("id");
       
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("article_info")->where("id",$v)->find();
            if($re){
                $del=db("article_info")->where("id",$v)->setField("reviewstatus",1); 

                $uid=session("uid");
                $user=db("manager_info")->where("id=$uid")->find();
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
    public function find()
    {
        $id=input("id");     
        $re=db("category_info")->field("id,name")->where("id=$id")->find();
              
        echo json_encode($re);
    }

    public function lingdao()
    {
        
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
        
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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

        // $status=input("status");
        // $start=input("start");
        // $end=input("end");
        $keywords=input("keywords");
        $categoryid=input("categoryid");

        if($categoryid != 0){
            
            $cate=db("article_category")->where("categoryid",$categoryid)->select();
            if($cate){
                foreach($cate as $v){
                    $arrs[]=$v['articleid'];
                }
                
                $where['id']=array("in",$arrs);
            }else{
                $where=[];
            }
        }

        if($keywords){
           
           
            if($keywords){
                $where['title']=array("like",'%'.$keywords.'%');
            }else{
                $keywords="";
            }
            
    
           

        }else{
          
            $keywords="";
            $categoryid=0;
            $where=[];
           
        }
      
      
        $this->assign("keywords",$keywords);
        
        $this->assign("categoryid",$categoryid);

        

      //  var_dump($where);exit;

       $list =db("article_info")->where("arttype","lingdao")->where("subtitle != '' ")->where($where)->order(['top desc','id desc'])->paginate(20,false,['query'=>request()->param()]);
       $this->assign("list",$list);
       
       $page=$list->render();
       $this->assign("page",$page);
       
        return $this->fetch();
    }
    public function addl()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $this->assign("user",$user);

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
       
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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
    public function savel()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

      //  $data=input("post.");
        if(!is_string(input("coverimage"))){
            $data['coverimage']=uploads("coverimage");
        }
        if(input("isbold")){
            $data['isbold']=0;
        }
        
        $data['title']=input("title");
        $data['subtitle']=input("subtitle");
       
        $data['description']=input("description");
        $data['summary']=input("summary");
       
        $data['color']=input("color");
      
        $data['reviewtime']=input("createtime");
        $data['createtime']=input("createtime");
        $data['reviewstatus']=input("reviewstatus");
        $data['arttype']=input("arttype");
      
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
            $this->success("添加成功",url("lingdao"));
        }else{
            $this->error("添加失败",url("lingdao"));
        }

    }
    public function modifyl()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
        
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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

       $ids=input("id");
       
       $rem=db("article_info")->field("id,title,subtitle,url,author,createtime,summary,sourceurl,isbold,description,color,coverimage")->where("id",$ids)->order("id desc")->limit(1)->find();
       
       $this->assign("re",$rem);

      $info=db("article_category")->where("articleid",$ids)->find();

      $content=db("article_content")->where("articleid",$ids)->find();
      $this->assign("info",$info);
      $this->assign("content",$content);
     
       return $this->fetch();
    }
    public function usavel()
    {
        
        $id=input("id");
        $re=db("article_info")->where("id",$id)->order("id desc")->find();

        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        
        if($re){

            if(!is_string(input("coverimage"))){
                $data['coverimage']=uploads("coverimage");
            }else{
                $data['coverimage']=$re["coverimage"];
            }
            if(input("isbold")){
                $data['isbold']=0;
            }
            
            $data['title']=input("title");
            $data['subtitle']=input("subtitle");
           
            $data['description']=input("description");
            $data['summary']=input("summary");
           
            $data['color']=input("color");
          //  $data['content']=input("content");
      
            $data['createtime']=input("createtime");
            $data['reviewstatus']=input("reviewstatus");
            $data['arttype']=input("arttype");
          
            $content=input("content");
    
            $res=db("article_info")->where("id",$id)->update($data);
    
            $articleid=db("article_info")->getLastInsID();
            $categoryid=input("categoryid");
            $info=db("category_info")->where("id",$categoryid)->order("id desc")->find();
            if($info){
                $arr['category_code']=$info['name'];
            }
            $arr['articleid']=$id;
            $arr['categoryid']=$categoryid;

        
            $rea=db("article_category")->where("articleid",$id)->update($arr);
           
    
            $arrc['content']=$content;
            $arrc['articleid']=$id;
           
           $rec=db("article_content")->where("articleid",$id)->update($arrc);
            
    
            
    
            $log['userid']=$user['id'];
            $log['realname']=$user['realname'];
            $log['createtime']=date("Y-m-d H:i:s");
            $log['actionname']="编辑";
            $log['newsid']=$id;
            $log['newstitle']=input("title");
    
            db("article_log")->insert($log);
    
            if($res || $rea || $rec){
                $this->success("修改成功",url("lingdao"));
            }else{
                $this->error("修改失败",url("lingdao"));
            }

        }else{
            $this->error("非法操作",url("lingdao"));
        }
       
    }
    public function delete_alls()
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
        $this->redirect("lingdao");
    }
    public function adopts()
    {
        $id=\input("id");
       
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("article_info")->where("id",$v)->find();
            if($re){
                $del=db("article_info")->where("id",$v)->setField("reviewstatus",1); 
                $uid=session("uid");
                $user=db("manager_info")->where("id=$uid")->find();
                $log['userid']=$user['id'];
                $log['realname']=$user['realname'];
                $log['createtime']=date("Y-m-d H:i:s");
                $log['actionname']="审核通过";
                $log['newsid']=$re['id'];
                $log['newstitle']=$re['title'];

                db("article_log")->insert($log);
            }
        }
        $this->redirect("lingdao");
    }
    public function refuses()
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
        $this->redirect("lingdao");
    }

    public function wenjian()
    {
        
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
        
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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

        $status=input("status");
        $start=input("start");
        $end=input("end");
        $keywords=input("keywords");
        $name=input("name");
        $categoryid=input("categoryid");

        
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
            $status=3;
            $start="";
            $end="";
            $keywords="";
            $name="全部";
            $categoryid=0;
            $where=[];
           
        }
      
        $this->assign("start",$start);
        $this->assign("end",$end);
        $this->assign("status",$status);
        $this->assign("keywords",$keywords);
        $this->assign("name",$name);
        $this->assign("categoryid",$categoryid);

        

      //  var_dump($where);exit;

       $list =db("article_info")->alias("a")->field("a.id,title,author,createtime,top,reviewstatus,shenhename,name,articleid,b.categoryid")->where("arttype","wenjian")->where("zhongguogovid = 0")->where($where)->join("article_category b","a.id=b.articleid")->join("category_info c","c.id = b.categoryid")->order(['top desc','id desc'])->paginate(20,false,['query'=>request()->param()]);
       $this->assign("list",$list);
       
       $page=$list->render();
       $this->assign("page",$page);
       
        return $this->fetch();
    }
    public function addw()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $this->assign("user",$user);

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
       
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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

    //    $id=input("parentid");
    //    if($id == 0){
    //        $re["id"]=0;
    //        $re['name']="周口政府";
    //    }else{
    //        $re=db("category_info")->where("id=$id")->find();
    //    }
    //    $this->assign("re",$re);
       return $this->fetch();
    }
    public function savew()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

     
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
        $data['fabuzh']=input("fabuzh");
        $data['color']=input("color");
        $data['reviewtime']=input("reviewtime");
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
            $this->success("添加成功",url("wenjian"));
        }else{
            $this->error("添加失败",url("wenjian"));
        }

    }
    public function modifyw()
    {
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

        $res=db("category_info")->field("id,name,parentid,level,status,orderid")->where("siteid=$siteid and status=0")->order(["orderid desc","id asc"])->select();
         
       
        $arr=array();
        \makeArr($res,$arr);
        
        $res[]=array("id"=>"0","parentid"=>"-1","name"=>"周口政府","level"=>"0");    
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

       $ids=input("id");
       
       $rem=db("article_info")->field("id,title,subtitle,url,createtime,reviewtime,author,source,fabuzh,sourceurl,isbold,bannerid,color,coverimage")->where("id",$ids)->order("id desc")->limit(1)->find();
       
       $this->assign("re",$rem);

      $info=db("article_category")->where("articleid",$ids)->find();

      $content=db("article_content")->where("articleid",$ids)->find();
      $this->assign("info",$info);
      $this->assign("content",$content);
     
       return $this->fetch();
    }
    public function usavew()
    {
        
        $id=input("id");
        $re=db("article_info")->where("id",$id)->order("id desc")->find();

        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        
        if($re){

           
            if(input("isbold")){
                $data['isbold']=0;
            }
           
            $data['title']=input("title");
            $data['subtitle']=input("subtitle");
           
            $data['url']=input("url");
            $data['author']=input("author");
            $data['source']=input("source");
            $data['fabuzh']=input("fabuzh");
            $data['color']=input("color");
            $data['reviewtime']=input("reviewtime");
            $data['createtime']=input("createtime");
           
            $content=input("content");
    
            $res=db("article_info")->where("id",$id)->update($data);
    
            $articleid=db("article_info")->getLastInsID();
            $categoryid=input("categoryid");
            $info=db("category_info")->where("id",$categoryid)->order("id desc")->find();
            if($info){
                $arr['category_code']=$info['name'];
            }
            $arr['articleid']=$id;
            $arr['categoryid']=$categoryid;

        
            $rea=db("article_category")->where("articleid",$id)->update($arr);
           
    
            $arrc['content']=$content;
            $arrc['articleid']=$id;
           
           $rec=db("article_content")->where("articleid",$id)->update($arrc);
            
    
            
    
            $log['userid']=$user['id'];
            $log['realname']=$user['realname'];
            $log['createtime']=date("Y-m-d H:i:s");
            $log['actionname']="编辑";
            $log['newsid']=$id;
            $log['newstitle']=input("title");
    
            db("article_log")->insert($log);
    
            if($res || $rea || $rec){
                $this->success("修改成功",url("wenjian"));
            }else{
                $this->error("修改失败",url("wenjian"));
            }

        }else{
            $this->error("非法操作",url("wenjian"));
        }
       
    }
   
    
    public function delete_allw()
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
        $this->redirect("wenjian");
    }
    public function adoptw()
    {
        $id=\input("id");
       
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("article_info")->where("id",$v)->find();
            if($re){
                $del=db("article_info")->where("id",$v)->setField("reviewstatus",1); 

                $uid=session("uid");
                $user=db("manager_info")->where("id=$uid")->find();
                $log['userid']=$user['id'];
                $log['realname']=$user['realname'];
                $log['createtime']=date("Y-m-d H:i:s");
                $log['actionname']="审核通过";
                $log['newsid']=$re['id'];
                $log['newstitle']=$re['title'];

                db("article_log")->insert($log);
            }
        }
        $this->redirect("wenjian");
    }
    public function refusew()
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
        $this->redirect("wenjian");
    }

    public function bumf()
    {
        
        $uid=session("uid");
        $user=db("manager_info")->where("id=$uid")->find();
        $rolesjson=json_decode($user['rolesjson']);
        $siteid=$rolesjson->SiteId;

       

        $status=input("status");  
        $keywords=input("keywords");
   

        
        
        
      
        if( $keywords || $status ){
           
           
            if($keywords){
                $where['title']=array("like",'%'.$keywords.'%');
            }else{
                $keywords="";
            }
            
            if($status != 3){
                $where['reviewstatus']=array('eq',$status);
            }
          


        }else{
            $status=3;
           
            $keywords="";
            $name="全部";
          
            $where=[];
           
        }
     
        $this->assign("status",$status);
        $this->assign("keywords",$keywords);
   

        

      //  var_dump($where);exit;

       $list =db("article_info")->where("arttype","wenjian")->where("zhongguogovid != 0")->where($where)->order(['createtime desc'])->paginate(20,false,['query'=>request()->param()]);
       $this->assign("list",$list);
       
       $page=$list->render();
       $this->assign("page",$page);
       
        return $this->fetch();
    }
   
   
    
    public function delete_allb()
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
        $this->redirect("bumf");
    }
    public function adoptb()
    {
        $id=\input("id");
       
        $arr=explode(",",$id);
        foreach($arr as $v){
            $re=db("article_info")->where("id",$v)->find();
            if($re){
                $del=db("article_info")->where("id",$v)->setField("reviewstatus",1); 
                $uid=session("uid");
                $user=db("manager_info")->where("id=$uid")->find();
                $log['userid']=$user['id'];
                $log['realname']=$user['realname'];
                $log['createtime']=date("Y-m-d H:i:s");
                $log['actionname']="通过审核";
                $log['newsid']=$re['id'];
                $log['newstitle']=$re['title'];

                db("article_log")->insert($log);
            }
        }
        $this->redirect("bumf");
    }
    public function refuseb()
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
        $this->redirect("bumf");
    }
    public function changes()
    {
        $id=input("id");
        $re=db("article_info")->where("id",$id)->find();
        if($re){
            if($re['b_banner'] == 0){
                db("article_info")->where("id",$id)->setField("b_banner",1); 
            }else{
                db("article_info")->where("id",$id)->setField("b_banner",0); 
            }
            echo '0';
        }else{
            echo '1';
        }
    }

    public function hot(){
        $list=db('article_hot')->paginate(10);
        $this->assign('list',$list);
        return $this->fetch();
    }

    public function save_hot(){
        if($this->request->isAjax()){
            $id=$_POST['id'];
            if($id){
                $data['title']=$_POST['title'];
                $res=db('article_hot')->where("id=$id")->update($data);
                if($res){
                    $this->success("修改成功！",url('hot'));
                }else{
                    $this->error("修改失败！",url('hot'));
                }
            }else{
                $data['title']=$_POST['title'];
                $re=$res=db('article_hot')->insert($data);
                if($re){
                    $this->success("添加成功！",url('hot'));
                }else{
                    $this->error("添加失败！",url('hot'));
                } 
            }
            
        }else{
            $this->success("非法提交",url('hot'));
        }
    }

    public function modify(){
        $id=input('id');
        $re=db('article_hot')->where("id=$id")->find();
        echo json_encode($re);
    }

    public function hot_change(){
        $id=input('id');
        $status=input('status');
        if($status == 1){
            $data['status']=0;
        }else{
            $data['status']=1;
        }
        db('article_hot')->where("id=$id")->update($data);
    }













}