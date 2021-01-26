<?php

namespace app\admin\controller\small;
use app\common\controller\Backend;
use app\common\model\Category;
class Advs extends Backend
{
    protected $model = null;
    protected $noNeedRight = ['check', 'rulelist'];
    protected $catelist;
//    protected $advmodel =null;

    public function initialize(){
        parent::initialize();
        $this -> model = new \app\admin\model\Adv();
        $this -> catelist= (new Category) -> where(['status'=>'1','type'=>1]) -> order('id asc') -> select();
        $advgroup =[];
        foreach ( $this -> catelist as $k => $v) {
            $advgroup[$v['id']] = $v['cname'];
        }
        $this -> assign('advgroup',$advgroup);
    }
    public function index(){
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select() ->toArray();

            foreach ($this -> catelist as $key => $value) {
                $list[$key]['posation'] = $value['cname'];
                unset($list[$key]['cname']);
            }
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this -> fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $params['createtime'] =time();
                $this ->model -> save($params);
                $this->success();
            }
            $this->error();
        }
        return $this -> fetch();
    }
    public function edit($ids=null){
        $row = $this -> model ->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $params['updatetime'] = time();
                $result = $row->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
                $this->success();
            }
            $this->error();

        }
        $this->assign('row', $row);
        return $this-> fetch();
    }
    /**
     * 删除.
     */
    public function del($ids = ''){
        if ($ids) {
            $this->model -> destroy($ids);
            $this->success();
        }
        $this->error();
    }
    /**
     * 批量更新.
     *
     * @internal
     */
    public function multi($ids = '')
    {
        // 管理员禁止批量操作
        $this->error();
    }

    public function selectpage()
    {
        return parent::selectpage();
    }
}