<?php

namespace Forexceccom\Backend\Controllers;
use Forexceccom\Repositories\Formatname;

define('MyS3UploadFolder', 'download');
class FormuploadController extends ControllerBase
{
    public function indexAction()
    {
        $uploadFiles = array();
        // Check if the user has uploaded files
        if ($this->request->hasFiles() == true)
        {
            \S3::setAuth(MyS3Key, MyS3Secret);
            $message = array(
                "type" => "error",
                "message" => ""
            );
            $numberOfSuccess = 0;
            $numberOfFail = 0;
            $numberOfFiles = 0;
            $bucket = MyS3Bucket;
            $paths = array();
            //Upload files
            foreach ($this->request->getUploadedFiles() as $file)
            {
                $filename = $_FILES['upload-files']['tmp_name'][0];
                $fp = fopen($filename, "r");//mở file ở chế độ đọc

                $contents = fread($fp, filesize($filename));//đọc file

                var_dump($contents);exit;
                echo "<pre>$contents</pre>";//in nội dung của file ra màn hình
                fclose($fp);//đóng file
            }
            exit;
            if($numberOfSuccess > 0 ){
                \S3::invalidateDistribution(MyDistributionId,$paths);
            }
            if($numberOfSuccess==$numberOfFiles)
            {
                $message = array(
                    "type" => "success",
                    "message" => "All files are uploaded successfully!<br>".$message["message"]
                );
            }
            else
            {
                if($numberOfSuccess>=$numberOfFail)
                {
                    $message["type"] = "info";
                }
                else
                {
                    $message["type"] = "error";
                }
            }
            $this->view->message = $message;
        }
        $this->view->uploadFiles = $uploadFiles;
    }
}