<?php
/**
 * Core Controller Class
 */
class ControllerCore extends CommonCore
{
	use Security;

    public $lang = 'default';
	public $user = NULL;
	public $params = [];
	public $failure = [];
	public $cache = [];

	/**
	 * summary
	 */
	public function __construct(
		string $permissions = 'all',
        string $lang        = 'default',
		array  $getData     = [],
		array  $postData    = [],
		array  $paramsMeta  = [],
		array  $onFailure   = [],
		bool   $cache       = false,
		int    $cacheTTL    = 0
	)
	{
        $lang = $this->escapeInput($lang);
		$getData = array_map([$this, 'escapeInput'], $getData);
		$postData = array_map([$this, 'escapeInput'], $postData);

        $this->_setLang($lang);
		$this->_setFailureBehavior($onFailure);
		$this->_setUser($postData, $permissions);
		$this->_checkAccess($permissions);
        $this->_setCache($cache, $cacheTTL);
        $this->_setParams($paramsMeta, $getData, $postData);
    }

	public function redirect(string $address = '/', int $code = 302) : void
	{
		$address = strlen($address) > 0 ? $address : '/';
		header("Location: {$address}", true, $code);
		exit(0);
	}

	public function error(string $message = '', int $code = 200) : void
	{
		$failureType = $this->failure['type'];
		$failureValue = $this->failure['value'];

		if ($failureType == 'redirect') {

			if (isset($failureValue['address'])) {
				$address = (string) $failureValue['address'];
			} else {
				$address = '/';
			}

			if (isset($failureValue['code'])) {
				$code = (int) $failureValue['code'];
			} else {
				$code = 302;
			}

			$this->redirect($address, $code);

		} elseif ($failureType == 'message') {

			http_response_code($code);
            header('Content-Type: application/json');
			$this->returnJSON(false, [
				'message' => $message
			]);
            exit(0);
		} else {
			throw new Exception($message, $code);
		}
	}

	public function returnJSON(bool $status = true, array $data = []) : void
	{
		$status = $status ? 'success' : 'error';

        $res = [
			'status' => $status,
			'data' => $data
		];

        header('Content-Type: application/json');
		echo json_encode($res);

        exit(0);
	}

    private function _setLang(string $lang = 'default') : void
    {
        $this->lang = $lang;
    }

	private function _setFailureBehavior(array $onFailure = []) : void
	{
		if (isset($onFailure['type'])) {
			$this->failure['type'] = (string) $onFailure['type'];
		} else {
			$this->failure['type'] = 'exception';
		}

		if (isset($onFailure['value'])) {
			$this->failure['value'] = (array) $onFailure['value'];
		} else {
			$this->failure['value'] = [];
		}
	}

	private function _setUser(
        array $postData     = [],
        string $permissions = 'all'
    ) : bool
	{
        switch ($permissions) {
            case 'publc_key':
                $tokenName = 'publc_key';
                $isPaid = false;
                break;

            case 'private_key':
                $tokenName = 'private_key';
                $isPaid = false;
                break;

            case 'paid_publc_key':
                $tokenName = 'publc_key';
                $isPaid = true;
                break;

            case 'paid_private_key':
                $tokenName = 'private_key';
                $isPaid = true;
                break;

            default:
                return true;
                break;
        }

		$user = $this->initModel('User');

		$id = isset($postData['id']) ? (int) $postData['id'] : -1;

		$tokenValue = isset($postData[$tokenName])
            ? (string) $postData[$tokenName]
            : '';

		$this->user = $user->getCurrentUser(
            $id,
            $tokenName,
            $tokenValue,
            $isPaid
        );

        return true;
	}

	private function _checkAccess(string $permissions = 'all') : void
	{
		if ($permissions!='all' && !$this->_isAuth()) {
			$this->error('Authentication error');
		}
	}

	private function _isAuth() : bool
	{
		$userID = isset($this->user['id']) ? (int) $this->user['id'] : -1;
		return $userID > 0;
	}

    private function _setCache(
        bool $isEbabled = false,
        int $ttl = -1
    ) : void
    {
        $this->cache['isEnabled'] = $isEbabled;
        $this->cache['TTL'] = $ttl;
    }

    private function _prepareParamsMeta(array $paramsMeta = []) : array
    {
        foreach ($paramsMeta as $idx => $value) {
            
            if (isset($value['type'])) {
                $value['type'] = (string) $value['type'];
            } else {
                $value['type'] = 'get';
            }

            if (isset($value['required'])) {
                $value['required'] = (bool) $value['required'];
            } else {
                $value['required'] = false;
            }

            if (isset($value['prepare'])) {
                $value['prepare'] = (array) $value['prepare'];
            } else {
                $value['prepare'] = [];
            }

            if (isset($value['validation'])) {
                $value['validation'] = (array) $value['validation'];
            } else {
                $value['validation'] = [];
            }

            if (isset($value['validation']['type'])) {
                $type = (string) $value['validation']['type'];
                $value['validation']['type'] = $type;
            } else {
                $value['validation']['type'] ='string';
            }

            if (isset($value['rules'])) {
                $rules = (array) $value['validation']['rules'];
                $value['validation']['rules'] = $rules;
            } else {
                $value['validation']['rules'] = [];
            }

            $paramsMeta[$idx] = $value;
        }

        return $paramsMeta;
    }

    private function _setParams(
        array $paramsMeta = [],
        array $getData = [],
        array $postData = []
    ) : void
    {
        $paramsMeta = $this->_prepareParamsMeta($paramsMeta);

        foreach ($paramsMeta as $idx => $paramsMetaValue) {
            if($paramsMetaValue['type'] == 'get'){
                if (count($getData)>0) {
                    $this->params[$idx] = [];
                    $this->params[$idx]['value'] = array_shift($getData);
                    $this->params[$idx]['meta'] = $paramsMetaValue;
                } elseif($paramsMetaValue['require']) {
                    $this->error("Value \"{$idx}\" not set");
                }
            }
            if($paramsMetaValue['type'] == 'post'){
                if (isset($postData[$idx])) {
                    $this->params[$idx] = [];
                    $this->params[$idx]['value'] = $postData[$idx];
                    $this->params[$idx]['meta'] = $paramsMetaValue;
                } elseif(isset($paramsMetaValue['required'])) {
                    $this->error("Value \"{$idx}\" not set");
                }
            }
        }
        
        $this->_validateParams();
        $this->_prepareParams();
    }

    private function _validateParams() : void {
        foreach ($this->params as $idx => $param) {
            $param = $this->_validateParamType($param, $idx);
            $param = $this->_validateParamRules($param, $idx);
            $this->params[$idx] = $param;
        }
    }

    private function _validateParamType(
        array $param = [],
        string $paramName = ''
    ) : array
    {
        $res = true;
        $param['value'] = (string) $param['value']; 
        switch ($param['meta']['validation']['type']) {
            case 'string':
                $res = true;
                break;
            case 'email':
                $res = preg_match(
                    '/^(.*?)\@(.*?)\.(.*?)$/su',
                    $param['value']
                );
                $res = $res && !preg_match('/\s/su', $param['value']);
                break;
            case 'numeric':
                $res = preg_match('/^([0-9]+)$/su', $param['value']);
                break;
            case 'bool':
                $res = true || preg_match('/^(0)|(1)$/su', $param['value']);
                $res = $res || preg_match(
                    '/^(true)|(false)$/su',
                    $param['value']
                );
                break;
            default:
                $type = $param['meta']['validation']['type'];
                $this->error(
                    "Schema error: type \"{$type}\" is not supported"
                );
                break;
        }
        if (!$res) {
            $this->error("Value \"{$paramName}\" has bad type");
        }
        return $param;
    }

    private function _validateParamRules(
        array $param = [],
        string $paramName = ''
    ) : array
    {
        $regexRule = '';
        $res = true;

        foreach ($param['meta']['validation']['rules'] as $rule) {
            switch ($rule) {
                case 'only_digits':
                    $regexRule = "{$regexRule}0-9";
                    break;
                case 'only_letters':
                    $regexRule = "{$regexRule}a-zA-Zа-яёіґєїА-ЯЁІҐЄЇ";
                    break;
                case 'only_unsigned':
                    $res = $res && floatval($param['value']) > 0;
                    break;
                case 'not_zero':
                    $res = $res && intval($param['value']) != 0;
                    break;
                default:
                    $this->error("
                        Schema error: rule \"{$rule}\" is not supported"
                    );
                    break;
            }
        }

        if (strlen($regexRule)>0) {
            $res = $res && preg_match(
                '/^(['.$regexRule.']+)$/su',
                $param['value']
            );
        }

        if (!$res) {
            $this->error("Value \"{$paramName}\" has bad type");
        }

        return $param;
    }

    private function _prepareParams()
    {
        foreach ($this->params as $idx => $param) {
            foreach ($param['meta']['prepare'] as $prepareAction) {
                switch ($prepareAction) {
                    case 'type_string':
                        $param['value'] = (string) $param['value'];
                        break;
                    case 'type_float':
                        $param['value'] = (float) $param['value'];
                        break;
                    case 'type_int':
                        $param['value'] = (int) $param['value'];
                        break;
                    case 'type_bool':
                        $param['value'] = (bool) $param['value'];
                        break;
                    case 'case_lower':
                        $param['value'] = (string) mb_convert_case(
                            $param['value'],
                            MB_CASE_LOWER
                        );
                        break;
                    case 'case_upper':
                        $param['value'] = (string) mb_convert_case(
                            $param['value'],
                            MB_CASE_UPPER
                        );
                        break;
                    case 'case_title':
                        $param['value'] = (string) mb_convert_case(
                            $param['value'],
                            MB_CASE_TITLE
                        );
                        break;
                    case 'trim':
                        $param['value'] = trim($param['value']);
                        break;
                    default:
                        $this->error(
                            "Schema error: prepare action \"".
                            $prepareAction.
                            "\" is not supported"
                        );
                        break;
                }
            }
            $this->params[$idx] = $param['value'];
        }
    }
}
?>