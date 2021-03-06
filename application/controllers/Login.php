<?php 


class Login extends CI_Controller
{
	
	function __construct()
	{
        error_reporting(0);
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('session');
		$this->load->model('login_model', 'model');
		
		$this->active['current_page'] = $this->uri->segment(1);
	}


	public function index()
	{
		$session = $this->session->userdata('logged_in');

		if(isset($session['user_id'])){
	        
	        $data=array('active'=>$this->active,'title'=>'Customer Sign In - Sign Up | MiConsulting','headline'=>"Sign In - Sign Up" );
			$this->load->view('template/header' , $data);
			$this->load->view('dashboard/index');
			$this->load->view('template/footer');
			
		}else{
        
	        $data=array('active'=>$this->active,'title'=>'Customer Sign In - Sign Up | MiConsulting','headline'=>"Sign In - Sign Up" );
			$this->load->view('template/header' , $data);
			$this->load->view('login/index');
			$this->load->view('template/footer');
		}

	}

	public function registration()
	{
		$data = array(
				'firstname' => $this->input->post('firstname'),
				'lastname' => $this->input->post('lastname'),
				'email' => $this->input->post('email'),
				'type_of_user' => $this->input->post('type_of_user'),
				'mobile' => $this->input->post('mobile'),
				'password' => $this->input->post('password')
			);

		$result = $this->model->check_new_user($data);

		if($result->result_id->num_rows == "1")
		{
			$data['reg'] = "1"; // There is already an account with this email address provided
			$this->load->view('template/header' , $this->active);
			$this->load->view('login/index', $data);
			$this->load->view('template/footer');
		
		}else{

			$inserted_id = $this->model->registration_insert($data);
			
			if(isset($inserted_id))
			{
				$data = $this->model->retrieve_sess_data($inserted_id);
				$result = $data->result();

				$session_data = array(
					'user_id' => $inserted_id,
					'user_email' => $result[0]->email,
					'user_name' => $result[0]->firstname." ".$result[0]->lastname,
					'user_key' => md5($inserted_id.session_id()),
					'avatar' => $result[0]->avatar,
					'user_level' => $result[0]->user_type,
					'user_level_status' => $result[0]->registration_status
				);
				$this->session->set_userdata('logged_in', $session_data);

				$data = array(
					'firstname' => $result[0]->firstname,
					'lastname' => $result[0]->lastname,
					'username' => $result[0]->username,
					'password' => $result[0]->password,
					'street' => $result[0]->street,
					'zip' => $result[0]->zip,
					'city' => $result[0]->city,
					'state' => $result[0]->state,
					'country' => $result[0]->country,
					'email' => $result[0]->email,
					'phone' => $result[0]->phone,
					'avatar' => $result[0]->avatar
					);

				$this->load->view('template/header' , $this->active);
				$this->load->view('user/user_edit', $data);
				$this->load->view('template/footer');	
			}
			
		}
		
	}

	public function login_check()
	{
		$data = array(
			'email' => $this->input->post('email') ,
			'password' => $this->input->post('pass')
		);

		

		ob_start();
		 $result = $this->model->login($data); 

		if ($result == 'TRUE')
			{
			$email = $this->input->post('email');
			$result = $this->model->login_fetch($email); 
			if ($result != false)
				{
					$session_data = array(
					'user_id' => $result[0]->id,
					'user_email' => $result[0]->email,
					'user_name' => $result[0]->firstname." ".$result[0]->lastname,
					'user_key' => md5($result[0]->id.session_id()),
					'avatar' => $result[0]->avatar,
					'user_level' => $result[0]->user_type,
					'user_level_status' => $result[0]->registration_status
					);
					// Add user data in session
					$this->session->set_userdata('logged_in', $session_data);
					$session = $this->session->userdata('logged_in');
					if($session['user_level'] == '3'){
						//header('Location:'.base_url().'Home');
	                	echo '<script> window.location.href = "'.base_url().'home"; </script>';
					}elseif(($session['user_level'] == '0' && $session['user_level_status'] == '1') || ($session['user_level'] == '2' && $session['user_level_status'] == '1') || ($session['user_level'] == '1' && $session['user_level_status'] == '1')){
						//header('Location:'.base_url().'admin/homecrm');
						echo '<script> window.location.href = "'.base_url().'admin/homecrm"; </script>';
					}else{
						//header('Location:'.base_url().'home');
					 	echo '<script> window.location.href = "'.base_url().'home"; </script>';
					}

				}
			}
		  else
			{
				if ($result == "1") {
					$data = array('error_message' => '1');
				}elseif($result == "2"){
					$data = array('error_message' => '2');
				}elseif($result == "3"){
					$data = array('error_message' => '3');
				}
			
			$this->load->view('template/header' , $this->active);
			$this->load->view('login/index', $data);
			$this->load->view('template/footer');
			}
	}
		

	// Logout from admin page

	public function logout()
	{
		$sess_array = array('username' => '');
		$this->session->unset_userdata('logged_in', $sess_array);
		$data['message_display'] = 'Successfully Logout';
		$this->load->view('template/header' , $this->active);
		$this->load->view('login/index', $data);
		$this->load->view('template/footer');
	}
	

}


 ?> 
