<?php
namespace app\index\controller;

class Search extends BaseHome{
    public function search_result(){
        $search=input('search');
        $hid=input('hid');
        $week=input('week');
        $month=input('mouth');
        $year=input('year');
        
        if($search){
            $map['title|author|source']=['like','%'.$search.'%'];
            $res=db('article_info')->field('id,title,createtime')->where("reviewstatus",1)->where($map)->order('id desc')->paginate(10);
        }elseif($hid){
            $hot=db('article_hot')->where("id=$hid")->find();
            $search=$hot['title'];
            $map['title|author|source']=['like','%'.$search.'%'];
            $res=db('article_info')->field('id,title,createtime')->where("reviewstatus",1)->where($map)->order('id desc')->paginate(10);
        }elseif($week){
            // $time=date('Y-m-d',time());
            // $map['createtime']=['<= time',$time];
            $res=db('article_info')->field('id,title,createtime')->where("reviewstatus",1)->whereTime("createtime","last week")->order('id desc')->paginate(10);
        }elseif($year){
            $time=date('Y',time());
            $map['createtime']=['<= time',$time];
            $res=db('article_info')->field('id,title,createtime')->where("reviewstatus",1)->whereTime("createtime","m")->order('id desc')->paginate(10);
        }elseif($month){
            $time=date('Y-m',time());
            $map['createtime']=['<= time',$time];
            $res=db('article_info')->field('id,title,createtime')->where("reviewstatus",1)->whereTime("createtime","y")->order('id desc')->paginate(10);
        }
        else{
            $map=[];
            $res=db('article_info')->field('id,title,createtime')->where("reviewstatus",1)->where($map)->order('id desc')->paginate(10);

        }
        
        $this->assign('res',$res);

        $page=$res->render();
        $this->assign('page',$page);

        $hot=db('article_hot')->where('status=1')->select();
        $this->assign('hot',$hot);

        return $this->fetch();
        
    }
}