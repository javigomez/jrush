<?php

Namespace Controller ;

class Deploy extends Base {

    public function execute($pageVars) {

        $this->content["route"] = $pageVars["route"];
        $this->content["messages"] = $pageVars["messages"];
        $action = $pageVars["route"]["action"];

        if ($action=="cli") {

            $gitCheckoutModel = new \Model\GitCheckout();
            $this->content["checkoutResult"] = $gitCheckoutModel->checkoutProject();

            $projectModel = new \Model\Project();
            $this->content["projectInitResult"] = $projectModel->askWhetherToInitializeProject();
            $this->content["projectBuildResult"] = $projectModel->askWhetherToInstallBuildInProject();

            $hostEditorModel = new \Model\HostEditor();
            $this->content["hostEditorResult"] = $hostEditorModel->askWhetherToDoHostEntry();

            $VhostEditorModel = new \Model\VhostEditor();
            $this->content["VhostEditorResult"] = $VhostEditorModel->askWhetherToCreateVHost();

            $dbConfigureModel = new \Model\DBConfigure();
            $this->content["dbResetResult"] = $dbConfigureModel->askWhetherToResetDBConfiguration();
            $this->content["dbConfigureResult"] = $dbConfigureModel->askWhetherToConfigureDB();

            $dbInstallModel = new \Model\DBInstall();
            $this->content["dbInstallResult"] = $dbInstallModel->askWhetherToInstallDB($dbConfigureModel);

            $cukeConfModel = new \Model\CukeConf();
            $this->content["cukeCreateResult"] = $cukeConfModel->askWhetherToCreateCuke();
            $this->content["cukeResetResult"] = $cukeConfModel->askWhetherToResetCuke();

            return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content); }

        if ($action=="autopilot") {

            $autoPilotType= (isset($pageVars["route"]["extraParams"][0])) ? $pageVars["route"]["extraParams"][0] : null;

            if (isset($autoPilotType) && strlen($autoPilotType)>0 ) {

                $autoPilotFile = getcwd().'/'.escapeshellcmd($autoPilotType);
                $autoPilot = $this->loadAutoPilot($autoPilotFile);

                if ( $autoPilot!==null ) {

                    // git checkout
                    $gitCheckoutModel = new \Model\GitCheckout();
                    $this->content["gitCheckoutResult"] = $gitCheckoutModel->runAutoPilotCloner($autoPilot);
                    if ($autoPilot->gitCheckoutExecute && $this->content["gitCheckoutResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot Checkout/Clone Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    $this->content["gitDeletorResult"] = $gitCheckoutModel->runAutoPilotDeletor($autoPilot);
                    if ($autoPilot->gitDeletorExecute && $this->content["gitDeletorResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto PilotDeltor Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    // project
                    $projectModel = new \Model\Project();
                    $this->content["projectInitResult"] = $projectModel->runAutoPilotInit($autoPilot);
                    if ($autoPilot->projectInitializeExecute && $this->content["projectInitResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot Project Initialize Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    $this->content["projectBuildResult"] = $projectModel->runAutoPilotBuildInstall($autoPilot);
                    if ($autoPilot->projectBuildInstallExecute && $this->content["projectBuildResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot Build Install Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    // host editor
                    $hostEditorModel = new \Model\HostEditor();
                    $this->content["hostEditorAdditionResult"] = $hostEditorModel->runAutoPilotHostAddition($autoPilot);
                    if ($autoPilot->hostEditorAdditionExecute && $this->content["hostEditorAdditionResult"] != "1") {
                        $this->content["autoPilotErrors"]="Host file editor creation Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    $this->content["hostEditorDeletionResult"] = $hostEditorModel->runAutoPilotHostDeletion($autoPilot);
                    if ($autoPilot->hostEditorDeletionExecute && $this->content["hostEditorDeletionResult"] != "1") {
                        $this->content["autoPilotErrors"]="Host file editor deletion Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    // V Host Editor
                    $VHostEditorModel = new \Model\VHostEditor();
                    $this->content["virtualHostCreatorResult"] = $VHostEditorModel->runAutoPilotVHostCreation($autoPilot);
                    if ($autoPilot->virtualHostEditorAdditionExecute && $this->content["virtualHostCreatorResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot Virtual Host Creator Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    $this->content["virtualHostDeletionResult"] = $VHostEditorModel->runAutoPilotVHostDeletion($autoPilot);
                    if ($autoPilot->virtualHostEditorDeletionExecute && $this->content["virtualHostDeletionResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot Virtual Host Deletor Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }


                    // DB Configure
                    $dbConfigureModel = new \Model\DBConfigure() ;
                    $this->content["dbResetResult"] = $dbConfigureModel->runAutoPilotDBReset($autoPilot);
                    if ($autoPilot->dbResetExecute && $this->content["dbResetResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot DB Reset Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    $dbConfigureModel = new \Model\DBConfigure() ;
                    $this->content["dbConfigureResult"] = $dbConfigureModel->runAutoPilotDBConfiguration($autoPilot);
                    if ($autoPilot->dbConfigureExecute && $this->content["dbConfigureResult"] != "1" ) {
                        $this->content["autoPilotErrors"]="Auto Pilot DB Configure Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    // DB Install
                    $dbInstallModel = new \Model\DBInstall();
                    $this->content["dbInstallResult"] = $dbInstallModel->runAutoPilotDBInstallation($autoPilot);
                    if ($autoPilot->dbInstallExecute && $this->content["dbInstallResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot DB Install Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }
                    $this->content["dbDropResult"] = $dbInstallModel->runAutoPilotDBRemoval($autoPilot);
                    if ($autoPilot->dbDropExecute && $this->content["dbDropResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot DB Reset Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }

                    // Cuke Conf
                    $cukeConfModel = new \Model\CukeConf();
                    $this->content["cukeConfAdditionResult"] = $cukeConfModel->runAutoPilotAddition($autoPilot);
                    if ($autoPilot->cukeConfAdditionExecute && $this->content["cukeConfAdditionResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot Cuke Conf Creator Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  }
                    $this->content["cukeConfDeletionResult"] = $cukeConfModel->runAutoPilotDeletion($autoPilot);
                    if ($autoPilot->cukeConfDeletionExecute && $this->content["cukeConfResetResult"] != "1") {
                        $this->content["autoPilotErrors"]="Auto Pilot Cuke Conf Reset Broken";
                        return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content);  } }

                else {
                        $this->content["autoPilotErrors"]="Auto Pilot not defined"; }  }

            else {
                $this->content["autoPilotErrors"]="Auto Pilot not defined"; }

            return array ("type"=>"view", "view"=>"install", "pageVars"=>$this->content); }

    }

    private function loadAutoPilot($autoPilotFile){
        if (file_exists($autoPilotFile)) {
            include_once($autoPilotFile); }
        $autoPilot = (class_exists('\Core\AutoPilot')) ? new \Core\AutoPilot() : null ;
        return $autoPilot;
    }

}