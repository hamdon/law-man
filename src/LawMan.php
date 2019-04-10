<?php
namespace Hamdon\LawMan;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Session\SessionManager;
use Illuminate\Config\Repository;

class LawMan 
{
   /**
     * @var SessionManager
     */
    protected $session;
    /**
     * @var Repository
     */
    protected $config;

    /**
     * Packagetest constructor.
     * @param SessionManager $session
     * @param Repository $config
     */
    public function __construct(SessionManager $session, Repository $config)
    {
        $this->session = $session;
        $this->config = $config;
    }
	
	private function getSign($timestamp)
    {
        return md5(trim($this->config->get('lawman.appId')) . trim($this->config->get('lawman.secret')) . trim($this->config->get('lawman.token')) . $timestamp);
    }

    public function setFrontEndSubmit()
    {
        $this->submitUrl = '/ocean/front_end_rill';
        return $this;
    }

    public function setBackEndSubmit()
    {
        $this->submitUrl = '/ocean/back_end_rill';
        return $this;
    }

    public function setBackEndExecutionTime()
    {
        $this->submitUrl = '/ocean/execution_time_rill';
        return $this;
    }

    public function setSQLSubmit()
    {
        $this->submitUrl = '/ocean/sql_rill';
        return $this;
    }

    public function submitException(\Exception $exception)
    {
        if($exception instanceof NotFoundHttpException){
            return false;
        }

        $files = $exception->getTrace();
        $file = $exception->getFile();
        if(isset($files[0]['file'])){
            $file = $files[0]['file'].':'.$files[0]['line'];
        }
        $source['file'] = $file;
        $source['line'] = $exception->getLine();
        $source['message'] = $exception->getMessage();
        $source['trace'] = $exception->getTraceAsString();
        $source['ip'] = request()->getClientIp();
        $source['location_href'] =  request()->url();
        $source['method'] =  request()->method();
        $params = request()->all();
        $source['params'] =  empty($params)?'':json_encode($params,JSON_UNESCAPED_UNICODE);
        $this->submitData($params);
    }

    public function submitData($data)
    {
        $data['appId'] = trim($this->config->get('lawman.appId'));
        $data['timestamp'] = time();
        $data['sign'] = $this->getSign($data['timestamp']);
        $param = json_encode($data);
        $url = trim($this->config->get('lawman.host'), '/') . $this->submitUrl;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Content-Length:' . strlen($param)));
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return '';
        }
        curl_close($ch);
    }
}