<?php

namespace frontend\controllers;

use backend\models\ShelfApply;
use backend\models\ShelfMaterials;
use backend\models\UserInfo;
use backend\models\Shelf;
use common\models\SendMqtt;
use Yii;
use yii\helpers\ArrayHelper;

class MaterialsApiController extends FrontendBaseController
{

    /**
     * 添加物料接口
     * 请求方式 POST
     */
    public function actionInsert()
    {
        //提交方式为POST
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', 'Request method is incorrect!');
        }
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', 'Request parameters cannot be empty!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "Request parameter'" . $key . "'Can not be empty!");
            }
        }
        //检查参数
        $base = ['cont_no', 'part_no', 'lot_no', 'start_time', 'end_time', 'expire_time'];
        $this->checkParameter($post, $base);

        //检查物料是否已经存在
        $materialsModel = ShelfMaterials::findOne(['cont_no' => trim($post['cont_no'])]);
        if ($materialsModel) {
            return $this->outputJson('false', 'Material number already exists!');
        }
        //添加物料
        $result = (new ShelfMaterials())->create($post);
        if (!$result) {
            return $this->outputJson('false', 'Added failed!');
        }
        return $this->outputJson('true', 'Added successfully!');
    }

    /**
     * 申请物料接口
     * 请求方式 POST
     */
    public function actionApply()
    {
        //检查请求方式
        $isPost = Yii::$app->request->isPost;
        if (!$isPost) {
            return $this->outputJson('false', 'Request method is incorrect!');
        }
        //检查请求参数
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', 'Request parameters cannot be empty!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "Request parameter'" . $key . "'Can not be empty!");
            }
        }
        //检查参数
        $base = ['part_no', 'badge_code', 'count', 'machine_id'];
        $this->checkParameter($post, $base);

        //检查物料批次是否存在
        $materialsModel = ShelfMaterials::findOne(['part_no' => $post['part_no']]);
        if (!$materialsModel) {
            return $this->outputJson('false', 'Part No does not exist!');
        }

        //检查人员是否存在
        $userInfoModel = UserInfo::findOne(['sfc_number' => $post['badge_code']]);
        if (!$userInfoModel) {
            return $this->outputJson('false', 'Personnel information does not exist!');
        }

        //获取申请物料
        $count = (new ShelfMaterials())->getCountMaterials($post);

        //比较申请物料个数
        if ($count < $post['count']) {
            return $this->outputJson('false', 'The number of applications is greater than the quantity in stock!');
        }

        //拼接数据
        $data = [
            'part_no'    => $post['part_no'],
            'user_id'    => $userInfoModel->id,
            'machine_id' => $post['machine_id'],
            'amount'     => $post['count'],
        ];

        //添加申请数据
        $insert = (new ShelfApply())->create($data);
        if (!$insert) {
            return $this->outputJson('false', 'Application failed!');
        }
        return $this->outputJson('true', 'Successful application!');
    }

	/**
	 * 更新货架表状态
	 */
	public function actionUpdateShelf()
	{
        //先把空闲的灯灭掉
        $shelf= Shelf::find()->where(['status' => Shelf::STATUS_FREE])->asArray()->all();
        if (!empty($shelf)) {
			foreach ($shelf as $val) {
                $sendMqtt   = new SendMqtt();
                $sendMqtt->publishMessage($val['mark'], $val['number'], SendMqtt::SHELF_FREE); //给下位机发消息
                usleep(500000);//延迟半秒
            }
		}
		//获取已上架并且为入货架的物料
		$shelfIdList = ShelfMaterials::find()->where(['fettle' => ShelfMaterials::FETTLE_UP, 'status' => ShelfMaterials::STATUS_PENDING])->asArray()->all();
		if (empty($shelfIdList)) {
			return;
		}
        // print_r($shelfIdList);
		foreach ($shelfIdList as $value) {
            // print_r($value['shelf_id']);
            // print_r('-');
			$endTime    = strtotime($value['end_time']); //截止时间
			$expireTime = strtotime($value['expire_time']);//过期时间
			$status     = '';//默认 空闲
			//当前时间大于醒料完成时间 可领取
			if (time() > $endTime) {
				//有可领取的物料不修改灯颜色
				$materials = ShelfMaterials::findOne([
					'shelf_id'=> $value['shelf_id'],
					'fettle'  => ShelfMaterials::FETTLE_UP,
					'status'  => ShelfMaterials::STATUS_USING,
				]);
				if (!$materials) {
					$status = SendMqtt::SHELF_RECEIVE;//可领取
				}
			}
			//获取六个小时后的时间 黄灯预警
			if ($expireTime < strtotime("+6 hours")) {
				$status = SendMqtt::SHELF_WARNING;//预警中
			}
			//获取二分钟后的时间 物料过期
			if ($expireTime < strtotime("+2 minutes")) {
				$status = SendMqtt::SHELF_EXPIRE;//过期
                $materials = ShelfMaterials::findOne(['id' => $value['id']]);
                $materials->status = ShelfMaterials::STATUS_EXPIRED;
                $materials->save();
            }
           
			if (empty($status)) {
				continue;
			}
            // print_r($value);
			//修改货架状态
			$model         = Shelf::findOne($value['shelf_id']);
			$model->status = $status;
			$result        = $model->save();
			if ($result) {
                if($model->floor == 2){
                    $sendMqtt   = new SendMqtt();
                    $shelfModel = Shelf::findOne(['id' => $value['shelf_id']]);
                    $sendMqtt->publishMessage($shelfModel->mark, $shelfModel->number, $status); //给下位机发消息
                    usleep(500000);//延迟半秒
                }
				
			}
		}
		exit;
	}
    /**
     * 申请物料接口
     * 请求方式 POST
     */
    public function actionTwoConfirm()
    {
        //检查请求方式
        $isPost = Yii::$app->request->isPost;

        if (!$isPost) {
            return $this->outputJson('false', 'Request method is incorrect!');
        }
        //检查请求参数
        $post = Yii::$app->request->post();
        if (!$post || !is_array($post)) {
            return $this->outputJson('false', 'Request parameters cannot be empty!');
        }
        //检查参数是否为空
        foreach ($post as $key => $value) {
            if (empty($value)) {
                return $this->outputJson('false', "Request parameter'" . $key . "'Can not be empty!");
            }
        }
        //检查人员是否存在
        $userInfoModel = UserInfo::findOne(['sfc_number' => $post['badge_code']]);

        if (!$userInfoModel) {
            return $this->outputJson('false', 'Personnel information does not exist!');
        }

        //检查参数
        $base = ['cont_no', 'badge_code', 'count', 'machine_id'];
        $this->checkParameter($post, $base);
        //检查物料批次是否存在
        $materialsModel = ShelfMaterials::findAll(['user_id' => $userInfoModel->id,'cont_no' => $post['cont_no'],'fettle' => ShelfMaterials::FETTLE_DOWN]);

        if (!$materialsModel) {
            return $this->outputJson('false', 'Physical state error!');
        }


        $apply_id = array_unique(ArrayHelper::getColumn($materialsModel,'apply_id'));
        $apply =  ShelfApply::findAll(['id' => $apply_id ,'user_id' => $userInfoModel->id,'machine_id' => $post['machine_id']]) ;

        //比较申请物料个数
        if ($apply) {
            return $this->outputJson('false', 'The number of applications is greater than the quantity in stock!');
        }


        return $this->outputJson('true', 'Successful application!');
    }
}

