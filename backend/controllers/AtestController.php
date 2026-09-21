<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ServerErrorHttpException;
use yii\helpers\ExecHelper; // Yii2内置执行工具，更安全
use yii\helpers\FileHelper;
use Exception;
class AtestController extends Controller
{
    public $enableCsrfValidation = false;

	// 在Yii2控制器中执行smbclient命令创建文件夹
	public function actionCreateRemoteFolder()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	//	$this->createRemoteShare('10.192.134.255','csam','new_folder_'.date('YmdHis'),'CSAM','654321N');
		// 示例调用
		$result = $this->createDirInNetworkShare(
			'\\\\10.192.134.255\\csam', // 远程共享路径
			'CSAM',                              // 共享账号
			'654321N',                             // 共享密码
			'new_subdir/test/'.date('YmdHis')                     // 要创建的目录（支持多层）
		);

		// 输出结果
		print_r($result);
	}
	
	
     /**
	 * 在Windows网络共享文件夹中创建目录
	 * @param string $remoteShare 远程共享路径（格式：\\\\服务器IP\\共享名）
	 * @param string $username 共享文件夹的账号
	 * @param string $password 共享文件夹的密码
	 * @param string $newDir 要创建的目录（相对于共享根目录的路径）
	 * @return array 操作结果
	 */
	function createDirInNetworkShare($remoteShare, $username, $password, $newDir) {
		// 1. 处理路径格式（确保转义符正确）
		$remoteShare = str_replace('/', '\\', $remoteShare);
		$localDrive = 'Y:'; // 临时映射的驱动器号（避免冲突）
		$targetPath = $localDrive . '\\' . ltrim($newDir, '\\/');

		try {
			// 2. 先断开已有连接（避免1219错误）
			exec("net use \\10.192.134.255\csam /delete /y > nul 2>&1");

			// 3. 映射网络共享（使用指定账号密码）
			$mapCmd = "net use {$localDrive} \"{$remoteShare}\" /user:{$username} \"{$password}\" > nul 2>&1";
			exec($mapCmd, $mapOutput, $mapStatus);
			if ($mapStatus !== 0) {
				throw new Exception("共享映射失败，错误码：{$mapStatus}（请检查账号、密码或共享路径）");
			}

			// 4. 创建目录（递归创建多层目录）
			if (!file_exists($targetPath)) {
				$isCreated = mkdir($targetPath, 0777, true);
				if (!$isCreated) {
					throw new Exception("目录创建失败，权限不足或路径无效");
				}
			}

			// 5. 断开映射（释放驱动器号）
			exec("net use {$localDrive} /delete /y > nul 2>&1");

			return [
				'success' => true,
				'message' => "目录创建成功：{$remoteShare}\\{$newDir}",
				'path' => "{$remoteShare}\\{$newDir}"
			];
		} catch (Exception $e) {
			// 异常时确保断开映射
			exec("net use {$localDrive} /delete /y > nul 2>&1");
			return [
				'success' => false,
				'message' => $e->getMessage()
			];
		}
	}
	
	
	
	
	
	
}