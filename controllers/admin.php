<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author  Greg London
 * 
 */
class Admin extends Admin_Controller {

    protected $section = "list"; 

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Training_m', 'training');
        $this->load->model('Usertraining_m', 'user');
        define('TRAINING_PATH', 'addons/shared_addons/modules/training/');
        // Make sure current user is an admin or manager
        if ($this->training->getUserGroup() == '2') {
            // current user is not an admin, redirect them away from the training module, this still needs to be tested
            redirect('admin');
        }
    }

    function index()
    {
        $data['courses'] = $this->training->getTraining();
        $data['count'] = $this->training->trainingCount();
        $data['message'] = 'Add/Delete Training:';
        $this->load->library('session');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean|max_length[100]');
        $this->form_validation->set_rules('checkbox', 'checkbox', 'trim'); // may not need this?
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        // if add is clicked
        if($this->input->post('add')){ 
		if ($this->form_validation->run() == FALSE)
		{
                    // errors will be called automatically through CI form_validation            
		}
		else
		{
                    $name = $this->input->post('name');
                    $slug = str_replace(' ', '-', $name);
                    $dt = new DateTime();
                    $time = $dt->format('Y-m-d');
                    $formData = array(
                    'name' => $name,
                    'slug' => $slug,
                    'date' => $time
                    );
                  $this->training->addTraining($formData);
                  $added = '<b>' . $name . '</b> has been added.';
                  $this->session->set_flashdata('message', $added);
                  redirect('admin/training');
		}
        }
        
        // if delete button is clicked
        if ($this->input->post('delete')) {
            $ids = array();
            if (!empty($_POST['checks'])) {           
                foreach($this->input->post('checks') as $selected) { 
                    $ids[] = $selected;
                }
            } 
           $affected = $this->training->deleteTraining($ids);
           if ($affected > 0) {
               $this->training->deleteTrainingFromUser($ids);
           }
           if ($affected > 1) {
               $training = 'trainings';
               
           } else {
               $training = 'training';
           }
           $deleted = $affected . ' ' . $training . ' have been deleted.';
           $this->session->set_flashdata('message', $deleted);
           redirect('admin/training');
        }
        
        $data['notify'] = $this->session->flashdata('message');
        $this->template->active_section = 'list';
        $this->template
                ->set('data', $data)
                ->set_breadcrumb('list:course list')
                ->build('admin_v'); 
     }
     
     public function edit()
     {
        $data['results'] = 'Search Employees by category:'; 
        $data['submit'] = FALSE;
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('typed', 'Typed', 'trim|required|xss_clean|max_length[100]');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        // if search is clicked
        if($this->input->post('search')){ 
		if ($this->form_validation->run() == FALSE)
		{
                    // errors will be called automatically through CI form_validation            
		}
		else
		{
                    $data['submit'] = TRUE;
                    $typed = $this->input->post('typed');
                    $selected = $this->input->post('by');
                    $result = $this->user->searchUsers($typed, $selected);
                    if ($result == 0) {
                        $data['searchResult'] = FALSE;
                        $data['results'] = 'No search results found for: <b> ' . $typed . '</b>';
                    } else {
                        $data['searchResult'] = TRUE;
                        $data['results'] = '<b>' . $typed . '</b> in <b>' . $selected . '</b> returned the following results:';
                    }
                    // loop through all the data to show in the view
                    $users = array();
                    foreach($result as $user) {
                        $id = $user['user_id'];
                        $users[$id]['count'] = $this->user->countTraining($id);
                        $users[$id]['id'] = $user['user_id'];
                        $users[$id]['firstName'] = $user['first_name'];
                        $users[$id]['lastName'] = $user['last_name'];
                    }   
                    $data['users'] = $users;
		}
        }
        $this->template->active_section = 'edit';
        $this->template
                ->set('data', $data)
                ->set_breadcrumb('edit:user search')
                ->build('edit_v'); 
     }       
     
     public function user($user_id = NULL)
     {
         $data['user_id'] = $user_id;
         if ($user_id == NULL) {
            redirect('admin/training/edit'); 
         }
         // make sure integer gets passed in and clean
         $cleaned_id = (int)htmlspecialchars($user_id); 
         $id = $cleaned_id;
         $userInfo = $this->user->getUserInfo($id);
         foreach($userInfo as $col) {
             $data['firstName'] = $col['first_name'];
             $data['lastName'] = $col['last_name'];
         }
         // get all the user's training data
         $training = $this->user->getUserTrainingData($id);
         if ($training > 0) {
             $items = array();
             foreach ($training as $row) {
                 $tId = $row['id'];
                 $items[$tId]['id'] = $row['id'];
                 $items[$tId]['trainingId'] = $row['training_id'];
                 $items[$tId]['date'] = $row['dateCompleted'];
                 foreach ($this->user->getTrainingNames($items[$tId]['trainingId']) as $info) {
                    $items[$tId]['name'] = $info['name'];   
                 }
             }
             $data['trainings'] = $items;
         }
         $data['count'] = $this->user->countTraining($id);
         $data['trainingNamesDropdown'] = $this->training->getTrainingNames();
         $this->load->helper(array('form', 'url'));
         $this->load->library('form_validation');
         $this->form_validation->set_rules('add', 'Add', 'trim|required|xss_clean|max_length[100]');
         $this->form_validation->set_rules(
                 'date', 
                 'Date', 
                 'trim|required|xss_clean|max_length[50]|regex_match[/^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/]'
         );
         $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
         $this->load->library('session');
         
         // if add is clicked
        if($this->input->post('add')){
		if ($this->form_validation->run() == FALSE) {
                    // errors will be called automatically through CI form_validation 
		} else {
                    $selected = $this->input->post('training');
                    $date = $this->input->post('date');
                    if ($this->user->isTrainingAlready($selected, $id)) {
                        $warning = 'The training you selected has already been added to the Employee\'s training.';
                        $data['notify'] = $this->session->set_flashdata('message', $warning);
                        redirect('admin/training/user/' . $id);
                    } else {
                    $update = $this->user->insertUserTraining($selected, $date, $id);
               if ($update > 0) { 
                   $edited = 'The user\'s training records has been updated.';
               } else {
                   $edited = 'There was a problem updating the record. Please try again.';
               }               
                $data['notify'] = $this->session->set_flashdata('message', $edited);
                redirect('admin/training/user/' . $id);
                    }
		}
        }    
            // if delete user is clicked...
            if ($this->input->post('delete')) {
                $ids = array();
                if (!empty($_POST['checks'])) {   
                    foreach($this->input->post('checks') as $selected) { 
                        $ids[] = $selected;
                    }
                }
                $affected = $this->user->deleteUserTraining($id, $ids);
                $deleted = $affected . ' training(s) were deleted.';
                $data['notify'] = $this->session->set_flashdata('message', $deleted); 
                redirect('admin/training/user/' . $id);
            }    
          $data['notify'] = $this->session->flashdata('message');
          $this->template->active_section = 'user';
          $this->template
                ->set('data', $data)
                ->set_breadcrumb('user:user training')
                ->build('user_v'); 
     }
}

