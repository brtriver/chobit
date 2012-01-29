<?php
namespace Chobit\Service;

interface InstallerInterface {
    function createForm($app);
    function writeConfigFile($params);
}
