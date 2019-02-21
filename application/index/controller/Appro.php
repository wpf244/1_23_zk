<?php
namespace app\index\controller;

class Appro extends BaseHome
{
    public function index()
    {
        //置顶
        $retop=db("article_info")->where(['reviewstatus'=>1])->order("id desc")->limit(0,4)->find();
        $this->assign("retop",$retop);
       
        //标题轮播
        $rebs=db("article_category")->alias("a")->field("a.id as aid,articleid,category_code,b.id,reviewstatus,bannerid,title,coverimage")->where(['reviewstatus'=>1])->where("category_code","zkyw")->join("article_info b","a.articleid=b.id")->group("a.articleid")->group("b.time")->order("a.articleid desc")->limit(1,4)->select();
        $this->assign("rebs",$rebs);
       
        //轮播图
       $ret=db("article_category")->alias("a")->field("a.id as aid,articleid,category_code,b.id,reviewstatus,bannerid,title,coverimage")->where(['reviewstatus'=>1,'bannerid'=>1])->where("category_code","zkyw")->join("article_info b","a.articleid=b.id")->group("articleid")->order("a.articleid desc")->limit(0,5)->select();        
      //  $ret=db("article_info")->where(['reviewstatus'=>1,'bannerid'=>1])->order("id desc")->limit("0,5")->select();
        $this->assign("ret",$ret);
        
        
        //周口要闻       
        $catey=db("category_info")->field("id,code,status")->where(["code"=>"zkyw","status"=>0])->find();
        $yid=$catey['id'];
        
        $rey=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>$yid])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,8)->select();
        $this->assign("rey",$rey);

        //国务院信息
        $reg=db("article_info")->field("id,title,Unix_timestamp(createtime),arttype,zhongguogovid")->where(["arttype"=>"wenjian","zhongguogovid"=>["neq",0]])->order("createtime desc")->limit(0,8)->select();
        $this->assign("reg",$reg);
        

        //部门动态
        $cateb=db("category_info")->field("id,code,status")->where(["code"=>"bmdt","status"=>0])->find();
        $bid=$cateb['id'];

        $cates=db("category_info")->where(["parentid"=>$bid,"status"=>0])->select();
        $arr=array();
        $arr[]=$bid;
        foreach($cates as $vs){
           $arr[]=$vs['id']; 
        }
        
        $reb=db("article_category")->alias("a")->field("a.categoryid,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>["in",$arr]])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,8)->select();
        $this->assign("reb",$reb);

        //县区动态
        $catex=db("category_info")->field("id,code,status")->where(["code"=>"xqdt","status"=>0])->find();
        $xid=$catex['id'];

        $catex=db("category_info")->where(["parentid"=>$xid,"status"=>0])->select();
        $arrx=array();
        $arrx[]=$xid;
        foreach($catex as $vx){
           $arrx[]=$vx['id']; 
        }
        
        $rex=db("article_category")->alias("a")->field("a.categoryid,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>["in",$arrx]])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,8)->select();
        $this->assign("rex",$rex);

        //通知公告
        $catet=db("category_info")->field("id,code,status")->where(["code"=>"tzgg","status"=>0])->find();
        $tid=$catet['id'];
        
        $rets=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>$tid])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,8)->select();
        $this->assign("rets",$rets);


        //政务公开
        $zwgk=db("category_info")->field("id,code,status")->where(["code"=>"zwgk","status"=>0])->find();
        $zid=$zwgk['id'];

        //公开指南
        $gkzn=db("category_info")->field("id,code,status")->where(["code"=>"gkznhml","status"=>0])->find();
        $gid=$gkzn['id'];

        $gkzhs=db("category_info")->field("id,code,status,name,iconname,orderid,categoryurl")->where(["parentid"=>$gid,"status"=>0])->order(["orderid desc","id asc"])->select();
        $this->assign("gkzhs",$gkzhs);

        //重点信息公开
        $zdxx=db("category_info")->field("id,code,status")->where(["code"=>"zwgkzdxxgk","status"=>0])->find();
       
        $zid=$zdxx['id'];

        $zdxxs=db("category_info")->field("id,code,status,name,iconname,orderid,categoryurl")->where(["parentid"=>$zid,"status"=>0])->order(["orderid desc","id asc"])->select();
        $this->assign("zdxxs",$zdxxs);

        $newss=db("category_info")->field("id,code,status,name,iconname,orderid,categoryurl")->where(["parentid"=>$zid,"status"=>0])->order(["orderid desc","id asc"])->select();
        foreach($newss as $nk => $ns){ 
            $newss[$nk]['list']=db("category_info")->where(['status'=>0,"parentid"=>$ns['id']])->order(["id desc"])->limit("0,6")->select();
        }
        $this->assign("newss",$newss);

        //基础信息公开
        $jcxxgk=db("category_info")->field("id,code,status")->where(["code"=>"jcxxgk","status"=>0])->find();
        $jid=$jcxxgk['id'];

        $jcxxgks=db("category_info")->field("id,code,status,name,iconname,orderid,categoryurl")->where(["parentid"=>$jid,"status"=>0])->order(["orderid desc","id asc"])->select();
        $this->assign("jcxxgks",$jcxxgks);

        //便民服务

        $bmfu=db("link_info")->where(["parentid"=>0,"linktype"=>"bmfw"])->select();
        $this->assign("bmfu",$bmfu);

        //重点业务
        $zdyw=db("category_info")->field("id,code,status")->where(["code"=>"zdxm","status"=>0])->find();
        $did=$zdyw['id'];
     //   var_dump($did);
        
        $zdyws=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>$did])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,8)->select();
        $this->assign("zdyws",$zdyws);

        //政务服务轮播
        $lb=db("lb")->where("fid=1")->select();
        $this->assign("lb",$lb);

        //走进周口轮播
        $lbz=db("lb")->where("fid=2")->select();
        $this->assign("lbz",$lbz);

        //周口市情
        $zksq=db("category_info")->field("id,code,status")->where(["code"=>"zksq","status"=>0])->find();
        $kid=$zksq['id'];
     
        
        $zksqs=db("article_category")->alias("a")->field("a.categoryid as categoryids ,a.articleid,content")->where(["a.categoryid"=>$kid])->join("article_content b","a.articleid=b.articleid")->find();
        $this->assign("zksqs",$zksqs);

        //历史概况
        $lsgk=db("category_info")->field("id,code,status")->where(["code"=>"lsgk","status"=>0])->find();
        $lid=$lsgk['id'];
     
        
        $lsgks=db("article_category")->alias("a")->field("a.categoryid as categoryids ,a.articleid,content")->where(["a.categoryid"=>$lid])->join("article_content b","a.articleid=b.articleid")->find();
        $this->assign("lsgks",$lsgks);

        //自然资源
        $zrzy=db("category_info")->field("id,code,status")->where(["code"=>"zrzy","status"=>0])->find();
     
        $zrzys=db("article_category")->alias("a")->field("a.categoryid as categoryids ,a.articleid,content")->where(["a.categoryid"=>$zrzy['id']])->join("article_content b","a.articleid=b.articleid")->find();
        $this->assign("zrzys",$zrzys);

        //行政区划
        $xzqh=db("category_info")->field("id,code,status")->where(["code"=>"xzqh","status"=>0])->find();
     
       // $xzqhs=db("article_category")->alias("a")->field("a.categoryid as categoryids ,a.articleid,content")->where(["a.categoryid"=>$xzqh['id']])->join("article_content b","a.articleid=b.articleid")->order("a.id desc")->find();
       $xzqhs=db("article_content")->where("articleid",6304)->find(); 
       $this->assign("xzqhs",$xzqhs);

       //发展周口
       $fzzk=db("category_info")->field("id,code,status")->where(["code"=>"fzzk","status"=>0])->find();
       $fzzks=db("category_info")->field("id,status")->where(["parentid"=>$fzzk['id'],"status"=>0])->select();
       $arrf=array();
       foreach($fzzks as $v){
          $arrf[]=$v['id'];
       }
       $fzzkss=db("category_info")->field("id,name,status,iconname")->where(["parentid"=>["in",$arrf],"status"=>0])->order("orderid desc")->select();
       $this->assign("fzzkss",$fzzkss);

       //周口印象
       $zkyx=db("category_info")->field("id,code,status")->where(["code"=>"zkyx","status"=>0])->find();
       $zkyxs=db("category_info")->field("id,name,status")->where(["parentid"=>$zkyx['id'],"status"=>0])->order("orderid desc")->limit(0,4)->select();

       $this->assign("zkyxs",$zkyxs);
       
        //人文周口
        $rwzk=db("category_info")->field("id,code,status")->where(["code"=>"rwzk","status"=>0])->find();
        $rid=$rwzk['id'];
     //   var_dump($did);
        
        $rwzks=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,createtime,reviewstatus")->where(['reviewstatus'=>1,"a.categoryid"=>$rid])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,20)->select();
        $this->assign("rwzks",$rwzks);
        $this->assign("rid",$rid);

        //精彩周口
        $jczk=db("category_info")->field("id,code,status")->where(["code"=>"jczk","status"=>0])->find();
        $cid=$jczk['id'];
     //   var_dump($did);
        
        $jczks=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,reviewstatus,coverimage")->where(['reviewstatus'=>1,"a.categoryid"=>$cid])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,10)->select();
        $this->assign("jczks",$jczks);

         //旅游景点
         $lyjd=db("category_info")->field("id,code,status")->where(["code"=>"lyjd","status"=>0])->find();
         $yid=$lyjd['id'];
      //   var_dump($did);
         
         $lyjds=db("article_category")->alias("a")->field("a.categoryid as categoryids ,articleid,b.id,title,reviewstatus,b.coverimage")->where(['reviewstatus'=>1,"a.categoryid"=>$yid])->join("article_info b","a.articleid=b.id")->order("id desc")->limit(0,10)->select();
         $this->assign("lyjds",$lyjds);

         //专题专栏
         $ztzl=db("spec")->where("status",1)->order("id desc")->select();
         $this->assign("ztzl",$ztzl);

        return $this->fetch();
    }
}