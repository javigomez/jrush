<?php

Namespace Controller ;

class JUser extends Base {

    public function execute($pageVars) {
      $isHelp = parent::checkForHelp($pageVars) ;
      if ( is_array($isHelp) ) {
        return $isHelp; }
      $action = $pageVars["route"]["action"];
      $extraParams = $pageVars["route"]["extraParams"];

      if ($action=="delete") {
        $jUserModel = new \Model\JUser\Delete($extraParams);
        $this->content["jUserInfoResult"] = $jUserModel->askWhetherToDeleteUser();
        return array ("type"=>"view", "view"=>"jUserDelete", "pageVars"=>$this->content); }

      if ($action=="info") {
        $jUserModel = new \Model\JUser\Info($extraParams);
        $this->content["jUserInfoResult"] = $jUserModel->askWhetherToGetUserInfo();
        return array ("type"=>"view", "view"=>"jUserInfo", "pageVars"=>$this->content); }

      if ($action=="password") {
        $jUserModel = new \Model\JUser\Password($extraParams);
        $this->content["jUserInfoResult"] = $jUserModel->askWhetherToUpdateUserPassword();
        return array ("type"=>"view", "view"=>"jUserPassword", "pageVars"=>$this->content); }

      else {
            $this->content["genErrors"]="No Action"; }

      $this->content["messages"][] = "Invalid Action";
      return array ("type"=>"view", "view"=>"index", "pageVars"=>$this->content);

    }
    
}