<?php
require dirname(__DIR__)."/library/ConfigRSA.php";

/**
 * @name AccountController
 * @author IceSolitary
 * @desc Account 控制器
 */
class AccountController extends JsonControllerAbstract
{
    public function init() {
        parent::init();
    }

    /**
     * @desc Action Login，管理员账户登录
     *
     * @return FLASE
     */
    public function loginAction() {
        //  防止全局变量造成安全隐患
        $admin = false;
        $tag = $this->getRequest()->getParam('tag');

        if ($tag !== null){
            // 请求公钥
            if ($tag == 1) {
                $result = APIStatusCode::getOkMsgArray();

                $result['result'] = array('pubkey'=>RSA_public);

                // 编码为 json
                $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                // 处理 json_encode 错误
                if ($json == false) {
                    $error = json_last_error_msg();
                    $json = APIStatusCode::getErrorMsgJson(APIStatusCode::JSON_ENCODE_ERROR, $error);
                }

                // 设置 response
                $this->jsonResponse->setBody($json);

                return FALSE;
            }
            // 返回登录结果
            elseif ($tag == 2) {
                if ($_POST['name'] !== null && $_POST['pw'] !== null)
                {
                    $accountName = $_POST['name'];
                    $pw = $_POST['pw'];

                    //私钥解密password
                    $decodePw = RSAUtils::privDecrypt($pw);

                    //md5加密密码
                    $pwMD5 = md5($decodePw);

                    //从数据库取出密码
                    $pwMD5FromDB = Dao_AdministratorModel::getInstance()->queryOne(['administrator_name'=>$accountName])['password'];

                    if($pwMD5 == $pwMD5FromDB && $pwMD5 !== null){
                        $result = APIStatusCode::getOkMsgArray();

                        $result['result'] = array('msg_login'=>1);

                        // 编码为 json
                        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                        // 处理 json_encode 错误
                        if ($json == false) {
                            $error = json_last_error_msg();
                            $json = APIStatusCode::getErrorMsgJson(APIStatusCode::JSON_ENCODE_ERROR, $error);
                        }

                        //  当验证通过后，启动 Session
                        session_start();
                        //  注册登陆成功的 admin 变量，并赋值 true
                        $_SESSION["admin"] = true;

                        // 设置 response
                        $this->jsonResponse->setBody($json);

                        return FALSE;

                    }
                    else{
                        $result = APIStatusCode::getOkMsgArray();

                        $result['result'] = array('msg_login'=>0);

                        // 编码为 json
                        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                        // 处理 json_encode 错误
                        if ($json == false) {
                            $error = json_last_error_msg();
                            $json = APIStatusCode::getErrorMsgJson(APIStatusCode::JSON_ENCODE_ERROR, $error);
                        }

                        // 设置 response
                        $this->jsonResponse->setBody($json);

                        return FALSE;
                    }
                }
                // 处理必要参数为空
                else{
                    $json = APIStatusCode::getErrorMsgJson(APIStatusCode::NULL_PARAMS);
                    // 设置 response
                    $this->jsonResponse->setBody($json);

                    return FALSE;
                }

            }
            //处理tag错误
            else{
                $json = APIStatusCode::getErrorMsgJson(APIStatusCode::Tag_ERROR);
                // 设置 response
                $this->jsonResponse->setBody($json);

                return FALSE;
            }
        }
        // 处理必要参数为空
        else {

            $json = APIStatusCode::getErrorMsgJson(APIStatusCode::NULL_PARAMS);
            // 设置 response
            $this->jsonResponse->setBody($json);

            return FALSE;
        }



    }

    /**
     * @desc Action Alter，管理员账户修改密码
     *
     * @return FLASE
     */
    public function alterAction() {
        $tag = $this->getRequest()->getParam('tag');
        if ($tag !== null ) {
            // 请求公钥
            if ($tag == 1) {
                $result = APIStatusCode::getOkMsgArray();

                $result['result'] = array('pubkey'=>RSA_public);

                // 编码为 json
                $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                // 处理 json_encode 错误
                if ($json == false) {
                    $error = json_last_error_msg();
                    $json = APIStatusCode::getErrorMsgJson(APIStatusCode::JSON_ENCODE_ERROR, $error);
                }

                // 设置 response
                $this->jsonResponse->setBody($json);

                return FALSE;
            }
            // 返回修改结果
            elseif ($tag == 2) {
                if ($_POST['name'] !== null && $_POST['pw_old'] !== null && $_POST['pw_new'] !== null) {
                    $accountName = $_POST['name'];
                    $pw_old = $_POST['pw_old'];
                    $pw_new = $_POST['pw_new'];

                    //私钥解密password
                    $decodePw_old = RSAUtils::privDecrypt($pw_old);
                    $decodePw_new = RSAUtils::privDecrypt($pw_new);

                    //md5加密密码
                    $pwMD5_old = md5($decodePw_old);
                    $pwMD5_new = md5($decodePw_new);

                    //从数据库取出密码
                    $pwMD5FromDB = Dao_AdministratorModel::getInstance()->queryOne(['administrator_name'=>$accountName])['password'];

                    if($pwMD5_old == $pwMD5FromDB){
                        $result = APIStatusCode::getOkMsgArray();

                        $result['result'] = array('msg_login'=>1);
                        //修改密码
                        Dao_AdministratorModel::getInstance()->update(['administrator_name'=>$accountName],['password'=>$pwMD5_new]);

                        // 编码为 json
                        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                        // 处理 json_encode 错误
                        if ($json == false) {
                            $error = json_last_error_msg();
                            $json = APIStatusCode::getErrorMsgJson(APIStatusCode::JSON_ENCODE_ERROR, $error);
                        }

                        // 设置 response
                        $this->jsonResponse->setBody($json);

                        return FALSE;

                    }
                    else{
                        $result = APIStatusCode::getOkMsgArray();

                        $result['result'] = array('msg_login'=>0);

                        // 编码为 json
                        $json = json_encode($result, JSON_UNESCAPED_UNICODE);
                        // 处理 json_encode 错误
                        if ($json == false) {
                            $error = json_last_error_msg();
                            $json = APIStatusCode::getErrorMsgJson(APIStatusCode::JSON_ENCODE_ERROR, $error);
                        }

                        // 设置 response
                        $this->jsonResponse->setBody($json);

                        return FALSE;
                    }

                }
                // 处理必要参数为空
                else{
                    $json = APIStatusCode::getErrorMsgJson(APIStatusCode::NULL_PARAMS);
                    // 设置 response
                    $this->jsonResponse->setBody($json);

                    return FALSE;
                }


            }
            //处理tag错误
            else{
                $json = APIStatusCode::getErrorMsgJson(APIStatusCode::Tag_ERROR);
                // 设置 response
                $this->jsonResponse->setBody($json);

                return FALSE;
            }

        }
        // 处理必要参数为空
        else {

            $json = APIStatusCode::getErrorMsgJson(APIStatusCode::NULL_PARAMS);
            // 设置 response
            $this->jsonResponse->setBody($json);

            return FALSE;
        }




    }



}