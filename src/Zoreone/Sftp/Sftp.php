<?php
/**
 * Created by PhpStorm.
 * User: zoreone
 * Date: 2020-07-08
 * Time: 15:40
 */
namespace Zoreone\Sftp;

class Sftp {
    // 连接为NULL
    private $conn = NULL;
    //sftp resource
    private $ressftp = NULL;

    // 初始化
    public function __construct($config)
    {
        if( !$this->ressftp ){
            $this->conn = ssh2_connect($config->host, $config->port);
            if (ssh2_auth_password($this->conn, $config->username, $config->password)) {
                $this->ressftp = ssh2_sftp($this->conn);//启动引力传动系统
            } else {
                throw new \Exception("用户名或密码错误");
            }
        }

        return $this->ressftp;
    }

    /**
     * 判段远程目录是否存在
     * @param $dir /远程目录
     * @return bool
     */
    public function ssh2_dir_exits($dir){
        return file_exists("ssh2.sftp://" . intval($this->ressftp) .$dir);
    }

    /**
     * 下载文件
     * @param $remote /远程文件地址
     * @param $local /下载到本地的地址
     * @return bool
     */
    public function downSftp($remote, $local)
    {
        return copy("ssh2.sftp://" . intval($this->ressftp).$remote, $local);
    }

    /**
     * 文件上传
     * @param $local /本地文件地址
     * @param $remote /上传后的文件地址
     * @param int $file_mode
     * @return bool
     */
    public function upSftp($local,$remote, $file_mode = 0777)
    {
        return copy($local, "ssh2.sftp://" . intval($this->ressftp) . $remote);
    }

    /**
     * 删除远程目录中文件
     * @param $file
     * @return bool
     */
    public function deleteSftp($file)
    {
        return ssh2_sftp_unlink($this->ressftp, $file);
    }

    /**
     * 遍历远程目录
     * @param $remotePath
     * @return array
     */
    public function fileList($remotePath)
    {
        $fileArr = scandir('ssh2.sftp://' . intval($this->ressftp) . $remotePath);
        foreach ($fileArr as $k => $v) {
            if ($v == '.' || $v == '..') {
                unset($fileArr[$k]);
            }
        }
        return $fileArr;
    }

    /**
     * 创建远程目录中文件夹
     * @param $file
     * @return bool
     */
    public function ssh2_sftp_mkdir($dir)
    {
        return ssh2_sftp_mkdir($this->ressftp, $dir);
    }
}