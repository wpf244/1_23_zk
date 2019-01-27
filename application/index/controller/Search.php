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
        }elseif($hid){
            $hot=db('article_hot')->where("id=$hid")->find();
            $search=$hot['title'];
            $map['title|author|source']=['like','%'.$search.'%'];
        }elseif($week){
            $time=date('Y-m-d',time());
            $map['createtime']=['<= time',$time];
        }elseif($year){
            $time=date('Y',time());
            $map['createtime']=['<= time',$time];
        }elseif($month){
            $time=date('Y-m',time());
            $map['createtime']=['<= time',$time];
        }
        else{
            $map=[];
        }
        $res=db('article_info')->field('id,title,createtime')->where($map)->order('id desc')->paginate(10);
        $this->assign('res',$res);

        $page=$res->render();
        $this->assign('page',$page);

        $hot=db('article_hot')->where('status=1')->select();
        $this->assign('hot',$hot);

        return $this->fetch();
        
    }
}