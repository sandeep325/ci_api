<?php
require APPPATH.'libraries/REST_Controller.php';

class Image extends REST_Controller {

    /*
    INSERT: POST REQUEST TYPE
    UPDATE: PUT REQUEST TYPE
    DELETE: DELETE REQUEST TYPE
    LIST: GET REQUEST TYPE

    
    */

// GET:<project_url>/index.php/Image
    public function index_get() {
        // die("get list");
         $this->load->model('api/Image_model');
       $getrecord =  $this->Image_model->getData();

       if(count($getrecord) > 0 ) {

    
           $this->response(array(
               "status" =>200,
               "message"=>"Fetch all record",
               "data" =>  $getrecord,
           ),REST_Controller::HTTP_OK);

       } else {

        $this->response(array(
            "status" =>404,
            "message"=>"No record found",
            "data" =>  $getrecord,
        ),REST_Controller::HTTP_NOT_FOUND);

       }

    }
// ================================================================================================
// POST:http://localhost/ci_api/index.php/api/Image
    public function index_post(){
$this->load->model('api/Image_model');
$this->load->library('Lib_common');

   if(empty($_POST['userid']) || $_POST['userid'] == '') {
    $this->response(array(
        "status" =>404,
        "message"=>"User id can not empty.",
        "data" =>  [],
    ),REST_Controller::HTTP_NOT_FOUND);

   }
if(isset($_FILES['image']['name']) && count($_FILES['image']['name']) > 0 ){
    $image = $_FILES['image'];
// echo "<pre>"; print_r($image); die;
    $image = $this->lib_common->renameFile($image,rand(1,99));
    // echo base_url(); die;
    $uploadedFileArr = $this->lib_common->uploadFiles($image,dirname($_SERVER["SCRIPT_FILENAME"])."/upload/");
    // echo "<pre>"; print_r($uploadedFileArr); die;
    $len = count($uploadedFileArr);
    for($i = 0; $i < $len; $i++){

        if( isset($uploadedFileArr[$i]['is_uploaded']) && $uploadedFileArr[$i]['is_uploaded'] == "YES"  ){
            
            $update_array = [];
            $update_array['image'] = isset($uploadedFileArr[$i]['filename']) ? $uploadedFileArr[$i]['filename'] : NULL;
                $imgs [$i]= $update_array['image'];
        }

    } // End For Loop
            //  print_r($imgs); die;
          $data = [
              "user_id" => $_POST['userid'],
              "image" => json_encode($imgs,JSON_FORCE_OBJECT),
              "create_date"=> date('d-m-Y-H-i-s'),

          ];
        //   echo "<pre>"; print_r($data); die;
           $insertData = $this->Image_model->insertData("images_upload",$data);
          if($insertData) {
      $value = json_decode(json_encode($insertData), true);
    //   echo "<pre>"; print_r($value); die;
      foreach ( json_decode($value[0]['image'])  as $val) {
           $imagedb = []; 
           $imagedb["imgurl"] = base_url().'upload/'.$val;
           $allimgurl [] = $imagedb["imgurl"];
      } 

      $data = [
        'id' => $value[0]['id'],
        'userid' => $value[0]['user_id'],
        'create_date' => $value[0]['create_date'],
        'images' => $allimgurl,


      ];
            $this->response(array(
                "status" =>201,
                "message"=>"data inserted.",
                "data" =>  $data,
            ),REST_Controller::HTTP_CREATED);


          } else {

            $this->response(array(
                "status" =>500,
                "message"=>"Could not inserted.",
                "data" =>  [],
            ),REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

          }


   } else {

    $this->response(array(
        "status" =>404,
        "message"=>"Image can not empty",
        "data" =>  [],
    ),REST_Controller::HTTP_NOT_FOUND);


   }


}


}

?>