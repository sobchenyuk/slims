<?php

namespace Modules\Core\Source\Libs\Logger;

class LoggerSystem
{
    protected $logger;
    protected $arMessTypeStr = [
        'info' => 'Info - ',
        'stat' => 'Statistic - ',
        'error' => 'Error - ',
    ];
    protected $levelInfo = ['info', 'statistic', 'error'];
    protected $statLevelInfo = [];

    function __construct($loggerSystem, array $arConfig = [])
    {
        $this->logger = $loggerSystem;
        foreach ($arConfig as $levelInfo) {
            if (in_array($levelInfo, $this->levelInfo)) {
                $this->statLevelInfo[] = $levelInfo;
            }
        }
        if (in_array('none', $arConfig))
            $this->statLevelInfo = [];
    }

    public function info($mess, $data = [])
    {
        if (in_array('info', $this->statLevelInfo))
            $this->logger->addInfo($this->arMessTypeStr['info'] . $mess, $data);
    }

    public function stat($mess, $data = [])
    {
        if (in_array('statistic', $this->statLevelInfo))
            $this->logger->addInfo($this->arMessTypeStr['stat'] . $mess, $data);
    }

    public function error($mess, $data = [])
    {
        if (in_array('error', $this->statLevelInfo))
            $this->logger->addWarning($this->arMessTypeStr['error'] . $mess, $data);
    }

    public function __call($name, $arguments)
    {
        switch(count($arguments)){
            case 0: $this->logger->$name(); break;
            case 1: $this->logger->$name($arguments[0]); break;
            case 2: $this->logger->$name($arguments[0], $arguments[1]); break;
            case 3: $this->logger->$name($arguments[0], $arguments[1], $arguments[2]); break;
        }
    }

    public function getLogger()
    {
        return $this->logger;
    }

}