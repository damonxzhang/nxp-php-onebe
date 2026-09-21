<?php
namespace backend\controllers;

use backend\models\search\BillMaterialsSearch;
use backend\models\BillMaterials;
use backend\models\BillShelf;
use backend\models\UserInfo;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class BillMaterialsController extends BaseController
{

    /**
     * 货架列表
     * @return string
     */
    public function actionIndex()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new BillMaterialsSearch();
        $request['status'] = BillMaterials::STATUS_YES;  
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('index', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * 添加数据
     */
    public function actionCreate()
    {
        if (!Yii::$app->request->isPost) {
            $shelfArr = [];
            $shelfNumber = (new BillShelf())->getShelfArr();
            if($shelfNumber){
                $shelfArr = ArrayHelper::map($shelfNumber, 'id', 'shelf_number');
            }
            return $this->render('create',['shelfArr' => $shelfArr]);
        }
        $post                 = Yii::$app->request->post();
		$shelf_id			  = BillShelf::find();
		$shelf_id->andwhere(['shelf_number'=>$post['shelf_id']]);
		$shelf_id->limit(1);
		$shelf_id_data = $shelf_id->asArray()->all();
		if (!$shelf_id_data) {
			return $this->json(100, '存储单元编号错误!');
			
		}
		// 判断单号是否有效
		$materials_number_2 = substr($post['materials_number'] , 0 , 2);
		if($materials_number_2 != 'TJ' && $materials_number_2 != 'YZ' && $materials_number_2 != 'YL' && $materials_number_2 != '8E' ){
			return $this->json(100, '物料单号错误!');

		}
		
        $check                = BillMaterials::findOne(['materials_number' => trim($post['materials_number']),'shelf_id' => $shelf_id_data[0]['id'],'status' => BillMaterials::STATUS_YES]);
        if ($check) {
            return $this->json(100, '物料单号已存在!');
        }
        $userInfo             = UserInfo::findOne($this->getUid());
        $post['create_user']  = $userInfo->id;
		$post['shelf_id']  = $shelf_id_data[0]['id'];
        $materials = (new BillMaterials())->create($post);
        if (!$materials) {
            return $this->json(100, '添加失败!');
        }
        return $this->json(200, '添加成功!');
    }

    /**
     * 修改货架
     * @return string
     */
    public function actionDown()
    {
        $get              = Yii::$app->request->get();
        $userInfo         = UserInfo::findOne($this->getUid());
        $model            = BillMaterials::findOne($get['id']);
        $model->down_user = $userInfo->id;
        $model->status    = BillMaterials::STATUS_NO;
        $model->down_time = date('Y-m-d H:i:s', time());
        //更新数据
        if (!$model->save(false)) {
            return $this->json(100, '下架失败!');
        }
        return $this->json(200, '下架成功!');
    }

    /**
     * 货架列表
     * @return string
     */
    public function actionHistory()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new BillMaterialsSearch();
        $request['status'] = BillMaterials::STATUS_NO;  
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('history', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }
 
}
