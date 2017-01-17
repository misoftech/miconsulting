<?php
class userController 
{

  private $_view;
  private $_model;

    
  public function __construct() 
  {
    require 'library/View.php';
    $this->_view = new View();
    require 'models/userModel.php';
    $this->_model = new userModel();    
  }

    
  public function __call($name, $args) 
  {
    $this->_view->title         = TITEL_404;
    $this->_view->headline      = HEADLINE_404;
    $this->_view->display('404/error.tpl.php');
  }

    
  public function indexAction() 
  { 
    $this->_view->title         = "Manage employees | MiConsulting";
    $this->_view->headline      = "Manage employees";
    $this->_view->users         = $this->_model->allUsers();     
    $this->_view->display('user/index.tpl.php');
  }

    
  public function userAction() 
  {
    if($_POST['send']=="1"){ $this->_model->userupdate($_POST); } 
    if($_POST['send']=="del"){ $go = $this->_model->userdel($_POST); header("Location: index.php?c=user"); }          
    $this->_view->title         = "Edit employees | MiConsulting";
    $this->_view->headline      = "Edit employees";
    $this->_view->page          = $this->_model->user($_GET['id']);
    $this->_view->users         = $this->_model->allUsers();
    $this->_view->display('user/user.tpl.php');
  }
    
    
  public function adduserAction()
  {
    if($_POST['send']=="1")
    {
        // generate passwort
        $password = $this->_model->makePass();        
        $newid = $this->_model->userinsert($_POST,$password);
        // send email
        $mailtext = '<html><head><title>Welcome at MiConsulting</title></head><body>
        <h2>Welcome at MiConsulting</h2>
        <p>Your access account for MiConsulting CRM is:</p>        
        <p>Username: '.$_POST["email"].'</p>
        <p>Password: '.$password.'</p>
        <p>Url: http://crm.miconsulting.in/</p>
        <p>We wish you all the best at MiConsulting</p>
        </body></html>';
        $empfaenger = $_POST['email'];
        $absender   = "info@miconsulting.in";
        $betreff    = "Your Account for MiConsulting CRM";            
        $header  = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $header .= "From: $absender\r\n";                        
        $header .= "X-Mailer: PHP ". phpversion();
        mail($empfaenger, $betreff, $mailtext, $header);        
        unset($_POST);        
        header("Location: index.php?c=user&a=user&id=".$newid);        
    }
    $this->_view->title         = "Add employee | MiConsulting";
    $this->_view->headline      = "Add employee";
    $this->_view->display('user/adduser.tpl.php');
  }
  
  
}