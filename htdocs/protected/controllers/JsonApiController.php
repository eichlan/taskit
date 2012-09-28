<?php

class JsonApiController extends Controller
{
	public function actionIndex()
	{
		$data = json_decode(file_get_contents('php://input'), true);

		$resp = array(
			'result'=>null,
			'error'=>null,
			'id'=>$data['id']
		);

		if( !is_array($data) || !isset($data['method']) ||
			!isset($data['params']) || !isset($data['id']) ||
			!is_string($data['method']) || !is_array($data['params']) ||
			!is_int($data['id']) )
		{
			$resp['error'] = 'Invalid json-rpc format';
		}
		else
		{
			$fnc = 'apicall' . ucfirst( $data['method'] );
			if( method_exists( $this, $fnc ) )
			{
				$paramCount = count($data['params']);
				$method = new ReflectionMethod($this, $fnc);
				if( $paramCount > $method->getNumberOfParameters() )
				{
					$resp['error'] = 'Too many parameters.';
				}
				else if( $paramCount < $method->getNumberOfRequiredParameters() )
				{
					$resp['error'] = 'Too few parameters.';
				}
				else
				{
					try
					{
						$resp['result'] = call_user_func_array(
							array($this, $fnc), $data['params']
						);
					}
					catch( Exception $e )
					{
						$resp['error'] = $e->getMessage();
					}
				}
			}
			else
			{
				$resp['error'] = 'No such method.';
			}
		}
		echo json_encode( $resp );
	}

	public function apicallHelp( $method )
	{
		switch( $method )
		{
		case 'help': return 'Returns a help string for a given method.';
		case 'listMethods': return 'Lists all available json-rpc methods.  Not all methods listed may be usable in every situation.  You can optionally specify that listMethods should not provide parameter information.';
		}
		return 'no help yet';
//		throw new Exception('No such method.');
	}

	public function apicallCount()
	{
		if( !isset(Yii::app()->session['count']) )
			Yii::app()->session['count'] = 0;

		Yii::app()->session['count'] += 1;

		return Yii::app()->session['count'];
	}

	public function apicallListMethods( $withParams=true )
	{
		$ret = array();
		$class = new ReflectionClass( $this );
		foreach( $class->getMethods() as $method )
		{
			if( substr($method->getName(), 0, 7) == 'apicall' )
			{
				$fnc = lcfirst( substr($method->getName(), 7) );
				if( $withParams )
				{
					$prm = array();
					foreach( $method->getParameters() as $param )
					{
						$ptxt = $param->getName();
						if( $param->isDefaultValueAvailable() )
							$ptxt .= '=' . json_encode($param->getDefaultValue());
						$prm[] = $ptxt;
					}
					$fnc .= '(' . implode(',', $prm) . ')';
				}
				$ret[] = $fnc;
			}
		}
		return $ret;
	}
}
