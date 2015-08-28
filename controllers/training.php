<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Public Training module controller
 *
 * @author  Greg London
 * 
 */
class Training extends Public_Controller
{
    public function __construct()
    {
	parent::__construct();
        $this->load->model('Training_m', 'training');
        $this->load->model('Usertraining_m', 'user');
        $this->load->model('Front_m', 'front'); // added due to conflicts between front & backend
        define('TRAINING_PATH', 'addons/shared_addons/modules/training/');
        define('LUBER_URL', 'http://*****/index.php/');
    }

    function index()
    {
        // get the current user & group they are in (admin=1,user=2,manager=3,asstmanager=4)
        if ($this->current_user->id) {
            $id = $this->current_user->id;
        } else {
            // not logged in...
            redirect('users/login');
        }
        $group = $this->training->getUserGroup();
        if ($group == 2) {
            $data['group'] = 2;
        } else if ($group == 3 || $group == 1 || $group == 4) {
            $data['group'] = 3;
        } else {
            $data['group'] = 0; // not a user, manager, or admin
        }
        $userInfo = $this->user->getUserInfo($id);
        foreach($userInfo as $col) {
             $data['firstName'] = $col['first_name'];
        }
        if ($group == 0) {
            // not logged in...
            redirect('users/login');
        } else if ($group == 2) {
            // user
            $data['title'] = "Training courses";
            $data['trainingCount'] = $this->user->countTraining($id);
            $training = $this->user->getUserTrainingData($id);
            if ($training > 0) {
                $data['noTraining'] = FALSE;
                $items = array();
                foreach ($training as $row) {
                    $id = $row['id'];
                    $items[$id]['id'] = $row['id'];
                    $items[$id]['trainingId'] = $row['training_id'];
                    $items[$id]['dateCompleted'] = $row['dateCompleted'];
                    foreach ($this->front->getTrainingNames($items[$id]['trainingId']) as $trainingName) {
                        $items[$id]['trainingName'] = $trainingName['name'];
                    }
                }
                $data['trainings'] = $items;
            } else {
              $data['noTraining'] = TRUE;  
            }      
        } else {
            // admin, manager, asst manager
            $data['title'] = 'Training courses completed by Employees';
            $usersCompleted = $this->front->getAllUsersWithTraining();
            if ($usersCompleted > 0) {
                $data['noTraining'] = FALSE;
                $row = array();
                foreach ($usersCompleted as $user) {
                    $uId = $user['user_id'];
                    $row[$uId]['userId'] = $user['user_id'];
                    // get user data based on user_id's grabbed
                    foreach ($this->front->getUsersInfo($row[$uId]['userId']) as $uData) {
                        $row[$uId]['firstName'] = $uData['first_name'];
                        $row[$uId]['lastName'] = $uData['last_name'];
                    }
                    // count their training
                    $row[$uId]['tCount'] = $this->user->countTraining($row[$uId]['userId']);
                }
                $data['rows'] = $row;
            } else {
               $data['noTraining'] = TRUE; 
            }
        }
        $this->template
                ->set('data' , $data)
                ->build('training_v');
     }
     
     public function ulist($user_id = NULL) 
     {
         // get the current user & group they are in (admin=1,user=2,manager=3,asstmanager=4)
        if ($this->current_user->id) {
            $id = $this->current_user->id;
        } else {
            // not logged in...
            redirect('users/login');
        }
        $group = $this->training->getUserGroup();
        // make sure only admin, manager, or asst manager
         if ($group == 3 || $group == 1 || $group == 4) {
             // default to group 3
            $data['group'] = 3;
        } else {
            $data['group'] = 0; // not anything...
        }
        if ($group == 2) {
            redirect('training');
        } else {
            // get id from url
            $data['user_id'] = $user_id;
            if ($user_id == NULL) {
            redirect('training');
         }
         // make sure integer gets passed in and clean
         $cId = (int)htmlspecialchars($user_id); 
         // get user info.. we know they already have training so don't need to check that..
         $user = $this->user->getUserInfo($cId);
         foreach ($user as $col) {
             $data['firstName'] = $col['first_name'];
             $data['lastName'] = $col['last_name'];
         }
         $data['title'] = 'Courses ' . $data['firstName'] . ' ' . $data['lastName'] . ' has completed: ';
         // we already know user has training & > 0 from previous controller...
         $training = $this->user->getUserTrainingData($cId);
         $rows = array();
         foreach ($training as $row) {
             $id = $row['id'];
             $rows[$id]['id'] = $row['id'];
             $rows[$id]['trainingId'] = $row['training_id'];
             $rows[$id]['dates'] = $row['dateCompleted'];
             foreach ($this->front->getTrainingNames($rows[$id]['trainingId']) as $tName) {
                $rows[$id]['trainingName'] = $tName['name'];
            } 
         }
         $data['uData'] = $rows;
         $data['count'] = $this->user->countTraining($cId);
         }
         $this->template
                ->set('data' , $data)
                ->build('list_v');
     }
}

