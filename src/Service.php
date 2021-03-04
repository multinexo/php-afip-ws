<?php


namespace Multinexo;


use Multinexo\Models\AfipConfig;

class Service extends \stdClass
{
    /** @var AfipConfig */
    public $config;

    /** @var string */
    public $ws;

    public function getXmlPath():string {
        return $this->config->getFolder(). 'TA-' . $this->config->getCuit() . '-' . $this->ws . '.xml';
    }
    }
