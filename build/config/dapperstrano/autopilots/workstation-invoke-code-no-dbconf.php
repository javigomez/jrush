<?php

/*************************************
*      Generated Autopilot file      *
*     ---------------------------    *
*Autopilot Generated By Dapperstrano *
*     ---------------------------    *
*************************************/

Namespace Core ;

class AutoPilotConfigured extends AutoPilot {

    public $steps ;
    public $swaps ;

    public function __construct() {
        $this->setSteps();
        $this->setSSHData();
    }

    /* Steps */
    private function setSteps() {

      $this->steps =
        array(
          array ( "InvokeSSH" => array(
            "sshInvokeSSHDataExecute" => true,
            "sshInvokeSSHDataData" => "",
            "sshInvokeServers" => array(
              array("target" => "1", "user" => "1", "pword" => "1", ),

            ),
          ) , ) ,
        );

    }


//
// This function will set the sshInvokeSSHDataData variable with the data that
// you need in it. Call this in your constructor
//
  private function setSSHData() {
    $timeDrop = time();
    $this->steps[0]["InvokeSSH"]["sshInvokeSSHDataData"] = <<<"SSHDATA"
cd /tmp/
git clone -b master --no-checkout --depth 1 https://github.com/phpengine/jrush.git dapper$timeDrop
cd dapper$timeDrop
git show HEAD:build/config/dapperstrano/autopilots/workstation-node-install-code-no-dbconf.php > /tmp/workstation-node-install-code-no-dbconf.php
rm -rf /tmp/dapper$timeDrop
cd /tmp/
sudo dapperstrano autopilot execute workstation-node-install-code-no-dbconf.php
sudo chown -R www-data /opt/jrush/jrush/current/src
sudo rm workstation-node-install-code-no-dbconf.php
SSHDATA;
  }

}
