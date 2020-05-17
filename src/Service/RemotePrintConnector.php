<?php

namespace Dochne\Shopping\Service;

use Mike42\Escpos\PrintConnectors\PrintConnector;

/**
 * This should only be needed when debugging
 *
 * @package Dochne\Shopping\Service
 */
class RemotePrintConnector implements PrintConnector
{
    protected $sent = false;

    protected $stream = "";

    public function __destruct()
    {
        if (!$this->sent) {
            throw new \Exception("Data was never sent. Did you call finalise?");
        }
    }

    public function finalize()
    {
        $encoded = base64_encode($this->stream);
        $command = 'ssh auri.local "echo "' .$encoded . '" | base64 -d | sudo tee /dev/usb/lp0 > /dev/null"';
        shell_exec($command);
        $this->sent = true;
    }

    public function read($len)
    {
        throw new \Exception("Read is not supported");
    }

    public function write($data)
    {
        $this->stream .= $data;
    }

}