<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/11/17
 * Time: 21:38
 */

namespace app\api\controller;


use app\BaseController;
use app\common\controller\CartLogic;
use app\common\controller\CommonController;
use app\common\model\Cart;
use app\common\model\Address;
use app\common\model\OrdersShop;
use app\common\model\OrdersPay;
use app\common\model\ProductSku;
use app\common\model\Product;
use app\common\model\User;
use app\Request;
use app\common\util\TpshopException;
use think\facade\Db;
use Yansongda\Pay\Pay;

class Cartitem extends BaseController{
    //购物车列表
    public function cartlist(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $cartlist1 = new CartLogic();
        $cartlist1 -> setUserId($uid);
        $cartlist = $cartlist1->getCartList();//用户购物车
        $cartlist['total'] = count((new Cart)::where('user_id',$uid) -> select());
        return apiBack('success', '获取成功', '10000',$cartlist);
    }

    //添加商品到购物车
    public function cartsave(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $type = $request -> post('type');
        $pid = $request -> post('product_id');
        $skuid = $request -> post('skuid');
        $price = $request -> post('price');
        $specvalue = $request -> post('specvalue');
        $quantity = $request -> post('quantity') ?? 1;
        if(empty($pid))  return apiBack('fail', '请选择要购买的商品', '10004');
        switch ($type){
            case 'list':
                //查询商品
                $pskulist = (new ProductSku)::where('product_id',$pid) -> field('id as skuid,price,stock') -> select() -> toArray();//查询产品属性
                $product_spec_info = (new Product())::where('id',$pid) -> find() -> value('product_spec_info');
                $specvalue = json_decode($product_spec_info,1)['list'];
                $data = [
                    'user_id' => (int)$uid,
                    'product_id' => (int)$pid,
                    'sku_id' => (int)$pskulist[0]['skuid'],
                    'quantity' => (int)$quantity,
                    'specvalue'=> $specvalue[0],
                    'price' => floatval($pskulist[0]['price']),
                    'createtime' => time()
                ];
                $stock = $pskulist[0]['stock'];
                break;
            case 'details':
                //查询
                $skunum = (new ProductSku)::where('id',$skuid) -> field('stock') -> find() -> toArray();
                $data = [
                    'user_id' => (int)$uid,
                    'product_id' => (int)$pid,
                    'sku_id' => (int)$skuid,
                    'quantity' => (int)$quantity,
                    'specvalue'=> $specvalue,
                    'price' => floatval($price),
                    'createtime' => time()
                ];
                $stock = $skunum['stock'];
                break;
        }
        //0元购产品
        $pro0ids = Product::where('buy0', 1)->column('id');
        $cartinfo = (new Cart)::where(['user_id'=>$uid,'product_id'=>$pid,'sku_id'=>$data['sku_id'],'specvalue'=>$data['specvalue']]) ->find();
        if(empty($cartinfo)){
            $res =  (new Cart) -> save($data);
        }else{
            //判断0元购商品只能添加一个！
            if (in_array($pid, $pro0ids)) {
                return apiBack('fail', '0元购商品只能添加一个！', '10004');
            }
            $quantity = $cartinfo -> quantity + (int)$quantity;
            $data = [
                'quantity' => $quantity,
            ];
            $res =  (new Cart)::where(['id'=>$cartinfo->id])
                -> update($data);
        }
        if($res){
            $res=app('redis')->llen('goods_store'.$pid.$data['sku_id']);
            $count=$stock - $res;
            for($i=0;$i<$count;$i++){
                app('redis') ->lpush('goods_store'.$pid.$data['sku_id'],1);
            }
            return apiBack('success', '添加成功', '10000');
        }else{
            return apiBack('fail', '添加失败', '10004');
        }
    }


    public function updateCart(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $cartid = $request ->post('cartid');
        $number = $request ->post('number') ?? 1;
        if(!$cartid) return apiBack('fail', '请选择所选的商品', '10004');
        $res = (new Cart)::where('id',$cartid) -> update(['quantity'=>(int)$number]);
        if($res){
            return apiBack('success', '更新成功', '10000');
        }else{
            return apiBack('fail', "更新失败", '10004');
        }
    }

    //删除购物车商品
    public function cartdel(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $cartid = $request ->post('ids');
        $uid = $request ->post('uid');
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($uid);
        try{
            $cartLogic -> clear($cartid);
            return apiBack('success', '删除成功', '10000');
        }catch (TpshopException $t){
            return apiBack('fail', '删除失败', '10004');
        }
    }

    public function goskk(Request $request){
        // 取出用户
        if( !$this->userPop())
        {
            $this->insertLog('no users buy');
            return;
        }
        /// 判断是否重复下订单
        if( in_array( $this->user_id, $this->redis->sMembers( 'users_buy')))
        {
            $this->insertLog($this->user_id.' repeat place order');
            return;
        }
        // 检查库存
        $count=$this->redis->lpop('goods_store');
        if(!$count){
            $this->insertLog($this->user_id .' error:no store redis');
            return;
        }

        // 开启事务 确保订单不会重复下

        //生成订单
        $order_sn=$this->build_order_no();

        $order_rs = Db::name( 'order')
            ->insert([
                'order_sn' => $order_sn,
                'user_id' => $this->user_id,
                'goods_id' => $this->goods_id,
                'sku_id' => $this->sku_id,
                'price' => $this->price
            ]);

        //库存减少
        $store_rs = Db::name( 'store')
            ->where( 'sku_id', $this->sku_id)
            ->Dec( 'number', $this->number);
        if($store_rs){
            // 用户购买成功，把user_id存入set集合缓存。拿来判断该用户是否会继续下单
            $this->redis->sAdd( 'users_buy', $this->user_id);
            $this->insertLog($this->user_id .' 库存减少成功');
            return;
        }else{
            $this->insertLog($this->user_id .' 库存减少失败');
            return;
        }
    }


    /**
     * 购物车第二步确定页面
     */
    public function cart2(Request $request){ //cartconfirm
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid/d');
        $cartid = $request -> post('cartid');
        $action = $request -> post('action'); // 行为
        $pid = $request -> post('pid/d');// 商品id
        $skuid = $request -> post('skuid/d');//商品规格id
        $specvalue = $request -> post('specvalue');
        $quantity = $request -> post('quantity/d') ?? 1;
        $is_rush = $request -> post('is_rush/d');
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($uid);
        //立即购买
        if($action == 'buy_now'){
            if(!$pid)return apiBack('fail', '请选择商品', '10004');
            if(!$specvalue)return apiBack('fail', '请选择规格属性', '10004');
            if(!$skuid)return apiBack('fail', '请选择规格Id', '10004');
            $cartLogic->setGoodsModel($pid)
                -> setSpecvalue($specvalue)
                ->setProductSku($skuid)
                ->setGoodsBuyNum($quantity);
            try{
                if($is_rush == 1){
                    $cartList = $cartLogic->buyNowms();
                }else{
                    $cartList = $cartLogic->buyNow();
                }
            }catch (TpshopException $t){
                $error = $t->getErrorArr();
                return apiBack('fail', $error['msg'], '10004');
            }
        }else{
            if(empty($cartid)){
                return apiBack('fail', '你的购物车没有选中商品', '10004');
            }
            $cartList = $cartLogic->getCartList($cartid); // 获取用户选中的购物车商品 $cartList['cart']
//            $cartList['cartList'] = $cartLogic->getCombination($cartList['cartList']);  //找出搭配购副商品
        }
        //查询默认收货地址
        $addressinfo = (new Address)::where(['user_id'=>$uid,'is_defult'=>1,'status'=>1]) -> field('id as addressid,contact_name,contact_phone,disarea,address') -> find();
        if(empty($addressinfo)){
            $cartList['addressinfo'] = [];   //return apiBack('fail', '请先去设置地址', '10004');
        }else{
            $cartList['addressinfo'] = $addressinfo -> toArray();
        }
        //获取优惠卷数量
//        $couponnum = count((new coupon)::where('user_id',$uid)->select());
        $cartList['couponsnum'] =  0; //优惠卷  $couponnum
        //获取用户积分
        $cartList['integral'] = (new User)::where(['id'=>$uid]) -> value('money'); //积分
//        $cartGoodsList = get_arr_column($cartList['cart'],'product');
//        $cartPriceInfo = $cartLogic->getCartPriceInfo($cartList['cart']);  //初始化数据。商品总额/节约金额/商品总共数量
//        $userCouponList = $couponLogic->getUserAbleCouponList($uid, $cartGoodsId, $cartGoodsCatId);//用户可用的优惠券列表
//        $cartList = array_merge($cartList,$addressinfo -> toArray());
        return apiBack('success', '获取成功', '10000',$cartList);
    }



    /**
     * 创建订单
     * @param Request $request
     * @return \think\response\Json
     */
    public function createOrder (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $post = $request->post();
        $cartids =  $request->post('cartid');
        $data = $post['data'];
        $address = $post['address_id'];
        $discount = $post['discount'];
        //总价
        $total_price = $post['total_price'];
        $order = [];
        $order_ids = [];
        $common = new CommonController();
        $pay_order_no = $common->create_order_no();
        $common::beginTrans();
        try {
            foreach ($data as $k => $v) {
                $order['user_id'] = $post['uid'];
                $order['addressid'] = $address;
                $order['order_sn'] = $common->create_order_no();
                $order['status'] = 1;
                $order['createtime'] = time();

                $product_price = 0;
                $detail = [];
                foreach ($v as $key => $val) {
                    $detail[$key]['product_id'] = $val['pid'];
                    $detail[$key]['skuid'] = $val['skuid'];
                    $detail[$key]['price'] = $val['price'];
                    $detail[$key]['total_price'] = $val['price'] * $val['quantity'];
                    $detail[$key]['number'] = $val['quantity'];
                    $detail[$key]['specvalue'] = $val['specvalue'];
                    $detail[$key]['speckey'] = $val['speckey'];
                    $detail[$key]['createtime'] = time();

                    $product_price += $val['price'] * $val['quantity'];
                }
                if ($k == 0) {
                    $discountPrice = $product_price - $discount;
                    if ($discountPrice < 0) {
                        $order['payment_price'] = 0.01;
                    } else {
                        $order['payment_price'] = $discountPrice;
                    }
                    $order['goods_price'] = $product_price;
                    $order['amount_price'] = $discount;
                } else {
                    $order['payment_price'] = $product_price;
                    $order['goods_price'] = $product_price;
                    $order['amount_price'] = 0;
                }
                $order_id = Db::name('orders')->insertGetId($order);
                array_push($order_ids, $order_id);
                foreach ($detail as $key => $value) {
                    $detail[$key]['order_id'] = intval($order_id);
                }
                // dump($detail);die;
                Db::name('orders_detail')->insertAll($detail);
            }

            $pay_order = [
                'order_sn' => $pay_order_no,
                'order_ids' => implode(',', $order_ids),
                'createtime' => time(),
                'pay_price' => $total_price
            ];
            Db::name('orders_pay')->insert($pay_order);
            $user = User::where('id', $post['uid'])->find();
            $user->money -= $discount;
            $user->save();
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
        $openid = \app\common\model\User::where('id', $post['uid'])->value('openid');
        $payment = new Payment();
        $res = $payment -> pay($pay_order_no, $total_price, '小香铺购物下单', $openid);
        if ($cartids) {
            $this->delCart($cartids);
        }
        if ($res) {
            return apiBack('success', '成功', '10000', $res);
        } else {
            return apiBack('fail', '获取订单失败', '10001');
        }
    }

    /**
     * 订单款支付
     * @param Request $request
     * @return \think\response\Json
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function orderPay (Request $request)
    {
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $total_price = $request->post('total_price');
        $pay_order_no = $request->post('order_no');
        $uid = $request->post('uid');
        $openid = \app\common\model\User::where('id', $uid)->value('openid');
        $payment = new Payment();
        $res = $payment -> pay($pay_order_no, $total_price, '小香铺购物下单', $openid);
        if ($res) {
            return apiBack('success', '成功', '10000', $res);
        } else {
            return apiBack('fail', '获取订单失败', '10001');
        }
    }

    /**
     * 删除购物车
     * @param $ids
     * @return bool
     */
    private function delCart ($ids)
    {
        $ids = explode(',', $ids);
        Cart::where('id', 'in', $ids)->delete();
        return true;
    }




    /**
     * 更新购物车，并返回计算结果
     */
    public function AsyncUpdateCart(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $cart = input('cart/a', []);
        $cartLogic = new CartLogic();
    }

    /**
     *  购物车加减
     */
    public function changeNum(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $cartid = $request -> post('cartid');
        $quantity = $request -> post('quantity');
        if (empty($cartid))   return apiBack('fail', '请选择要更改的商品', '10004');
        $result = (new CartLogic())->changeNum($cartid,$quantity);
        if($result){
            return apiBack('success', '更新数量成功', '10000');
        }
    }






    //选中状态
    public function cartselect(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid/d');
        $cartid = $request -> post('id/d');
        $selected = $request -> post('selected/d');
        if($selected == 1){
            $result1 = (new Cart())::where(['id'=>$cartid,'user_id'=>$uid]) -> update(['selected' => 1]);
        }else{
            $result = (new Cart())::where(['id'=>$cartid,'user_id'=>$uid]) -> update(['selected' => 1]);
        }
        if($result1){
            return apiBack('success', '选中成功', '10000');
        }
    }


}
