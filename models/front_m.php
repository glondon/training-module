<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @author  Greg London
 * 
 * Made this class due to conflicts between front and backend....
 * 
 */
class Front_m extends MY_Model
{
    protected $_table = 'default_userTraining';
    
    /**
     * 
     * @param type $trainingIds
     * Gets the titles of the trainings after the ids are grabbed via search for a single user
     */
    public function getTrainingNames($trainingIds)
    {
        if ($trainingIds == 0 || $trainingIds == NULL){       
            return;        
        } else if (count($trainingIds) > 1) {   
                $ids = join(',', $trainingIds); // implode() causing error
                $names = array();
                $result = $this->db->query("SELECT * FROM default_training WHERE id IN (" . $ids . ")");
                foreach($result->result_array() as $row) {
                $names[] = $row;
                }
            } else {
                $result = $this->db->query("SELECT * FROM default_training WHERE id=" . $trainingIds . "");
                $names[] = $result->row_array();
            }               
            return $names;               
    }
    
    /**
     * Gets all the rows from the table to get user_ids with completed training...
	 *
     */
    public function getAllUsersWithTraining() 
    {
        $users = array();
        $result = $this->db->query("SELECT * FROM " . $this->_table . "");
        if ($result->num_rows() > 0) {
            foreach($result->result_array() as $row){
                   $users[] = $row;
               }
               return $users;
        } else {
            return 0;
        }
    }
    
    /**
     * Returns completed training data for multiple users based on user_id's...
     */
    public function getUsersInfo($ids)
    {
       $toReturn = array();
       if (count($ids) > 1) {
          $jIds = join(',', $ids);
          $result = $this->db->query("SELECT * FROM default_profiles WHERE user_id IN (" . $jIds . ")");
          if ($result->num_rows() > 0) {
            foreach($result->result_array() as $row) {
                $toReturn[] = $row;
            }
            return $toReturn;
          } 
       } else {
           $result = $this->db->query("SELECT * FROM default_profiles WHERE user_id=" . $ids . "");
           if ($result->num_rows() > 0) {
               foreach ($result->result_array() as $row) {
                 $toReturn[] = $row;  
               }
               return $toReturn;
           }  
       }  
    }
}

