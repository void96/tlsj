<?php

namespace app\common\model\block;

use think\Model;
use think\db\Where;
use think\facade\Request;
use think\Db;

class BlockEpBuy extends Model
{
    protected $name = 'block_ep_buy';

    /**
     * 添加买入数据
     * @param int $blockId 货币id
     * @param int $userId 会员id
     * @param float $num 购买数量
     * @param float $price 价格
     * @param int $fee 手续费
     * @return BlockEpBuy
     */
    public function addBlocEpkBuy($blockId, $userId, $num, $price, $fee = 0)
    {
        $buyData = [
            'bid' => $blockId,
            'uid' => $userId,
            'add_time' => time(),
            'num' => $num,
            'fee' => $fee,
            'fee_money' => $num * $price * $fee / 100,
            'price' => $price,
            'money' => $num * $price,
            'status' => 1,
            'stay_num' => $num,
            'lock_id' => 0
        ];
        return $this->create($buyData);
    }

    /**
     * ajax查询数据买入
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getBuyList()
    {
        $where = new Where();
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        Request::param('bid') && $where['bid'] = Request::param('bid');
        $pSize = 5;
        $where['status'] = ['in', '1,2'];
        return $this->where($where)->limit(($p * $pSize) . ',' . $pSize)->order('price asc')->select();
    }

    /**
     * 根据id查询数据
     * @param $id
     * @param array $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEpBuyInfoById($id)
    {
        $where = [
            'id' => $id
        ];
        return $this->where($where)->find();
    }

    /**
     * 根据id修改数据
     * @param $id
     * @param $data
     */
    public function updateEpBuyInfoById($id, $data = [])
    {
        $where = [
            'id' => intval($id)
        ];
        return $this->where($where)->update($data);
    }

    /**
     * Ajax获取数据
     * @param $uid 用户id
     * @param $bid 货币id
     * @param array $field
     * @return array|null|\PDOStatement|string|Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEpBuyFieldById($uid, $field = [])
    {
        $where = new Where();
        $p = Request::param('p') > 0 ? Request::param('p') : 0;
        Request::param('bid') && $where['bid'] = Request::param('bid');
        $where['uid'] = intval($uid);
        $pSize = 10;
        return $this->where($where)->field($field)->limit(($p * $pSize) . ',' . $pSize)->order('price desc')->select();
    }

    /**
     * 更新买入信息状态
     * @param $num
     */
    public function updateBuyStatus($num)
    {

        if ($this->stay_num - $num <= 0) {
            $this->status = 9;
            $this->out_time = time();
        } else {
            $this->status = 2;
        }

        $this->stay_num = Db::raw('stay_num-' . $num);

        $this->save();
    }


}