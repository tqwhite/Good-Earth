<?php
namespace Heliport;
/**
 *
 */

/**
 *
IHR190
test to see if Helix is up and running
checks view state of one view.
awake of not...



Retrieve items
rgc 100	open process id

rgc 120	View record count delim stop char  e.g. 5 9 13
rgc 141	View fieldnames
rgc 140	retrieve the data

rgc 130	close process




Store items
rgc 100	open process id

rgc 120	View record count delim stop char  e.g. 5 9 13
rgc 141	View fieldnames
rgc 130	close process


SSC110	enter the data  //after the close because an SC110 is its own process


 */
/*
 * @db_todo Implement Helix Errors interface with class constants and methods for error handling
 * @db_note This could possibly be something that every DataBright class could have access to for their error handling
 */
class ServerInterface
{
    const LEASE_POOL_USER_FAILED = 'A free pool user could not be obtained or locked';
    const USER_POOL_CONFIG_ERROR_DISPATCHER = 'The user pool requires a username and password that can lease and release pool user records';
    /**
     * hpConnection
     * @public resource
     */
    public $fileSocket;
    /**
     * hpConnection
     * @public string
     */
    public $ipAddress;
    /**
     * hpConnection
     * @public integer
     */
    public $port;
    /**
     * hxHelix
     * @public string
     */
    public $collection;
    public $ihr190Relation;
    public $ihr190View;
    private $lastihr190Stat = false;
    public $processId;
    public $batchCount;
    public $commandTerminator;
    public $commandDelimiter;
    public $helixVersion;
    public $encodeDecodeData = true;
    /**
     *
     * @var User
     */
    public $adminUser;
    /**
     *
     * @var User
     */
    public $overrideUser;
    /**
     * hxCommon
     * @public string
     */
    public $relationName;
    public $viewName;
    public $fieldNames;
    public $viewData;
    public $outerData;
    public $numRecs;
    public $recordDelimiter;
    public $fieldDelimiter;
    public $errorStack;
    public $userErrorStack;
    public $messageStack;
    public $aeeaErrors;
    public $debug;
    /**
     *
     * @var User
     */
    private $leasee = NULL;
    /**
     *
     * @var stdClass
     */
    private $userPoolConfig = NULL;
    /*
     * @db_note Verify all of the "pool" properties are no longer used and remove
     */
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolDispatchRelation = '';
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolDispatchView = '';
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolReleaseRelation = '';
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolReleaseView = '';
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolDispatchUser = '';
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolDispatchPwd = '';
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolUser = '';
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolPassword = '';
    /**
     *
     * @var string
     * @deprecated since V2
     * @see userPoolConfig
     */
    public $poolRetryTimes = 3;

	private $displayFieldsReport=false;
private $className;

public function __construct(){


		$this->className=get_class($this);

		$helixConfiguration=\Zend_Registry::get('helixConfiguration');


		$ipAddress=$helixConfiguration['hostIp'];
		$port=$helixConfiguration['port'];
		$collection_name=$helixConfiguration['collection'];
		$adminUser=$helixConfiguration['user'];
		$adminPassword=$helixConfiguration['password'];
		$ihr190Relation=$helixConfiguration['test_rel'];
		$ihr190View=$helixConfiguration['test_view'];
		$userPool=$helixConfiguration['userpool'];

/*debug*/ //  error_log("ENTERING ServerInterface ===== ");

// \Q\Utils::dumpWeb(array(
// 	'ipAddress'=>$ipAddress,
// 	'port'=>$port,
// 	'collection_name'=>$collection_name,
// 	'adminUser'=>$adminUser,
// 	'adminPassword'=>$adminPassword,
// 	'ihr190Relation'=>$ihr190Relation,
// 	'ihr190View'=>$ihr190View,
// 'userPool'=>$userPool)
// );

        $func_name = 'helixUserConnection';
        $this->fileSocket = NULL;
        $this->ipAddress = $ipAddress;
        $this->port = $port;
        $this->collection_name = $collection_name;
        $this->adminUser = new User($adminUser, $adminPassword);

        $this->ihr190Relation = $ihr190Relation;
        $this->ihr190View = $ihr190View;
        $this->commandTerminator = chr(13) . chr(10) . '.' . chr(13) .
          chr(10);
        $this->commandDelimiter = chr(9);
        $this->set_aeea();
        $this->errorStack = array('error_count' => 0, 'socket' => array(),
            'heliport' => array(), 'ae' => array());
        $this->userErrorStack = array('error_count' => 0, 'errors' => array());
        $this->batchCount = 10;

        $this->userPoolConfig = $userPool;

        //$this->userPoolConfig-

        $this->set_msg($func_name . ' :: heliPortComm object created');

        $this->connect_to_socket();
    }

    /**
     *
     * @return boolean
     * @throws User_Exception When dispatcher username and/or password are empty
     */
protected function setOverrideUserToDispatcher()
    {
        if (!is_null($this->userPoolConfig)) {
            if (!empty($this->userPoolConfig['dispatcher']['username']) && !empty($this->userPoolConfig['dispatcher']['password'])) {
                return ($this->overrideUser = new User(
                    $this->userPoolConfig['dispatcher']['username'],
                    $this->userPoolConfig['dispatcher']['password']
                ));
            } else {
                throw new User_Exception(DataBright_Helix_HeliPort::USER_POOL_CONFIG_ERROR_DISPATCHER);
            }
        }

        return false;
    }

    /**
     *
     * @return DataBright_Helix_HeliPort
     */
protected function clearOverrideUser()
    {
        $this->overrideUser = NULL;

        return $this;
    }

    /**
     *
     * @return User
     */
private function selectLoginUser()
    {
/*debug*/ //  "<hr/>entering selectLoginUser<br>";
/*debug*/ //  error_log("<hr/>entering selectLoginUser<br>");
    	$outUser='';

        if (!is_null($this->overrideUser)) {
/*debug*/ //  "using overrideUser<br>";
/*debug*/ //  error_log("using overrideUser<br>");
            $outUser=$this->overrideUser;
        } else if (!is_null($this->leasee)) {
/*debug*/ //  "using leasee<br>";
/*debug*/ //  error_log("using leasee<br>");
            $outUser=$this->leasee;
        } else {
/*debug*/ //  "using adminUser<br>";
/*debug*/ //  error_log("using adminUser<br>");
            $outUser=$this->adminUser;
        }
/*debug*/ //\Q\Utils::dumpWeb($outUser, 'hc.selectLoginUser.outUser'); //tqdebug
/*debug*/ //  "exiting selectLoginUser<br>";
/*debug*/ //  error_log("exiting selectLoginUser<br>");
        return $outUser;
    }

    /**
     *
     * @param array $config
     * @return DataBright_Helix_HeliPort Fluent interface
     */
public function setPoolOptions(array $config)
    {
        foreach ($config as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                call_user_method($method, $this);
            }
        }
        return $this;
    }

    /**
     * Attempt to obtain and secure a free pool user
     * @throws DataBright_Helix_HeliPort_UserPool_Exception When a pool user cannot be obtained
     */
public function leasePoolUser()
    {
/*debug*/ //  "<hr/>helix.leasePoolUser-entering<br>";
/*debug*/ //  error_log("<hr/>helix.leasePoolUser-entering<br>");
        $user_info = array();

        if ($this->lastihr190Stat) {
            if ($this->setOverrideUserToDispatcher()) {
                $try_count = 0;

                $user_info = $this->retrieve(
                    $this->userPoolConfig['dispatch']['relation'],
                    $this->userPoolConfig['dispatch']['view'],
                    false,
                    $this->overrideUser->getUsername(),
                    $this->overrideUser->getPassword()
                );
                if (isset($user_info ['data'] [0])) {
                    $this->clearOverrideUser();
/*
                    $this->leasee = new User(
                        $user_info ['data'] [0] ['username'],
                        $user_info ['data'] [0] ['password']
                    );
*/
                    $this->leasee = new User(
                        $user_info ['data'] [0] ['user'],
                        $user_info ['data'] [0] ['pwd']
                    );
/*debug*/ //\Q\Utils::dumpWeb($user_info, 'helix.heliport.user_info');
/*debug*/ //\Q\Utils::dumpWeb($this->leasee, 'helix.heliport-this->leasee');
/*debug*/ //  "helix.leasePoolUser #1<br>";
/*debug*/ //  error_log("helix.leasePoolUser #1<br>");
                    return true;
                } else {
                    $this->clearOverrideUser();

/*debug*/ //  "helix.leasePoolUser #2<br>";
/*debug*/ //  error_log("helix.leasePoolUser #2<br>");
                    throw new DataBright_Helix_HeliPort_UserPool_Exception(DataBright_Helix_HeliPort::LEASE_POOL_USER_FAILED);
                }
            }
        } else {
/*debug*/ //  "helix.leasePoolUser #3<br>";
/*debug*/ //  error_log("helix.leasePoolUser #3<br>");
            return false;
        }
/*debug*/ //  "helix.leasePoolUser #4<br>";
/*debug*/ //  error_log("helix.leasePoolUser #4<br>");
    }

    /**
     *
     * @return boolean True if the
     */
public function releasePoolUser()
    {
/*debug*/ //  "<hr/>entering hc.releasePoolUser()<br>";
/*debug*/ //  error_log("<hr/>entering hc.releasePoolUser()<br>");
        if (!$this->lastihr190Stat) {
            return FALSE;
        }

        if (NULL === $this->leasee) {
            return TRUE;
        }

        try {
            if (!$this->setOverrideUserToDispatcher()) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        $result = $this->store(
            $this->userPoolConfig['release']['relation'],
            $this->userPoolConfig['release']['view'],
            array(
                'i user' => $this->leasee->getUserName()
            ),
            $this->overrideUser->getUsername(),
            $this->overrideUser->getPassword()
        );

        if ($result) {
            $this->leasee = NULL;
        }

/*debug*/ //  "exit hc.releasePoolUser.result=$result<br>";
/*debug*/ //  error_log("exit hc.releasePoolUser.result=$result<br>");
        return $result;
    }

public function hasPoolUserLeased()
    {
        return (NULL !== $this->leasee);
    }

public function retrieve($rel, $view, $include_outer_data = false)
    {

/*debug*/ //  "<div style=color:red;font-size:14pt;margin-top:20px;>entering hc.retrieve</div>";
/*debug*/ //  error_log("<div style=color:red;font-size:14pt;margin-top:20px;>entering hc.retrieve</div>");
        $func_name = 'retrieve';

        $this->relationName = $rel;
        $this->viewName = $view;

        $retVal = false;
        $varrReturn = array(
            'data' => array(),
            'viewouterdata' => array(),
            'recordcount' => 0,
            'fieldnames' => array()
        );

        $initHelixResult=$this->initHelix();

        if ($initHelixResult) {
            if ($this->initView()) {

                if ($include_outer_data) {
                    $this->getContextData(); //rgc142
                }

                $fieldNameResult=$this->getFieldNames();

                if ($fieldNameResult) {

              		$getRecordResult=$this->getRecords(); //==========================

                    if ($getRecordResult) {
                        $varrReturn['f_sep'] = $this->fieldDelimiter;
                        $varrReturn['r_sep'] = $this->recordDelimiter;

                        if (sizeof($this->viewData) > 0) {
                            $varrReturn['data'] = $this->viewData;
                        }

                        if (sizeof($this->fieldNames) > 0) {
                            $varrReturn['fieldnames'] = $this->fieldNames;
                        }

                        $varrReturn['recordcount'] = sizeof($this->viewData);
                        $retVal = true;
                        $this->cleanOutput();
                    }
                }

            }
        }
        $this->terminateHelixProcess();
        if ($retVal) {
/*debug*/ //\Q\Utils::dumpWeb($varrReturn, 'hc.retrieve.varrReturn');
/*debug*/ //  "hc.retrieve.retVal=$retVal<br>";
/*debug*/ //  error_log("hc.retrieve.retVal=$retVal<br>");
/*debug*/ //  "exiting hc.retrieve<br";
/*debug*/ //  error_log("exiting hc.retrieve<br");
            return $varrReturn;
        } else {
/*debug*/ //  "exiting hc.retrieve (using else clause)<br";
/*debug*/ //  error_log("exiting hc.retrieve (using else clause)<br");
            return $retVal;
        }
    }

private function sequenceFieldsToHelix($inData, $fieldArray){
	$remainingFieldNameList=$fieldArray;
	$remainingDataElementList=$inData;
	$resultingFieldList=array();

	$outArray=array();
	foreach ($fieldArray as $label=>$fieldName){
		if (isset($inData[$fieldName])){
			$outArray[$fieldName]=$inData[$fieldName];
			$resultingFieldList[$fieldName]=$fieldName;
			unset($remainingDataElementList[$fieldName]);
			unset($remainingFieldNameList[$label]);
		}
	}

	if ($this->displayFieldsReport){
		\Zend_Debug::dump($remainingFieldNameList, 'helix names not fulfilled');
		\Zend_Debug::dump($remainingDataElementList, 'sql data not used');
		echo "<div style='border-bottom:1pt solid gray;width:300px;height:5px;'></div>";
		\Zend_Debug::dump($fieldArray, 'helix expected field sequence');
		echo "<div style='border-bottom:1pt solid gray;width:300px;height:5px;'></div>";
		\Zend_Debug::dump($resultingFieldList, 'actual outbound field sequence');
		\Zend_Debug::dump($outArray, 'actual outbound data');
		echo "<div style='background:red;height:10px;width:100%;'></div>";
	}
	return $outArray;
}

public function storeAndDisplayFieldsReport($rel, $view, $data, $displayFieldsReport=false){
	$this->displayFieldsReport=$displayFieldsReport;
	return $this->store($rel, $view, $data);
}

public function store($rel, $view, $data)
    {
/*debug*/ //  "<hr/>entering hc.store<br>";
/*debug*/ //  error_log("<hr/>entering hc.store<br>");
/*debug*/ //\Q\Utils::dumpWeb($rel, "rel");
/*debug*/ //\Q\Utils::dumpWeb($view, "view");
/*debug*/ //\Q\Utils::dumpWeb($data, "data");
        $func_name = 'store';
        $this->relationName = $rel;
        $this->viewName = $view;
        $do_store = false;

        if ($this->initHelix()) {
            if ($this->initView()) {
                $do_store = $this->getFieldNames(); //rgc141()
/*debug*/ //\Q\Utils::dumpWeb($do_store, 'hc.store.getFieldNames');
            }
        }

        $this->terminateHelixProcess();

        $store_done = true;
        if ($do_store) {
			if ($this->displayFieldsReport){
				echo "tableName/view=$view<br>";
			}

			$data=$this->sequenceFieldsToHelix($data, $this->fieldNames);
            $store_done = $this->saveSingleRecord($data);

        if ($this->displayFieldsReport){
				\Zend_Debug::dump($data, "actual SENT data-tableName/view=$view");
			}
\Zend_Debug::dump($data, 'actual SENT data-tableName/view=$view');
        }
/*debug*/ //  "exit hc.store=$store_done<br>";
/*debug*/ //  error_log("exit hc.store=$store_done<br>");
        return $store_done;
    }

public function __destruct()
    {
        $this->destruct();
    }

private function destruct()
    {
        $this->discon();
        $this->close_socket();
    }

private function connect_to_socket()
    {
        $func_name = 'connect_to_socket';
        $retVal = true;

@        $this->fileSocket = stream_socket_client(
            ('tcp://' . $this->ipAddress . ':' . $this->port), $errno, $errstr, 10);
        if (!$this->fileSocket) {
            $this->set_error('socket',
              ($func_name . '-' . $errno . ': ' . $errstr . ' ' . $this->ipAddress .
              ':' . $this->port));
            $retVal = false;
        } else {
            $this->set_msg($func_name . ' :: connection established');
        }
        return $retVal;
    }

private function close_socket()
    {
        $func_name = 'close_socket';
        if ($this->fileSocket) {
            fclose($this->fileSocket);
        }
        $this->fileSocket = NULL;
        $this->set_msg($func_name . ' :: socket closed');
    }

private function read_socket()
    {
        $func_name = 'read_socket';
        $buff = array();
        while (!feof($this->fileSocket)) {
            $out = fread($this->fileSocket, 8096);
            $buff[] = $out;
            if ($this->commandTerminator ==
              substr($out, - (strlen($this->commandTerminator)),
                strlen($this->commandTerminator))) {
                break;
            }
        }
        $output = str_replace($this->commandTerminator, '',
            implode('', $buff));
        return $output;
    }

    /*
      Create the process
     */
private function initHelix(){
		return $this->rgc100();
	}

private function rgc100()
    {

/*debug*/ //  "<hr/>entering initHelix<br>";
/*debug*/ //  error_log("<hr/>entering initHelix<br>");
        $func_name = 'rgc100';
        $retVal = true;

        if (!$this->fileSocket) {

            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);

            $retVal = false;

        } else {
            $user = $this->selectLoginUser();
            if (is_null($user)) {
                $this->set_error('retrieve',
                  ($func_name . ' - No authentication provided'),
                  $this->relationName,
                  $this->viewName);
                $retVal = false;
            } else {

                $cmdParams = array(
                    $this->collection_name,
                    $user->getUserName(),
                    $user->getPassword(),
                    $this->relationName,
                    $this->viewName
                );
/*debug*/ //\Q\Utils::dumpWeb($cmdParams,  'hc.initHelix.cmdParams');
                $rgc100 = 'RGC100' .
                	$this->commandDelimiter .
                	base64_encode(implode($this->commandDelimiter, $cmdParams)) .
                	$this->commandTerminator;

                fwrite($this->fileSocket, $rgc100, strlen($rgc100));


/*debug*/ //  "<div style=background:black;color:white;>hc.initHelixhc (100). says: wrote to fileSocket, RGC100 said=".htmlentities(implode($this->commandDelimiter, $cmdParams))." (but was base64 encoded)</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.initHelixhc (100). says: wrote to fileSocket, RGC100 said=".htmlentities(implode($this->commandDelimiter, $cmdParams))." (but was base64 encoded)</div>");

                $output = $this->read_socket();


/*debug*/ //  "<div style=background:black;color:white;>hc.initHelixhc (100). says: read from fileSocket: $output</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.initHelixhc (100). says: read from fileSocket: $output</div>");

                if (strpos($output, 'ERR RGC100') === 0) {
                    ereg('ERR RGC100' . $this->commandDelimiter . '([0-9]*)',
                      $output, $regs);
                    $error = $this->handleHError($regs[1],
                        $user->getUserName(),
                        $this->relationName,
                        $this->viewName);
                    $errorFormat = '%s - %s: %s';
                    $formattedError = sprintf($errorFormat, $func_name, $regs[1], $error);
                    $this->set_error('retrieve', $formattedError);
                    $retVal = false;
                } else {
                    $this->set_msg($func_name . ' :: retrieve process opened');
                    $this->processId = str_replace(('OK RGC100' . $this->commandDelimiter), '', $output);

                    $retVal = true;
                }
            }
        }
/*debug*/ //  "exiting hc.initHelix=$retval<br>";
/*debug*/ //  error_log("exiting hc.initHelix=$retval<br>");
        return $retVal;
    }

    /*
      Retrieve the summary data
     */
	private function initView(){
		return $this->rgc120();
	}

private function rgc120()
    {
/*debug*/ //  "<hr/>entering hc.initView (120)<br>";
/*debug*/ //  error_log("<hr/>entering hc.initView (120)<br>");
        $func_name = 'rgc120';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
            $rgc120 = 'RGC120' . $this->commandDelimiter .
              $this->commandTerminator;
            fwrite($this->fileSocket, $rgc120, strlen($rgc120));
/*debug*/ //  "<div style=background:black;color:white;>hc.initView says: wrote to fileSocket, said=$rgc120</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.initView says: wrote to fileSocket, said=$rgc120</div>");
            $output = $this->read_socket();
            if (substr($output, 0, 10) == 'ERR RGC120') {
                ereg('ERR RGC120' . $this->commandDelimiter . '([0-9]*)',
                  $output, $regs);
                $this->set_error('retrieve',
                  ($func_name . '-' . $regs[1] . ': ' .
                  (isset($this->aeeaErrors[$regs[1]]) ? $this->aeeaErrors[$regs[1]] : 'Unknown')));
                $retVal = false;
            } else {
                $output = str_replace($this->commandTerminator, '', $output);
                $tmp = explode($this->commandDelimiter, $output);
                $this->numRecs = $tmp[1];
                $this->fieldDelimiter = $tmp[2];
                $this->recordDelimiter = $tmp[3];
                $this->set_msg($func_name . ' :: summary info retrieved');
                $retVal = true;
            }
        }
    $tmp=array(
    'hc.initView.numRecs'=>$this->numRecs,
    'hc.initView.fieldDelimiter'=>$this->fieldDelimiter,
    'hc.initView.recordDelimiter'=>$this->recordDelimiter
    );
/*debug*/ //\Q\Utils::dumpWeb($tmp, 'hc.initView.receivedVars');
/*debug*/ //  "hc.initView.retVal=$retVal<br>";
/*debug*/ //  error_log("hc.initView.retVal=$retVal<br>");
/*debug*/ //  "exiting hc.initView (120)<br>";
/*debug*/ //  error_log("exiting hc.initView (120)<br>");
        return $retVal;
    }

    /*
      Terminate the process
     */
	private function terminateHelixProcess(){
		return $this->rgc130();
	}

private function rgc130()
    {
/*debug*/ //  "<hr/>entering hc.terminateHelixProcess (130)<br>";
/*debug*/ //  error_log("<hr/>entering hc.terminateHelixProcess (130)<br>");
        $func_name = 'rgc130';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
            $rgc130 = 'RGC130' . $this->commandTerminator;
            fwrite($this->fileSocket, $rgc130, strlen($rgc130));
/*debug*/ //  "<div style=background:black;color:white;>hc.terminateHelixProcess says: wrote to fileSocket, said=$rgc130</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.terminateHelixProcess says: wrote to fileSocket, said=$rgc130</div>");

            $output = $this->read_socket();
/*debug*/ //  "<div style=background:black;color:white;>hc.terminateHelixProcess says: read from fileSocket, output=$output</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.terminateHelixProcess says: read from fileSocket, output=$output</div>");
            if (substr($output, 0, 10) == 'ERR RGC130') {
                ereg('ERR RGC130' . $this->commandDelimiter . '([0-9]*)',
                  $output, $regs);
                $this->set_error('retrieve',
                  ($func_name . '-' . $regs[1] . ': ' .
                  (isset($this->aeeaErrors[$regs[1]]) ? $this->aeeaErrors[$regs[1]] : 'Unknown')));
                $retVal = false;
            } else {
                $this->set_msg($func_name . ' :: retrieve process terminated');
                $retVal = true;
            }
        }

/*debug*/ //  "exit hc.terminateHelixProcess (130)result=$retVal<br>";
/*debug*/ //  error_log("exit hc.terminateHelixProcess (130)result=$retVal<br>");
        return $retVal;
    }

    /*
      Retrieve the data
     */
	private function getRecords(){
		return $this->rgc140();
	}

private function rgc140()
    {
/*debug*/ //  "<hr/>entering hc.getRecords(140)<br>";
/*debug*/ //  error_log("<hr/>entering hc.getRecords(140)<br>");
        $func_name = 'rgc140';
        $retVal = true;
        if (!$this->fileSocket) {

            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);

            $retVal = false;
        } else {
            $tmpNumRecs = $this->numRecs;
            $start = 0;

/*debug*/ //  "<div style=background:black;color:white;>hc.getRecords (140) says: tmpNumRecs=$tmpNumRecs</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.getRecords (140) says: tmpNumRecs=$tmpNumRecs</div>");
            while ($tmpNumRecs > 0) {
                if ($tmpNumRecs > $this->batchCount) {
                    $rgc140 = 'RGC140 ' . $start . ' ' . $this->batchCount .
                      $this->commandTerminator;
                    $tmpNumRecs -= $this->batchCount;
                    $start += $this->batchCount;
                } else {
                    $rgc140 = 'RGC140 ' . $start . ' ' . ($tmpNumRecs) .
                      $this->commandTerminator;
                    $tmpNumRecs = 0;
                }
                fwrite($this->fileSocket, $rgc140, strlen($rgc140));


/*debug*/ //  "<div style=background:black;color:white;>hc.getRecords (140) says: wrote to fileSocket, said=$rgc140</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.getRecords (140) says: wrote to fileSocket, said=$rgc140</div>");

                $output = $this->read_socket();


/*debug*/ //  "<div style=background:black;color:white;>hc.getRecords(140) says: read from fileSocket: $output</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.getRecords(140) says: read from fileSocket: $output</div>");
                if (substr($output, 0, 10) == 'ERR RGC140') {
                    ereg('ERR RGC140' . $this->commandDelimiter . '([0-9]*)',
                      $output, $regs);
                    $this->set_error('retrieve',
                      ($func_name . '-' . $regs[1] . ': ' .
                      (isset($this->aeeaErrors[$regs[1]]) ? $this->aeeaErrors[$regs[1]] : 'Unknown')));
                    $tmpNumRecs = 0;
                    $retVal = false;
                } else {
					$output = str_replace($this->commandTerminator, '', $output);
                    $varrOut = explode($this->commandDelimiter, $output);

                    $varrRecs = explode(chr($this->recordDelimiter), base64_decode(trim($varrOut[1])));

                    foreach ($varrRecs as $rec) {
                        if ($rec == '') {
                            continue;
                        }
                        $tmp = explode(chr($this->fieldDelimiter), $rec);

                        $this->viewData[] = array();
                        foreach ($this->fieldNames as $key => $val) {
                            if ($this->recordDelimiter != 13) {
                                str_replace(chr(13), chr(10), $tmp[$key]);
                            }
                            if ($this->encodeDecodeData) {
                                $this->viewData[sizeof($this->viewData) - 1][$this->convert2UTF8(
                                    $val)] = $this->convert2UTF8($tmp[$key]);
                            } else {
                                $this->viewData[sizeof($this->viewData) - 1][$val] = $tmp[$key];
                            }
                        }
                    }
                    $this->set_msg(
                      $func_name . ' :: data retrieved (' .
                      sizeof($this->viewData) . ' records)');
                    $retVal = true;
                }
            }
        }
/*debug*/ //\Q\Utils::dumpWeb($this->viewData, "hc.getRecords.this->viewData");
/*debug*/ //  "exiting hc.getRecords(140)=$retVal<br>";
/*debug*/ //  error_log("exiting hc.getRecords(140)=$retVal<br>");
        return $retVal;
    }

    /*
      Retrieve field header
     */
	private function getFieldNames(){
		return $this->rgc141();
	}

private function rgc141()
    {
        $func_name = 'rgc141';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
            $rgc141 = 'RGC141' . $this->commandTerminator;
            fwrite($this->fileSocket, $rgc141, strlen($rgc141));


/*debug*/ //  "<div style=background:black;color:white;>hc.getFieldNames (141) says: wrote to fileSocket, said=$rgc141</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.getFieldNames (141) says: wrote to fileSocket, said=$rgc141</div>");

            $output = $this->read_socket();


/*debug*/ //  "<div style=background:black;color:white;>hc.getFieldNames (141) says: read from fileSocket: $output</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.getFieldNames (141) says: read from fileSocket: $output</div>");


            if (substr($output, 0, 10) == 'ERR RGC141') {
                ereg('ERR RGC141' . $this->commandDelimiter . '([0-9]*)', $output, $regs);
                $this->set_error('retrieve',
                  ($func_name . '-' . $regs[1] . ': ' .
                  (isset($this->aeeaErrors[$regs[1]]) ? $this->aeeaErrors[$regs[1]] : 'Unknown')));
                $retVal = false;
            } else {
                $output = str_replace($this->commandTerminator, '', $output);
                $varrOut = explode($this->commandDelimiter, $output);
                $this->fieldNames = explode(chr($this->fieldDelimiter),
                    trim(base64_decode($varrOut[1])));
                foreach ($this->fieldNames as $key => $val) {
                    if (!is_string($val)) {
                        Throw new Exception(sprintf('DB :: Value is not a string: %s', gettype($val)));
                    }
                    if ($this->encodeDecodeData) {
                        $this->fieldNames[$key] = $this->convert2UTF8($val);
                    } else {
                        $this->fieldNames[$key] = $val;
                    }
                }
                array_unshift($this->fieldNames, 'helix id');
                $this->set_msg($func_name . ' :: field headers retrieved');
                $retVal = true;
            }
        }
/*debug*/ //  "<div style='background:#ff6699;'>";
/*debug*/ //\Q\Utils::dumpWeb($this->fieldNames, "hc.getFieldNames.this->fieldNames"); echo "</div>";
        return $retVal;
    }

    /*
      Retrieve data outside the repeat rectangle
     */
	private function getContextData(){
		return $this->rgc142();
	}

private function rgc142()
    {
        $func_name = 'rgc142';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
            $rgc141 = 'RGC142' . $this->commandTerminator;
            fwrite($this->fileSocket, $rgc141, strlen($rgc141));
            $output = $this->read_socket();
            if (substr($output, 0, 10) == 'ERR RGC142') {
                ereg('ERR RGC142' . $this->commandDelimiter . '([0-9]*)',
                  $output, $regs);
                $this->set_error('retrieve',
                  ($func_name . '-' . $regs[1] . ': ' .
                  (isset($this->aeeaErrors[$regs[1]]) ? $this->aeeaErrors[$regs[1]] : 'Unknown')));
                $retVal = false;
            } else {
                $output = str_replace($this->commandTerminator, '', $output);
                $varrOut = explode($this->commandDelimiter, $output);
                $this->outerData = explode(chr($this->fieldDelimiter),
                    trim(base64_decode($varrOut[1])));
                foreach ($this->outerData as $key => $val) {
                    if ($this->encodeDecodeData) {
                        $this->outerData[$key] = $this->convert2UTF8($val);
                    } else {
                        $this->outerData[$key] = $val;
                    }
                }
                $this->set_msg($func_name . ' :: outer data retrieved');
            }
        }
        return $retVal;
    }

    /*
      Enter a single record

      When storing a missing value do not use NULL, use a non value that matches the Helix field, i.e. empty string or zero
     */
	private function saveSingleRecord($data){
		$this->ssc110($data);
	}

private function ssc110($data)
    {
/*debug*/ //  "<hr/>entering hc.saveSingleRecord(rgc110)<br>";
/*debug*/ //  error_log("<hr/>entering hc.saveSingleRecord(rgc110)<br>");
/*debug*/ //\Q\Utils::dumpWeb($data, 'hc.saveSingleRecord.inData');
        $func_name = 'ssc110';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
/*debug*/ //  "hc.saveSingleRecord(rgc110)-before selectLoginUser<br>";
/*debug*/ //  error_log("hc.saveSingleRecord(rgc110)-before selectLoginUser<br>");
            $user = $this->selectLoginUser();
/*debug*/ //  "hc.saveSingleRecord(rgc110)-after selectLoginUser<br>";
/*debug*/ //  error_log("hc.saveSingleRecord(rgc110)-after selectLoginUser<br>");
            if ($user->getUserName() == '') {
                $this->set_error('retrieve',
                  ($func_name . ' - No authentication provided'),
                  $this->relationName, $this->viewName);
                $retVal = false;
            } else {
                $values = array();
                $field_sep = chr($this->fieldDelimiter);
                $recordDelimiter = chr($this->recordDelimiter);
                $fields = $this->fieldNames;
                array_shift($fields);

                foreach ($fields as $field) {
                    if (isset($data[$field])) {
                        if ($this->encodeDecodeData) {
                            $values[] = $this->convert2MACROMAN($data[$field]);
                        } else {
                            $values[] = $data[$field];
                        }
                    }
                }

                $ssc110 = 'SSC110' . $this->commandDelimiter .
                  base64_encode(
                    $this->collection_name . $this->commandDelimiter .
                    $user->getUserName() . $this->commandDelimiter .
                    $user->getPassword() . $this->commandDelimiter .
                    $this->relationName . $this->commandDelimiter .
                    $this->viewName . chr(13) /* TLS: hardcode to carriage-return. (dont use the record delimiter which can change!) $recordDelimiter */ .
                    implode($field_sep, $values)) . $this->commandTerminator;

				$tmp=$this->collection_name . $this->commandDelimiter .
                    $user->getUserName() . $this->commandDelimiter .
                    $user->getPassword() . $this->commandDelimiter .
                    $this->relationName . $this->commandDelimiter .
                    $this->viewName . chr(13) /* TLS: hardcode to carriage-return. (dont use the record delimiter which can change!) $recordDelimiter */ .
                    implode($field_sep, $values);

/*debug*/ //  "<div style=background:black;color:white;>hc.saveSingleRecord (110) says: wrote to fileSocket, said=".urlencode($tmp)."</div>";
/*debug*/ //  error_log("<div style=background:black;color:white;>hc.saveSingleRecord (110) says: wrote to fileSocket, said=".urlencode($tmp)."</div>");
/*debug*/ //\Q\Utils::dumpWeb($values, 'file socket wrote this data');
                fwrite($this->fileSocket, $ssc110, strlen($ssc110));
                $output = $this->read_socket();
                if (substr($output, 0, 10) == 'ERR SSC110') {
                    preg_match(
                      '/ERR SSC110' . $this->commandDelimiter . '([0-9]*)/',
                      $output, $regs);
                    $retVal = false;
                } else {
                    $this->set_msg($func_name . ' :: single record stored');
                }
            }
        }
/*debug*/ //  "exiting hc.saveSingleRecord(rgc110)=$retVal<br>";
/*debug*/ //  error_log("exiting hc.saveSingleRecord(rgc110)=$retVal<br>");
        return $retVal;
    }

    /*
      Prepare a batch entry
     */

public function ssc111($record_count)
    {
        // 		$func_name = 'ssc111';
        //
        // 		$retVal = true;
        //
        // 		$rgc100 = 'SSC111' . $this->commandDelimiter . base64_encode ($this->collection_name . $this->commandDelimiter . (!empty ($user) ? $user : $this->web_pool_dispatch_user) . $this->commandDelimiter . (!empty ($user) ? $pwd : $this->web_pool_dispatch_pwd) . $this->commandDelimiter . $this->relationName . $this->commandDelimiter . $this->viewName . $this->commandDelimiter . $this->commandDelimiter . $this->recordDelimiter . $this->commandDelimiter . $record_count) . $this->commandTerminator;
        //
        // 		socket_write ($this->fileSocket, $rgc100, strlen ($rgc100));
        //
        // 		$output = $this->read_socket ();
        //
        // 		if (substr ($output, 0, 10) == 'ERR SSC111') {
        // 			ereg ('ERR SSC111' . $this->commandDelimiter . '([0-9]*)', $output, $regs);
        //
        // 			$this->set_error ('store batch', ($func_name . '-' . $regs [1] . ': ' . $this->handleHError ($regs [1], (!empty ($user) ? $user : $this->web_pool_dispatch_user), $this->relationName, $this->viewName)));
        //
        // 			$retVal = false;
        // 		}
        // 		else {
        // 			$this->set_msg ($func_name . ' :: batch entry prepared');
        //
        // 			$this->processId = $varrOut [1];
        // 			$retVal = true;
        // 		}
        //
        // 		return $retVal;
    }

    /*
      Sends batch records to be added to the _collection
     */

public function ssc112($data)
    {
        // 		$func_name = 'ssc112';
        //
        // 		$retVal = true;
        //
        // 		$tmpNumRecs = sizeof ($data);
        //
        // 		$start = 0;
        // 		while ($tmpNumRecs > 0) {
        // 			if ($tmpNumRecs > $this->batchCount) {
        // 				$recs = array ();
        // 				for ($i = 0 ; $i < 10 ; $i++) {
        // 					$recs [] = $data [0];
        // 					array_shift ($data);
        // 					$tmpNumRecs -= $this->batchCount;
        // 				}
        // 				$ssc112 = 'SSC112' . $this->commandDelimiter . base64_encode (implode ($this->) . $this->commandTerminator;
        // 			}
        // 			else {
        // 				$ssc112 = 'SSC112 ' . $start . ' ' . ($tmpNumRecs) . $this->commandTerminator;
        // 				$tmpNumRecs = 0;
        // 			}
        //
        // 			socket_write ($this->fileSocket, $ssc112, strlen ($ssc112));
        //
        // 			$output = $this->read_socket ();
        //
        // 			if (substr ($output, 0, 10) == 'ERR SSC112') {
        // 				ereg ('ERR SSC112' . $this->commandDelimiter . '([0-9]*)', $output, $regs);
        //
        // 				$ae_error = str_replace (('ERR SSC112' . $this->commandDelimiter), '', str_replace (('ERR SSC112' . $this->commandDelimiter), '', $output));
        //
        // 				$this->set_error ('store batch', ($func_name . '-' . $regs [1] . ': ' . (isset ($this->aeeaErrors [$regs [1]]) ? $this->aeeaErrors [$regs [1]] : 'Unknown')));
        //
        // 				$retVal = false;
        // 			}
        // 			else {
        // 				$output = str_replace ($this->commandTerminator, '', $output);
        //
        // 				$varrOut = explode ($this->commandDelimiter, $output);
        //
        // 				$varrRecs = explode (chr ($this->recordDelimiter), trim (base64_decode (trim ($varrOut [1]))));
        //
        // 				foreach ($varrRecs as $rec) {
        // 					$tmp = explode (chr ($this->fieldDelimiter), $rec);
        //
        // 					$this->viewData [] = array ();
        //
        // 					foreach ($this->fieldNames as $key => $val) {
        // 						$this->viewData [sizeof ($this->viewData) - 1] [$val] = $tmp [$key];
        // 					}
        // 				}
        // 				$this->set_msg ($func_name . ' :: data retrieved (' . sizeof ($this->viewData) . ' records)');
        //
        // 				$retVal = true;
        // 			}
        // 		}
        //
        // 		return $retVal;
    }

    /*
      Delete a record or records by Helix Record ID
     */

public function del150()
    {
        $func_name = 'del150';
    }

    /*
      Retrieves the version of Helix
     */

public function getver()
    {
        $func_name = 'getver';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
            $getver = 'GETVER' . $this->commandTerminator;
            fwrite($this->fileSocket, $getver, strlen($getver));
            $output = $this->read_socket();
            if (substr($output, 0, 10) == 'ERR GETVER') {
                ereg('ERR GETVER' . $this->commandDelimiter . '([0-9]*)',
                  $output, $regs);
                $this->set_error('util',
                  ($func_name . '-' . $regs[1] . ': ' .
                  (isset($this->aeeaErrors[$regs[1]]) ? $this->aeeaErrors[$regs[1]] : 'Unknown')));
                $retVal = false;
            } else {
                $output = str_replace($this->commandTerminator, '', $output);
                $varrOut = explode($this->commandDelimiter, $output);
                $this->helixVersion = $varrOut[1];
                $this->set_msg(
                  $func_name . ' :: retrieved helix version (' .
                  $this->helixVersion . ')');
                $retVal = true;
            }
        }
        return $retVal;
    }

    /*

     */

public function platfo()
    {
        $func_name = 'platfo';
        return $func_name;
    }

    /*
      Kill the open appleevent process
     */

public function kilpid()
    {
        $func_name = 'kilpid';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
            $kilpid = 'KILPID' . $this->commandTerminator;
            fwrite($this->fileSocket, $kilpid, strlen($kilpid));
            $this->set_msg($func_name . ' :: open AE process killed');
        }
        return $retVal;
    }

    /*
      Triggers a disconnect
     */

public function discon()
    {
        $func_name = 'discon';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
            $discon = 'DISCON' . $this->commandTerminator;
            fwrite($this->fileSocket, $discon, strlen($discon));
            $this->set_msg($func_name . ' :: disconnected from helix');
        }
        return $retVal;
    }

    /*
      I think this is used to change the user for a connection, but i don't know.
     */

public function loguse()
    {
        $func_name = 'loguse';
        return $func_name;
    }

    /*
      Retrieves a list of active connection usernames
     */

public function lisuse()
    {
        $func_name = 'lisuse';
        return $func_name;
    }

    /*
      Set to HeliPort and from HeliPort encoding
     */

public function encode()
    {
        $func_name = 'encode';
        $retVal = true;
        if (!$this->fileSocket) {
            $this->set_error('retrieve',
              ($func_name . ' - No valid socket available'), $this->relationName,
              $this->viewName);
            $retVal = false;
        } else {
            $encode = 'ENCODE' . $this->commandDelimiter .
              base64_encode('ISO-8859-1') . $this->commandTerminator;
            fwrite($this->fileSocket, $encode, strlen($encode));
            $out = socket_read($this->fileSocket, 2048);
            $this->set_msg($func_name . ' :: connection encoding set');
        }
        return $retVal;
    }

public function ihr190()
    {
/*debug*/ //  "ihr190<br/>";
/*debug*/ //  error_log("ihr190<br/>");
        $func_name = 'ihr190';

        $retVal = true;

        if (!$this->fileSocket) {

/*debug*/ //  "NO FILE SOCKET<br>";
/*debug*/ //  error_log("NO FILE SOCKET<br>");
$this->set_error(
	'retrieve',
  ($func_name . ' - No valid socket available'),
  $this->ihr190Relation, 
  $this->ihr190View
  );
            $retVal = false;
        } else {
/*debug*/ //  "FOUND FILE SOCKET<br>";
/*debug*/ //  error_log("FOUND FILE SOCKET<br>");
            $ihr190CmdParams = array(
                $this->collection_name,
                $this->adminUser->getUserName(),
                $this->adminUser->getPassword(),
                $this->ihr190Relation,
                $this->ihr190View
            );

            $encodedIhr190Cmd = base64_encode(
                implode(
                  $this->commandDelimiter,
                  $ihr190CmdParams
                )
            );

            $ihr190 = 'IHR190' . $this->commandDelimiter . $encodedIhr190Cmd . $this->commandTerminator;

            $status=fwrite($this->fileSocket, $ihr190, strlen($ihr190));

/*debug*/ //  "190 FWRITE STATUS=$status<br>";
/*debug*/ //  error_log("190 FWRITE STATUS=$status<br>");

            $output = $this->read_socket();

/*debug*/ //  "190 READ RESULT=$output<br>";
/*debug*/ //  error_log("190 READ RESULT=$output<br>");

            if ($this->isDebug('msg')) {
                $tmp = $output;
                $tmp = str_replace(('OK IHR190 ' . $this->commandDelimiter), '', $output);
            }
            if (substr($output, 0, 10) == 'ERR IHR190') {
                $this->set_error('util',
                  ($func_name . '-_collection (' . $this->collection_name . ') not loaded')
                );
                $retVal = false;
            } else {
                if (!preg_match('/^OK IHR190/', $output)) {
                    $this->set_error('util', ($func_name . '-' . $this->aeeaErrors[(str_replace(('OK IHR190 ' . $this->commandDelimiter), '', $output))]['desc'] . ' (' . $this->collection_name . '-' . $this->ihr190Relation . ')'));
                    $retVal = false;
                } else {
                    $this->set_msg($func_name . ' :: _collection: ' . $this->collection_name . ' is loaded');
                    $retVal = true;
                }
            }
        }

        $this->lastihr190Stat = $retVal;

        return $retVal;
    }

public function handleHError($errNum,
                                 $user = '')
    {
        $errString = '';
        if (isset($this->aeeaErrors[$errNum])) {
            $show2user = $this->aeeaErrors[$errNum];
            $errString .= $this->aeeaErrors[$errNum]['desc'] .
              (sizeof($this->aeeaErrors[$errNum]['show']) > 0 ? chr(40) : '');
            $tmpShows = array();
            foreach ($this->aeeaErrors[$errNum]['show'] as $shower) {
                if (isset($$shower)) {
                    $tmpShows[] = $shower . ': ' . $$shower;
                } else
                if (isset($this->$shower)) {
                    $tmpShows[] = $shower . ': ' . $this->$shower;
                } else {
                    $tmpShows[] = 'Unknown: ' . $shower;
                }
            }
            $errString .= ( implode(', ', $tmpShows) . ')');
            if ($this->aeeaErrors[$errNum]['allow_user_error']) {
                $this->set_user_error(
                  $this->aeeaErrors[$errNum]['user_error_id'], $errString);
            }
        } else {
            $errString = 'Unknown';
        }
        return $errString;
    }

public function cleanOutput()
    {
        $func_name = 'cleanOutput';
        $this->fieldNames = array();
        $this->viewData = array();
        $this->outerData = array();
        $this->processId = NULL;
        $this->numRecs = 0;
        $this->recordDelimiter = NULL;
        $this->fieldDelimiter = NULL;
    }

public function set_error($type,
                              $err_string)
    {
        $this->errorStack[$type][] = $err_string;
        $this->errorStack['error_count']++;
        if ($this->isDebug('err')) {
            print($err_string . "\n");
        }
    }

public function clear_errors()
    {
        $this->errorStack = array('error_count' => 0, 'socket' => array(),
            'heliport' => array(), 'ae' => array());
    }

public function return_errors($type = '',
                                  $lineSep = "\n",
                                  $spaceSep = '&nbsp;')
    {
        $out = '';
        if (empty($type)) {
            $sep = ($lineSep . str_repeat($spaceSep, 4));
            foreach ($this->errorStack as $type => $errors) {
                if (is_array($errors)) {
                    if (sizeof($errors) > 0) {
                        $out .= $type . $sep . implode($sep, $errors);
                    }
                }
            }
        } else {
            if (sizeof($this->errorStack[$type]) > 0) {
                $out = implode($lineSep, $this->errorStack[$type]);
            }
        }
        return $out;
    }

public function set_user_error($id,
                                   $err_string)
    {
        $this->userErrorStack['errors'][$id] = $err_string;
        $this->userErrorStack['error_count']++;
        if ($this->isDebug('err')) {
            print($err_string . "\n");
        }
    }

public function clear_user_errors()
    {
        $this->errorStack = array('error_count' => 0, 'errors' => array());
    }

public function return_user_error($id)
    {
        $out = '';
        if (isset($this->userErrorStack['errors'][$id])) {
            $out = $this->userErrorStack['errors'][$id];
        }
        return $out;
    }

public function set_msg($msg_string)
    {
        $this->messageStack[] = $msg_string;
        if ($this->isDebug('msg')) {
            print($msg_string . "\n");
        }
    }

public function clear_msgs()
    {
        $this->messageStack = array();
    }

public function return_msgs($clear = false)
    {
        if (sizeof($this->messageStack) > 0) {
            $out = implode("\n", $this->messageStack);
            if ($clear) {
                $this->clear_msgs();
            }
        } else {
            $out = '';
        }
    }

public function convert2UTF8($string)
    {
        return iconv('MACINTOSH', 'UTF-8', $string);
    }

public function convert2MACROMAN($string)
    {
        return iconv('UTF-8', 'MACINTOSH', $string);
    }

public function set_aeea()
    {
        $this->aeeaErrors = array(
            '10' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array(),
                'desc' => 'Incorrect number of items in the Descriptor List'),
            '20' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array(),
                'desc' => 'No _collection opened with named Helix application'),
            '30' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('coll'), 'desc' => 'Incorrect _collection name'),
            '40' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('coll'), 'desc' => '_collection not in User mode'),
            '50' => array('allow_user_error' => true,
                'user_error_id' => 'invalid_login', 'show' => array('user'),
                'desc' => 'Illegal User Name'),
            '60' => array('allow_user_error' => true,
                'user_error_id' => 'invalid_login', 'show' => array('user'),
                'desc' => 'Invalid Password for the target User'),
            '70' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel'), 'desc' => 'No such relation exists'),
            '80' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'), 'desc' => 'No such form exists'),
            '90' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'),
                'desc' => 'No such form exists with the User'),
            '100' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view', 'user'),
                'desc' => 'String length in List > 255'),
            '110' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array(), 'desc' => 'Invalid Transaction Code'),
            '120' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'), 'desc' => 'Form is not compiled'),
            '130' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'), 'desc' => 'Form not in Show Form mode'),
            '140' => array('allow_user_error' => true,
                'user_error_id' => 'invalid_form_state',
                'show' => array('rel', 'view'),
                'desc' => 'Form already opened (in Helix)'),
            '150' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array(), 'desc' => 'AEDone. Illegal Process ID'),
            '160' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array(), 'desc' => 'AECancel. Illegal Process ID'),
            '170' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'), 'desc' => 'Only LongInt is accepted'),
            '180' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'), 'desc' => 'End-of-List encountered'),
            '190' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'),
                'desc' => 'Illegal number of rows requested between 1 & 10'),
            '200' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'), 'desc' => 'AEStore. Not an Entry Form'),
            '210' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'),
                'desc' => 'AEStore. No valid Field in Entry Form'),
            '220' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'),
                'desc' => 'AEStore. Problem with input data'),
            '230' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array(), 'desc' => 'AERetrieve. Process not finished yet'),
            '240' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array(), 'desc' => 'AEDelete. Illegal Record ID'),
            '250' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array(), 'desc' => 'AEDelete. Locking problem'),
            '260' => array('allow_user_error' => true,
                'user_error_id' => 'max_users_exceeded',
                'show' => array('rel', 'view'),
                'desc' => 'Maximum number of AE users exceeded. Please check that you don\'t have Helix Client open.'),
            '1000' => array('allow_user_error' => false, 'user_error_id' => '',
                'show' => array('rel', 'view'),
                'desc' => 'Data do not meet validation criteria for the form'));
    }

public function isDebug($level = 'debug')
    {
        return false !== strpos($this->debug, $level);
    }
    
public function clearAllPoolUsers(){


/*debug*/ //  "clearAllPoolUsers<br/>";
/*debug*/ //  error_log("clearAllPoolUsers<br/>");
        $func_name = 'clearAllPoolUsers';

        $retVal = true;

        if (!$this->fileSocket) {

/*debug*/ //  "NO FILE SOCKET<br>";
/*debug*/ //  error_log("NO FILE SOCKET<br>");
			$this->set_error(
				'retrieve',
				($func_name . ' - No valid socket available'),
				$this->ihr190Relation, 
				$this->ihr190View
			);
			$retVal = false;
        } else {
/*debug*/ //  "FOUND FILE SOCKET<br>";
/*debug*/ //  error_log("FOUND FILE SOCKET<br>");
            $cmdParams = array(
                $this->collection_name,
                $this->adminUser->getUserName(),
                $this->adminUser->getPassword(),
                "  user pool global",
                "Release All Pool Users"
            );

            $encodedIhr190Cmd = base64_encode(
                implode(
                  $this->commandDelimiter,
                  $cmdParams
                )
            );

            $ihr190 = 'IHR190' . $this->commandDelimiter . $encodedIhr190Cmd . $this->commandTerminator;

            $status=fwrite($this->fileSocket, $ihr190, strlen($ihr190));

/*debug*/ //  "190 FWRITE STATUS=$status<br>";
/*debug*/ //  error_log("190 FWRITE STATUS=$status<br>");

            $output = $this->read_socket();

/*debug*/ //  "190 READ RESULT=$output<br>";
/*debug*/ //  error_log("190 READ RESULT=$output<br>");

            if ($this->isDebug('msg')) {
                $tmp = $output;
                $tmp = str_replace(('OK IHR190 ' . $this->commandDelimiter), '', $output);
            }
            if (substr($output, 0, 10) == 'ERR IHR190') {
                $this->set_error('util',
                  ($func_name . '-_collection (' . $this->collection_name . ') not loaded')
                );
                $retVal = false;
            } else {
                if (!preg_match('/^OK IHR190/', $output)) {
                    $this->set_error('util', ($func_name . '-' . $this->aeeaErrors[(str_replace(('OK IHR190 ' . $this->commandDelimiter), '', $output))]['desc'] . ' (' . $this->collection_name . '-' . $this->ihr190Relation . ')'));
                    $retVal = false;
                } else {
                    $this->set_msg($func_name . ' :: _collection: ' . $this->collection_name . ' is loaded');
                    $retVal = true;
                }
            }
        }

        $this->lastihr190Stat = $retVal;

        return $retVal;
    

}

}
