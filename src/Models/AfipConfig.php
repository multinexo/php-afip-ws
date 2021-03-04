<?php
/**
 * Copyright (C) 1997-2020 Reyesoft <info@reyesoft.com>.
 *
 * This file is part of php-afip-ws. php-afip-ws can not be copied and/or
 * distributed without the express permission of Reyesoft
 */

declare(strict_types=1);

namespace Multinexo\Models;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;

class AfipConfig
{
    /** @var array  */
    private $config_array = [];

    /** @var AdapterInterface  */
    private $adapter;

    /** @var Filesystem  */
    private $filesystem;

    /** @var bool */
    public $sandbox = false;

    /** @var string */
    private $cuit = '';

    /** @var string */
    private $xml_generated_directory;

    /** @var string */
    private $certificate_path;

    /** @var string */
    private $privatekey_path;

    public function __construct()
    {
        /** @var array $defConf */
        $defConf = include __DIR__ . '/../../config/config.php';
        $this->config_array = $defConf;
    }

    public function getAdapter():AdapterInterface {
        return $this->adapter;
    }
    public function setAdapter(AdapterInterface $adapter):void {
        $this->adapter = $adapter;
    }
    public function getFilesystem():Filesystem  {
        return $this->filesystem;
    }
    public function setFilesystem(Filesystem $filesystem):void {
        $this->filesystem = $filesystem;
    }
    public function getFolder():string {
        return '';
    }

    public function getSandbox():bool
    {
        return $this->sandbox;
    }

    public function setSandbox(bool $value = false): void
    {
        $this->sandbox = $value;
    }

    public function getCuit(): string
    {
        return $this->cuit;
    }

    public function getUrl(string $ws_service_name):string {
        return $this->config_array[$this->sandbox ? 'url' : 'url_production'][$ws_service_name];
    }

    public function setCuit(string $cuit): void
    {
        $this->cuit = $cuit;
    }
    public function getPathCertificate():string {
        return $this->getFolder().'certificate.crt';
    }
    public function getPathPrivateKey():string {
        return $this->getFolder().'privateKey';
    }

    public function setXmlFolder(string $xml_directory): void
    {
        $this->xml_generated_directory = $xml_directory;
    }

    public function setCertificateFilename(string $certificate_path): void
    {
        $this->certificate_path = $certificate_path;
    }

    public function getPrivateKeyFilename(): string
    {
        return $this->privatekey_path ;
    }

    public function setPrivateKeyFilename(string $privatekey_path): void
    {
        $this->privatekey_path = $privatekey_path;
    }

    public function getPassPhrase():?string {
        return null;
    }
    public function getProxyPort():int {
        return 80;
    }

    public function getWsaaWsdlPath():string {
        return __DIR__ . '/../WSAA/wsaa.wsdl';
    }
}
