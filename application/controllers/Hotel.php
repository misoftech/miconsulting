<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hospital extends CI_Controller {

  public function __construct()
  {
    parent:: __construct();
    error_reporting(0);
    $this->active['current_page'] = $this->uri->segment(1);
    $this->load->model('HospitalModel','model');
  }
  
  public function index() 
  {  
  

$speciliztion=$this->model->getspeciliztions();
 $speOption=''; 
 foreach($speciliztion as $val ){
    $speOption.="<option value=".$val->id.">".$val->sepcialization."</option>";
 }
                          
  $allhospital=$this->model->getallhospital();      
                          
$hospitalLocation=$this->model->gethospitalarea();
$data=array('speciliztion'=>$speciliztion,'hospitalLocation'=>$hospitalLocation,'speOption'=>$speOption,"allhospital"=>$allhospital );
     $this->load->view('template/header');
     $this->load->view('hospital/index.php',$data);
     $this->load->view('template/footer',$data);
  }  
  
  
  
  
  public function result() 
  {      
     $this->load->view('template/header');
     $this->load->view('hospital/result.php');
     $this->load->view('template/footer');
     
     
     
     
     
     
     
  } 
  
  
  
  
  
  
  
  
  }


?>