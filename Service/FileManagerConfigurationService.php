<?php

namespace Artgris\Bundle\MediaBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileManagerConfigurationService extends \Twig_Extension
{

    /**
     * @var array
     */
    private $artgrisFileManagerConfig;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct($artgrisFileManagerConfig, ContainerInterface $container)
    {
        $this->container = $container;
        $this->artgrisFileManagerConfig = $artgrisFileManagerConfig;
    }

    public function getWebPath(string $conf)
    {
        if (isset($this->artgrisFileManagerConfig['conf'][$conf]['service'])) {
            $extra = [];
            $conf = $this->container->get($this->artgrisFileManagerConfig['conf'][$conf]['service'])->getConf($extra);
            return $conf['dir'];
        }

        if (!isset($this->artgrisFileManagerConfig['conf'][$conf])) {
            throw new \InvalidArgumentException("The conf \"$conf\" was not found in artgris_file_manager.");
        }

        $confPath = $this->artgrisFileManagerConfig['conf'][$conf]['dir'];
        $publicDir = '../' . $this->artgrisFileManagerConfig['web_dir'];

        if (mb_strpos($confPath, $publicDir) !== 0) {
            return true;
        }

        return mb_substr($confPath, mb_strlen($publicDir));
    }


    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('get_web_path', [$this, 'getWebPath']),
        ];
    }

}
