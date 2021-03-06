<?php
/*
*@file UserController.php
*@author pengzhi (com@baidu.com)
*@date 2014-7-19
*@brief
*/
namespace blog\pengzhi\controllers;

class UserController extends \Phalcon\Mvc\Controller
{
	const PHOTO_DIR = "/blog/blog/photo/";
	public function indexAction()
	{
	   //session 获取用户名
	  // $this->flash->notice($this->session->get("username"));
	    $this->session->set("username", "asdf");
	   if ($this->session->has("username")) {
	   	$user = \blog\pengzhi\models\User::findFirst("username='".
	   			$this->session->get("username")."'");
	   	//$this->logger->log("username:{$usrname}", \Phalcon\Logger::NOTICE);
	   	if ($user === false) $this->logger->error("not found");
	   	if (count($user) === 1) {
	   		$info = $user->objectToArray();
	   		//echo json_encode($info);
	   		//$this->logger->notice(json_encode($info));
	   		foreach($info as $k=>$v) {
	   			$this->view->setVar($k,$v);
	   		}
	   	}
	   } else {
	   	  $this->logger->log("You must login first", \Phalcon\Logger::ERROR);
	   	  $this->flash->error("You must login first");
	   }
		
		
	}
	public function saveAction()
 	{		
 	    $user = new \blog\pengzhi\models\User();
 	 
 		try {
 			
 			$user->guid = \blog\librarys\Guid::newGuid();
 			$user->username = $this->request->getPost("username");
 			$user->sex = $this->request->getPost("sex");
 			$user->age = $this->request->getPost("age");
 			$user->email = $this->request->getPost("email");
 			$user->address = $this->request->getPost("address");
 			$user->password = $this->request->getPost("password");
 			//图像文件处理 二进制读取临时文件 存储进mysql
 			if ($this->request->hasFiles() == true) {
 				//Print the real file names and their sizes
 				foreach ($this->request->getUploadedFiles() as $file){
 					//echo $file->getName(), " ", $file->getSize()," ",$file->getTempName(),"\n";
 					$fileName = $file->getName();
 					$file->moveTo(PHOTO_DIR.$fileName);
 					$size = $file->getSize();
 					
 					
 					$fp = fopen($fileName ,"rb");
 					$data = addslashes(fread($fp,$size));
 					fclose($fp);
 					//echo json_encode($data);
 					break;
 			    }
 			}
 			$user->image = $data;
 			$user->mod_time = $user->add_time = date("Y/m/d H:i:s");
 			if($user->isValidated() == true) {
 				if ($user->save() === true) {
 					$this->logger->notice("save success!");
 				} else {
 					$msg = "";
 					$i = 0;
 					foreach($user->getMessages() as $message) {
 						//$msg[$i] = $message;
 						//$i++;
 						$msg.=" ";
 						$msg.=$message;
 					}
 					$this->flash->error($msg);
 					$this->logger->error("save fail:".json_encode($msg));
 				}
 					
 			}
 			$type = $_FILES["file"]["type"];
 			header("Content-type:${type}");
 			echo  $data;
 		}catch(\Exception $err) {
 			$msg = "save exception:".$err->getMessage();
 			$this->logger->error($msg);
 		}
 		
        
	}
	
}