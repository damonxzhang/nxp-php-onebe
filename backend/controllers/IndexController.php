<?php
namespace backend\controllers;

use backend\models\Menu;
use backend\models\Notice;
use backend\models\NoticeUser;
use backend\models\Role;
use backend\models\Route;
use backend\models\search\NoticeUserSearch;
use backend\models\Submenu;
use backend\models\Token;
use backend\models\User;
use backend\models\UserInfo;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\helpers\Json;

class IndexController extends BaseController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $menu = $this->getMenu();
        $notice = $this->getNotice();
        $userInfo = UserInfo::findOne($this->getUid());
        $name = $userInfo->chinese_name ? $userInfo->chinese_name : '';
        $pidArray = [
            65016794,12069820,65015338,65016488,12028687,12062484,12071199,65015411,65012814,12026261,65009879,12061362,65015407,91007860
           ,65012580,65020660,65021271,65014550,12053126,13960240,65016489,65019139,13963143,70023520,70022973,91004890,65015822,65022276
            ,65011160,12055248,91002672,91002334,65010873,65000724,91004905,91002965,12060498,65005045,91009494,91008723,65015528
        ];
        $url = '';
        if (in_array($userInfo->pid, $pidArray)){
            $token = Token::encode(Yii::$app->user->identity->user_id);
            $url = Yii::$app->params['bbs-url'].'/?user-backlogin&key='.$token;
        }
        return $this->render('index', ['menu' => $menu, 'notice' => $notice, 'name' => $name,'url' => $url]);
    }

    /**
     * @return string
     */
    public function actionWelcome()
    {
        $request      = Yii::$app->request->queryParams;
        $searchModel  = new NoticeUserSearch();
        $data         = $searchModel->search($request);
        $totalCount   = $data->totalCount;
        $dataProvider = $data->getModels();
        $pagination   = new Pagination([
            'totalCount' => $totalCount,
            'pageSize'   => 20,
        ]);
        return $this->render('welcome', [
            'request'      => $request,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'   => $pagination
        ]);
    }

    /**
     * @return string
     */
    public function actionClearCache()
    {
        $cache = Yii::$app->cache;
        $res = $cache->flush();
        if (!$res) {
            return $this->json(100, '刷新失败');
        }
        return $this->json(200, '刷新完成');
    }

    /**
     * 获取权限菜单（并存入缓存）
     * @return array|mixed|\yii\db\ActiveRecord[]
     */
    public function getMenu()
    {
        $userInfo = UserInfo::findOne($this->getUid());
        $role = Role::findOne($userInfo->role_id);
        $menu = Yii::$app->cache->get('menu' . $userInfo->role_id);
//        $menu = false;
        if ($menu)  return $menu;
        $permission = Json::decode($role->permission);
        $model = new Menu();
        $submenuIdArr = $model->getUserSubmenuIdArr();
        $submenus = Submenu::find()->where(['in', 'id', $submenuIdArr])->orderBy(['sort' => SORT_DESC])->asArray()->all();
        $menuIdArr = array_column($submenus, 'menu_id');
        $menuIdArr = array_unique($menuIdArr);
        $menu = Menu::find()->joinWith('submenu')
            ->where(['nxp_menu.is_show' => 1])
            ->andWhere(['in', 'nxp_menu.id', $menuIdArr])
            ->orderBy(['nxp_menu.sort' => SORT_DESC])
            ->asArray()->all();
        foreach ($menu as &$list) {
            foreach ($list['submenu'] as &$item) {
                $item['rule'] = Route::find()->where(['submenu_id' => $item['id']])
                    ->andWhere(['status' => 1])
                    ->andWhere(['in', 'id', $permission])
                    ->asArray()->all();
            }
        }
        Yii::$app->cache->set('menu' . $userInfo->role_id, $menu);
        return $menu;
    }

    public function getNotice()
    {
        $uid = $this->getUid();
        $query = new Query();
        $query->select(['u.title']);
        $query->from(NoticeUser::tableName() . ' nu');
        $query->leftJoin(Notice::tableName() . ' u', 'nu.notice_id = u.id');
        $query->where(['nu.user_id' => $uid,'nu.status' => NoticeUser::STATUS_UNREAD]);
        return $query->column();
    }

    /**
     * 获取消息通知数量
     * @return string
     */
    public function actionNoticeNumber()
    {
        $number = $this->getNotice();
        $data = empty($number) ? 0 : count($number);
        return $this->json(200, $data);
    }
}