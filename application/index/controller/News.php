<?php
namespace app\index\controller;

class News extends BaseHome
{
    public function index()
    {
        //新闻中心
        // $rex=db("category_info")->field("id,name,status")->where(["code"=>"xwzx","status"=>0])->find();
        // $xid=$rex['id'];
        // $this->assign("rex",$rex);
        
        //新闻分类
        $code=input("code");
        if(empty($code)){
            $id=input("id");
            $re=db("category_info")->field("id,name,status,parentid")->where(["id"=>$id,"status"=>0])->find();
        }else{
            $re=db("category_info")->field("id,name,status,parentid")->where(["code"=>$code,"status"=>0])->find();
        }
        $ids=$re['id'];
        db("category_info")->where("id",$ids)->setInc("clicks",1);
        $this->assign("ids",$ids);
        
        $pid=$re['id'];
        $parentid=$re['parentid'];
        $this->assign("re",$re);
     //   var_dump($re);
        //上级栏目
        $rep=db("category_info")->field("id,name,status")->where(["id"=>$parentid,"status"=>0])->find();
        
        if(empty($rep)){
            $rep=$re;
        }
        $this->assign("rep",$rep);

        //新闻列表
        $parent=db("category_info")->where("parentid",$pid)->select();
        if(empty($parent)){
            $list=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>$pid])->join("article_info b","a.articleid=b.id")->order("id desc")->paginate(10);
            $page=$list->render();       
        }else{
             $arr=array();
             $arr[]=$pid;
             foreach($parent as $vs){
               $arr[]=$vs['id']; 
             }
            
            $list=db("article_category")->alias("a")->field("a.categoryid,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>["in",$arr]])->join("article_info b","a.articleid=b.id")->order("id desc")->paginate(10);
            $page=$list->render();
       
        }
        $this->assign("list",$list);
        $this->assign("page",$page);

        //侧边栏
        $res=db("category_info")->field("id,name,status")->where(["parentid"=>$pid,"status"=>0])->order(["orderid desc","id asc"])->select();
       
        if(empty($res)){
           
            $res=db("category_info")->field("id,name,status")->where(["parentid"=>$parentid,"status"=>0])->order(["orderid desc","id asc"])->select();
            //  var_dump($res,$parentid);exit;
            if(empty($res)){
                $res=db("category_info")->field("id,name,status")->where(["parentid"=>$rep['id'],"status"=>0])->order(["orderid desc","id asc"])->select();

            }
        }
        $this->assign("res",$res);

       

        return $this->fetch();
    }
    public function detail()
    {
        //新闻中心
        $rex=db("category_info")->field("id,name,status")->where(["code"=>"xwzx","status"=>0])->find();
        $this->assign("rex",$rex);

        $nid=input("nid");
        $re=db("article_info")->where(["id"=>$nid,"reviewstatus"=>1])->find();
        
        if($re){
           if(empty($re['url'])){
                db("article_info")->where(["id"=>$nid,"reviewstatus"=>1])->setInc("clicks",1);
                $cate=db("article_category")->where("articleid",$nid)->find();
                $cateid=$cate['categoryid'];
                $info=db("category_info")->where("id",$cateid)->find();

                $content=db("article_content")->where("articleid",$nid)->find();
                $this->assign("content",$content);

                $this->assign("re",$re);
                $this->assign("info",$info);

                //上一篇
            $pre=db("article_category")->alias("a")->field("b.id,title")->where("articleid>$nid")->where("a.categoryid",$cateid)->join("article_info b","a.articleid=b.id")->order("a.id desc")->limit("1")->find();
            $this->assign("pre",$pre);
            
            //下一篇
            $nre=db("article_category")->alias("a")->field("b.id,title")->where("articleid<$nid")->where("a.categoryid",$cateid)->join("article_info b","a.articleid=b.id")->order("a.id asc")->limit("1")->find();
            $this->assign("nre",$nre);

                return $this->fetch();
           }else{
               $url=$re['url'];
               $this->redirect("$url");
           } 
          
        }else{
            $this->redirect("Index/index");
        }
    }
    public function ldzc()
    {
        $id=input("id");
        $reb=db("category_info")->where(["parentid"=>$id,"status"=>0])->order(["orderid desc","id asc"])->select();
        $this->assign("reb",$reb);

        $news=db("category_info")->field("id,parentid,name")->where(["parentid"=>$id,"status"=>0])->order(["orderid desc","id asc"])->select();
        //var_dump($news);exit;
        foreach($news as $k => $v){
            $child=db("category_info")->field("id,parentid,name")->where(["parentid"=>$v['id'],"status"=>0])->select();
            if($child){
               
                $news[$k]['list']=$child;
               
              
            }else{
                $news[$k]['name']="";
                $news[$k]['list']=db("article_category")->alias("a")->field("a.categoryid as categoryids ,subtitle,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>$v['id']])->join("article_info b","a.articleid=b.id")->order("id desc")->select();
            }
        }
      
        $this->assign("news",$news);

        return $this->fetch();
    }
    public function ranks()
    {
        //文章排行
        // $res=db("site_info")->field("id,sitename")->where("status",1)->order("id asc")->select();
        // $arr=array();
        // $arrs=array();
        // foreach($res as $k => $v){
        //     $rec=db("category_info")->field("id")->where(["siteid"=>$v['id'],"status"=>0])->select();
        //     foreach($rec as $vv){
        //         $arr[]=$vv['id'];
        //     }
        //     $arrs[$k][]=$v['sitename'];
        //     $arrs[$k][]=db("article_category")->where("categoryid","in",$arr)->count();
        // }
      //  $res=array_values($arrs);
      //  $this->assign("arrs",json_encode($arrs));

       $infos=db("category_info")->field("id,name,clicks")->where("status",0)->order("clicks desc")->limit("0,9")->select();
       $this->assign("infos",$infos);

       $hot=db("article_info")->field("id,title,clicks,createtime")->where("reviewstatus",1)->order("clicks desc")->limit("0,9")->select();
       $this->assign("hot",$hot);

        
        return $this->fetch();
    }
    public function ztzl()
    {
       
        $id=input("id");
        $re=db("category_info")->field("id,name,status,parentid")->where(["id"=>$id,"status"=>0])->find();
       
        $ids=$re['id'];
        db("category_info")->where("id",$ids)->setInc("clicks",1);
        $this->assign("ids",$ids);
        
        $pid=$re['id'];
        $parentid=$re['parentid'];
        $this->assign("re",$re);
     //   var_dump($re);
        //上级栏目
        $rep=db("category_info")->field("id,name,status")->where(["id"=>$parentid,"status"=>0])->find();
        
        if(empty($rep)){
            $rep=$re;
        }
        $this->assign("rep",$rep);

        //新闻列表
        $list=db("spec")->where("status",1)->order("id desc")->paginate(10);
        $page=$list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);

        //侧边栏
        $res=db("category_info")->field("id,name,status")->where(["parentid"=>$pid,"status"=>0])->order(["orderid desc","id asc"])->limit(0,2)->select();
       
        if(empty($res)){
           
            $res=db("category_info")->field("id,name,status")->where(["parentid"=>$parentid,"status"=>0])->order(["orderid desc","id asc"])->select();
            //  var_dump($res,$parentid);exit;
            if(empty($res)){
                $res=db("category_info")->field("id,name,status")->where(["parentid"=>$rep['id'],"status"=>0])->order(["orderid desc","id asc"])->select();

            }
        }
        $this->assign("res",$res);
       
        return $this->fetch();
    }

    public function ztzls()
    {
       
        $id=input("id");
        $re=db("category_info")->field("id,name,status,parentid")->where(["id"=>$id,"status"=>0])->find();
       
        $ids=$re['id'];
        db("category_info")->where("id",$ids)->setInc("clicks",1);
        $this->assign("ids",$ids);
        
        $pid=$re['id'];
        $parentid=$re['parentid'];
        $this->assign("re",$re);
     //   var_dump($re);
        //上级栏目
        $rep=db("category_info")->field("id,name,status")->where(["id"=>$parentid,"status"=>0])->find();
        
        if(empty($rep)){
            $rep=$re;
        }
        $this->assign("rep",$rep);

        //新闻列表
        $list=db("category_info")->where(["parentid"=>$id,"status"=>0])->order("id desc")->paginate(10);
        $page=$list->render();
        $this->assign("list",$list);
        $this->assign("page",$page);

        //侧边栏
        $res=db("category_info")->field("id,name,status")->where(["parentid"=>$parentid,"status"=>0])->order(["orderid desc","id asc"])->limit(0,2)->select();
       
       
        $this->assign("res",$res);
       
        return $this->fetch();
    }


        
   
    









}