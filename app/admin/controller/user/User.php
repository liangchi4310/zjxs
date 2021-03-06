<?php
declare (strict_types = 1);
namespace app\admin\controller\user;
use app\common\controller\Backend;
use think\Request;
use think\facade\Db;
class User extends Backend {
    protected $model = null;
    public function initialize(){
        $this -> model = new \app\common\model\User();
        return parent::initialize(); // TODO: Change the autogenerated stub
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
                ->limit($offset,$limit)
                ->select() ->toArray();
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
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

    //删除
    public function del($ids = ''){
        if ($ids) {
            $this->model -> destroy($ids);
            $this->success();
        }
        $this->error();
    }
}
