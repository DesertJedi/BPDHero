{<?php
class UserController extends BaseController
{
    /**
     * "/user/list" Endpoint - Get list of users
     * "/user/add" Endpoint - Register single user
     */
    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();

        if (strtoupper($requestMethod) == 'GET') {
            try {
                $userModel = new UserModel();

                $intLimit = 10;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }

                $arrUsers = $userModel->getUsers($intLimit);
                $responseData = json_encode($arrUsers);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function addAction()
        {
            $strErrorDesc = '';
            $requestMethod = $_SERVER["REQUEST_METHOD"];
            parse_str($_SERVER['QUERY_STRING'], $arrQueryStringParams); // $this->getQueryStringParams() isn't working in this method so changed to this

            if (strtoupper($requestMethod) == 'GET') { //TODO: Change to post in the app

                try {
                    $userModel = new UserModel();

                    if (isset($arrQueryStringParams['username']) && $arrQueryStringParams['username']) {
                        $username = $arrQueryStringParams['username'];
                    }

                    if (isset($arrQueryStringParams['password']) && $arrQueryStringParams['password']) {
                        $password = $arrQueryStringParams['password'];
                    }

                    if (isset($arrQueryStringParams['email']) && $arrQueryStringParams['email']) {
                        $email = $arrQueryStringParams['email'];
                    }

                    if (isset($username) && isset($password) && isset($email)){
                        try{
                            if($userModel->addUser($username,$password,$email) == "userAdded"){
                                $responseData = json_encode("User added to db.");
                            } elseif($userModel->addUser($username,$password,$email) == "userAlreadyExists")  {
                                $responseData = json_encode("User already exists.");
                            } elseif($userModel->addUser($username,$password,$email) == "emailAlreadyExists")  {
                                 $responseData = json_encode("Email already exists.");
                             }
                              else {
                                $responseData = json_encode("Failed to add user due to error.");
                            }
                        } catch (Error $e){
                            $strErrorDesc = $e->getMessage().'Unable to add user'; //TODO: Make less specific in prod
                            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                        }
                    } else {
                        $responseData = json_encode("not set");
                    }
                } catch (Error $e) {
                    $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }
            } else {
                $strErrorDesc = 'Method not supported';
                $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
            }

            // send output
            if (!$strErrorDesc) {
                $this->sendOutput(
                    $responseData,
                    array('Content-Type: application/json', 'HTTP/1.1 200 OK')
                );
            } else {
                $this->sendOutput(json_encode(array('error' => $strErrorDesc)),
                    array('Content-Type: application/json', $strErrorHeader)
                );
            }
        }
}