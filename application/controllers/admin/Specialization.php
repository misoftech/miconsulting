<?php
class Specialization extends CI_Controller {

  function __construct()
	{
     parent:: __construct();
    
    error_reporting(0);

    if(isset($_SESSION['logged_in'])){
      $sess = $this->session->userdata();
      if(( $sess['logged_in']['user_level'] != '1' && $sess['logged_in']['user_level_status'] != '1' ) || ( $sess['logged_in']['user_level'] != '2' && $sess['logged_in']['user_level_status'] != '1' ) ){  redirect('home','refresh');}//header ('Location: '.base_url().'home '); }
    }else{
      redirect('login','refresh');
    }      

     

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->model('admin/SpecializationModel','app');
    $this->load->helper("url");
    $this->load->library("pagination");
	}

  
  function do_upload()
{
    $config['upload_path'] = './uploads/';
    $config['allowed_types'] = 'gif|jpg|png';
    $config['max_size'] = '100';
    $config['max_width']  = '1024';
    $config['max_height']  = '768';
    $config['overwrite'] = TRUE;
    $config['encrypt_name'] = FALSE;
    $config['remove_spaces'] = TRUE;
    if ( ! is_dir($config['upload_path']) ) die("THE UPLOAD DIRECTORY DOES NOT EXIST");
    $this->load->library('upload', $config);
    if ( ! $this->upload->do_upload('userfile')) {
        echo 'error';
    } else {

        return array('upload_data' => $this->upload->data());
    }
}
  public function index() 
  {
   
     
     $hospitals = json_encode($this->app->allspecialization()); 

     $data=array('headlines' => 'All Hospitals','hospitals'=>$hospitals);
     $data1=array('page_title'=>"All Hospitals | MiConsulting");
     
     
     $this->load->view('admin/temp/headercrm',$data1);
     $this->load->view('admin/specialization/index',$data);
     $this->load->view('admin/temp/footercrm');
  }  
  public function getsonSpecialization(){
      
      
      
      
      
      $doctors = $this->app->fetchAll();
        
        $config = array();
        $config["base_url"] = base_url() . "admin/specialization/index";
        $config["total_rows"] = count($doctors);//$this->Countries->record_count();
        $config["per_page"] = 10;
        $config["uri_segment"] =$_GET['id']?$_GET['id']:0;
        $choice = $config["total_rows"] / $config["per_page"];
        $config["num_links"] = round($choice);
        $this->pagination->initialize($config);
        $page = ($_GET['id']) ? $_GET['id'] : 0;
        $data["results"] = $this->app->fetchAll($config["per_page"], $page);
        $data["links"] = $this->pagination->create_links();
        
        
       
    
       $hospitals = json_encode($data); 
      echo  $hospitals;
      
      
      
  }
  public function addhospital()
  {
    if(isset($_POST['send']) && ($_POST['send']=="1"))
    {
        $pic =$_FILES['hospital_image']['name'];
        $pic_loc = $_FILES['hospital_image']['tmp_name'];
        $folder="assets/img/hospitals/";
        $move=$folder.$pic;
        move_uploaded_file($_FILES['hospital_image']['tmp_name'],$move);
        $hospList=$this->app->addhospitalDetail($_POST,$pic);
    
    }
    
    $hospList=$this->app->gethospitallist();
    $data1 = array(
        'page_title' => 'Add Hospital | MiConsulting'
    );
    $data=array("headline"=>"Add Hospital",'hospList'=>$hospList);
    
    $this->load->view('admin/temp/headercrm',$data1);
    $this->load->view('admin/specialization/addhospital',$data);
    $this->load->view('admin/temp/footercrm');
//    $this->_view->title         = "Add employee | MiConsulting";
//    $this->_view->headline      = "Add employee";
//    $this->_view->display('user/adduser.tpl.php');
  }
  public function addhospitalname(){
  
    $hospitalName=$_GET['hospitl'];
    $hsp=$this->app->addHospitalname($hospitalName);
    if(count($hsp)>=1){
     //$bank=$model->insertnewbank($_GET['bankName']);
     echo '<span style="color:green;" >'.$hospitalName.' Add Successfully</sapn>'; 
    }else{
     echo '<span style="color:red;" >'.$hospitalName.' Already Exist </span>'; 
    }
  
}
public function updateGetNewHospital(){
    
    $hospList=$this->app->gethospitallist();
    $html='';
   foreach($hospList as $val):
    $html.='<option value="'.$val->id.'_'.$val->name.'">'.$val->name.'</option>' ;  
   endforeach;
   $html.='<option value="oth">Other</option>' ;  
   echo $html;
}
  public function viewhospital(){
    $hospital = $this->app->hospitalById($this->uri->segment('4')); 
    $data=array('headline' => 'Hospital Profile','hospital'=>$hospital);
    $data1=array('page_title'=>"Profile | MiConsulting");
    
    $this->load->view('admin/temp/headercrm',$data1);
    $this->load->view('admin/specialization/viewhospital',$data);
    $this->load->view('admin/temp/footercrm');
      
  }
   public function viewhospitaldata(){
  
       $hospital = $this->app->hospitalById($_GET['id']); 
       echo json_encode($hospital); 
      
  }
    public function edithospital()
  {
       
    if(isset($_POST['send']) && ($_POST['send']=="1"))
    {
    
      $hospList=$this->app->edithospitalDetail($_POST,$this->uri->segment('4'));
    
    }
    
    $editdata=$this->app->hospitalById($this->uri->segment('4')); 
    
    $hospList=$this->app->gethospitallist();
    $data1 = array( 'page_title' => 'Add Hospital | MiConsulting');
    $data=array("headline"=>"Add Hospital",'hospList'=>$hospList,'editdata'=>$editdata);    
    $this->load->view('admin/temp/headercrm',$data1);
    $this->load->view('admin/specialization/edithospital',$data);
    $this->load->view('admin/temp/footercrm');

  }
  
  public function delhospitalwithid(){
    $id=$_GET['appid'];
    $this->app->udpatehospitalstatus($id);
    
    
  }
}