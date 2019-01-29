<?php
namespace app\index\controller;

class Spec extends BaseHome
{
    public function index()
    {
        $id=input("id");
        $re=db("spec")->where("id",$id)->find();
        if($re){
           if($re['status'] == 1){
               $assort=$re['assort'];
               if($assort == "gkbg"){
                   $this->redirect("News/index",array("id"=>181));
               }else{
                $this->redirect("$assort");
               }
              
           }else{
            $this->redirect('Index/index');
           }
        }else{
            $this->redirect('Index/index');
        }
    }
    public function shce()
    {
        $list=db("category_info")->field("id as cid,name")->where(["parentid"=>2714,"status"=>0])->order(["orderid desc","id asc"])->select();
        foreach($list as $k => $v){
            $list[$k]['list1']=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,createtime,reviewstatus,url")->where(['reviewstatus'=>1,"a.categoryid"=>$v['cid']])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,10)->select();
        }
      //  var_dump($list);
        $this->assign("list",$list);
        return $this->fetch();
    }
    public function wclc()
    {
        $banner=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,coverimage,b.id,title,reviewstatus,bannerid")->where(['reviewstatus'=>1,"a.categoryid"=>1810,"bannerid"=>1])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,5)->select();
        $this->assign("banner",$banner);

        $res=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,createtime,reviewstatus,bannerid")->where(['reviewstatus'=>1,"a.categoryid"=>1810])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,9)->select();
        $this->assign("res",$res);

        return $this->fetch();
    }
    public function lh()
    {
        $title=db("category_info")->where(["parentid"=>2376,"status"=>0])->order(["orderid desc","id asc"])->select();
        $this->assign("title",$title);

        $arr=array();
       
        foreach($title as $k => $v){
           
            $arr[]=$v['id'];
        }
       // var_dump($arr);exit;

        $res=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>["in",$arr],'reviewstatus'=>1,"coverimage"=>["neq",""]])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,5)->select();
        $this->assign("res",$res);

        $ress=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>["in",$arr],'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,4)->select();
        $this->assign("ress",$ress);

        //最新报道
        $news=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>2377,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,6)->select();
        $this->assign("news",$news);

        //议案
        $yian=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>2378,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,6)->select();
        $this->assign("yian",$yian);

         //政策
         $zc=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>2379,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,9)->select();
         $this->assign("zc",$zc);

         //图集
         $tj=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>2380,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,5)->select();
         $this->assign("tj",$tj);

         //访谈
         $ft=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>2381,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,8)->select();
         $this->assign("ft",$ft);

        return $this->fetch();
    }
    //2017年两会
    public function lh_yq()
    {
        $id=input("id");
        $title=db("category_info")->where(["parentid"=>$id,"status"=>0])->order(["orderid desc","id asc"])->select();
        $this->assign("title",$title);

        $arr=array();
       
        foreach($title as $k => $v){
           
            $arr[]=$v['id'];
        }
       // var_dump($arr);exit;

        $res=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>["in",$arr],'reviewstatus'=>1,"coverimage"=>["neq",""]])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,5)->select();
        $this->assign("res",$res);

        $ress=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>["in",$arr],'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,4)->select();
        $this->assign("ress",$ress);

        //最新报道
        $news=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>946,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,6)->select();
        $this->assign("news",$news);

        //议案
        $yian=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>947,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,6)->select();
        $this->assign("yian",$yian);

         //政策
         $zc=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>948,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,9)->select();
         $this->assign("zc",$zc);

         //图集
         $tj=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>949,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,5)->select();
         $this->assign("tj",$tj);

         //访谈
         $ft=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>950,'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,8)->select();
         $this->assign("ft",$ft);

        return $this->fetch();
    }
    public function hjbh()
    {
        $title=db("category_info")->where(["parentid"=>2729,"status"=>0])->order(["orderid desc","id asc"])->select();
        $arr=array();
        foreach($title as $k => $v){   
            $title[$k]['list']=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>$v['id'],'reviewstatus'=>1,])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,8)->select();
        }
       
        $this->assign("titles",$title);

        return $this->fetch();
    }
    public function zfzd()
    {
        $title=db("category_info")->where(["parentid"=>1817,"status"=>0])->order(["orderid desc","id asc"])->select();
        $this->assign("title",$title);

        $arr=array();
        $arr[]=1817;
        foreach($title as $k => $v){
           
            $arr[]=$v['id'];
        }
       // var_dump($arr);exit;

        $res=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,subtitle,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>["in",$arr],'reviewstatus'=>1,"bannerid"=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,5)->select();
        $this->assign("res",$res);

        $news=db("article_category")->alias("a")->field("a.categoryid ,articleid,b.id,title,createtime,subtitle,coverimage,reviewstatus,bannerid")->where(["a.categoryid"=>["in",$arr],'reviewstatus'=>1])->join("article_info b","a.articleid=b.id")->group("articleid")->order("b.id desc")->limit(0,7)->select();
        $this->assign("news",$news);

        return $this->fetch();
    }
}