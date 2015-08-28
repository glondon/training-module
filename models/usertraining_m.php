<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @author  Greg London
 * 
 */
class Usertraining_m extends MY_Model
{
	protected $_table = 'default_userTraining';
        
        /**
         * Returns a list of users from the database based on category selected
         * searches the default_profiles table only
         */
        public function searchUsers($typed, $selected)
        {   
           $escaped = $this->db->escape($typed);
           $clean = trim($escaped, "'");
           $result = $this->db->query("SELECT * FROM default_profiles WHERE " . $selected . " LIKE '%" 
                   . $clean . "%' ORDER BY first_name LIMIT 10");
           if($result->num_rows() > 0){
               $toReturn = array();
               foreach($result->result_array() as $row){
                   $toReturn[] = $row;
               }
               return $toReturn;
           } else {
               return 0;
           }
        }
        
        /**
         * Returns a single user row based on what user_id is grabbed from url
         * accesses default_profile table after user clicked on 
         */
        public function getUserInfo($id) 
        {
            $toReturn = array();
            $result = $this->db->query("SELECT * FROM default_profiles WHERE user_id=" . $id . " LIMIT 1");
            if ($result->num_rows() > 0) {               
                    $toReturn[] = $result->row_array();   
            }
            return $toReturn;
        }
        
        /**
         * Returns a list of completed training IDs for a single user, gets the user_id
         * 
         */
        public function getUserTrainingIds($id) 
        {   
            $result = $this->db->query("SELECT * FROM " . $this->_table . " WHERE user_id=" . $id . "");
            if ($result->num_rows() > 0) {
                $trainingIds = array();
                foreach($result->result_array() as $row) {
                    $trainingIds[] = $row['training_id'];
                }              
                return $trainingIds;               
            } else {   
                return 0;    
            } 
        }  
        
        /**
         * Returns ALL completed training data for a single user based on the user_id passed in, 
         * 
         */
        public function getUserTrainingData($id)
        {
           $toReturn = array();
            $result = $this->db->query("SELECT * FROM " . $this->_table . " WHERE user_id=" . $id . "");
            if ($result->num_rows() > 0) {
                foreach($result->result_array() as $row) {
                    $toReturn[] = $row;
                }
                return $toReturn;
            } else {
                return 0;
            }  
        }
        
        /**
         * Counts how many trainings a user has completed and returns the #
         */
        public function countTraining($id)
        {
            $this->db->where('user_id', $id);
            $this->db->from($this->_table);
            $count = $this->db->count_all_results();
            return $count;
        }
        
        /**
         * 
         * @param type $trainingIds
         * Gets the titles of the trainings after the ids are grabbed via search for a single user
         */
        public function getTrainingNames($trainingIds)
        {
            if ($trainingIds == 0 || $trainingIds == NULL){       
                return;        
            } 
            $names = array();
            if (count($trainingIds) > 1) {   
                    $ids = join(',', $trainingIds); // implode() causing error                   
                    $result = $this->db->query("SELECT * FROM default_training WHERE id IN (" . $ids . ")");
                    foreach($result->result_array() as $row) {
                    $names[] = $row;
                    }
                } else {
                   // $id = join(',', $trainingIds); // this is conflicting between different controllers...
                    $result = $this->db->query("SELECT * FROM default_training WHERE id=" . $trainingIds . "");
                    $names[] = $result->row_array();
                }               
                return $names;               
        }
        
        /**
         * Inserts completed trainings into the userTraining database as a single row
         * 
         */
        public function insertUserTraining($selected, $date, $id)
        {
            $date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';
            if (!preg_match($date_regex, $date)) {
                // the date entered did not match YYYY-MM-DD (the controller didn't catch it) - put today's date in
                $dt = new DateTime();
                $time = $dt->format('Y-m-d');
            } else {
                // datepicker only allows numbers and is correctly formatted so INSERT
                $time = $date;
            }
                $data = array(
                  'user_id' => $id,  
                  'training_id' => $selected,
                  'dateCompleted' => $time
                );
                $this->db->insert($this->_table, $data);
                return $this->db->affected_rows();        
        }
        
        /**
         * 
         * @param type $id
         * @param type $ids
         * @return type
         * 
         * Deletes training records, deletes multiple & single trainings based on user_id
         * need to query table for unique primary keys before deleting data
         * 
         */
        public function deleteUserTraining($id, $ids)
        {
            $j = implode(',', $ids);
            $count = count($ids);
            if ($count > 1) {
                 // get primary ids 1st...
                $query = $this->db->query("SELECT * FROM " . $this->_table . " WHERE user_id=" 
                        . $id . " AND training_id IN (" . $j . ")");
                foreach($query->result_array() as $row) {
                    $this->db->where('id', $row['id']);
                    $this->db->delete($this->_table);
                } 
                //return $this->db->affected_rows($query); isn't working, just return $count instead (for now)
                return $count;
            } else {
                $this->db->delete($this->_table, array('user_id' => $id, 'training_id' => $j));
                return $this->db->affected_rows();
            }    
        }
        
        /**
         * checks database to make sure the record isn't already saved to the userTraining table based on user_id
         * returns bool true or false based on query
         */
        public function isTrainingAlready($selected, $id)
        {
            $toCheck = array();
            $query = $this->db->query("SELECT * FROM " . $this->_table . " WHERE user_id=" . $id . "");
             if ($query->num_rows() > 0) {
                foreach($query->result_array() as $row) {
                    $toCheck[] = $row['training_id'];
                }    
                if (in_array($selected, $toCheck)) {
                    return true;
                } else {
                    return false;
                }
             } 
        }
}

