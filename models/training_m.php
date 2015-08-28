<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @author  Greg London
 * 
 */
class Training_m extends MY_Model
{
	protected $_table = 'default_training';
        
        /**
         * Returns what type of user (current user) from default_users table
         * group_id WHERE id=id (user_id) in profile table
         * (1 = admin, 2 = user, 3 = manager, 4 = asst manager
         */
        public function getUserGroup() 
        {
           if ($this->current_user->id) {
                $result = $this->db->query("SELECT group_id FROM default_users WHERE id=" . $this->current_user->id . " LIMIT 1");
                if ($result->num_rows() > 0) {
                    return $result->row('group_id'); 
                } else {
                    return 0;
                }    
           } else {
               // no one is logged in
               return 0;
           }
        }
        
        /**
         * Returns a list of courses from the database
         */
        public function getTraining()
        {
           $toReturn = array();
           $result = $this->db->query("SELECT * FROM " . $this->_table . " ORDER BY name");
           if($result->num_rows() > 0){
               foreach($result->result_array() as $thisRow){
                   $toReturn[] = $thisRow;
               }
           }
           
           return $toReturn;
        }
        
        /**
         * Grabs a unique (single) record from the database based on id(s)
         */
        public function getTrainingRecord($id)
        {
            if (count($id) > 1) {
                $rows = array();
             // do a different query if an array of data... 
                $ids = join(',', $id);
                $query = $this->db->query("SELECT * FROM " . $this->_table . " WHERE id IN (" . $ids . ")");
                if ($result->num_rows() > 0) {
                    foreach($result->result_array() as $row) {
                        $rows[] = $row;
                    }
                    return $row;
                }
            } else {
            $query = $this->db->query("SELECT * FROM " . $this->_table . " WHERE id=" . $id . " LIMIT 1");
            $row = $query->row(); 
            return $row;
            }
        }
        
         /**
         * Adds a new training record to the database
         */
        public function addTraining($formData)
        {
            $this->db->insert($this->_table, $this->db->escape($formData));
        }
        
         /**
         * Gets most recent training id from the database, 
         * not currently being used, but if needed we can use it
         */
        public function getLastInsert() 
        {
            return $this->db->insert_id();
        }
        
         /**
         * Deletes training record(s) - can be single or multiple, also runs another method that deletes trainings from
         * the other table as well...
         */
        public function deleteTraining($ids)
        {  
            $this->db->where_in('id', $ids);
            $this->db->delete($this->_table);
            return $this->db->affected_rows();            
        }
        
        /**
         * Deletes trainings from userTraining when trainings are successfully deleted from index page...
         * stops any unneeded confusion. The returned data isn't checked..
         */
        public function deleteTrainingFromUser($ids)
        {
            // this works when leaving id, but may need to change to training_id column if bugs occur in the future
            $this->db->where_in('id', $ids);
            $this->db->delete('default_userTraining');
            return $this->db->affected_rows(); 
        }
        
         /**
         * Updates a single training record (not currently being used, but may need in the future)
         */
        public function updateTraining($data, $where)
        {
            $this->db->update_string($this->_table, $data, $where); 
        }
        
        /**
         * Counts training records
         */
        public function trainingCount()
        {
            return $this->db->count_all($this->_table);
        }
        
        /**
         * Returns an array of all available trainings to use for dropdown selector
         */
        public function getTrainingNames()
        {
            $toReturn = array();
            $results = $this->db->query("SELECT * FROM " . $this->_table . " ORDER BY name");
            foreach($results->result_array() as $row) {
                $toReturn[] = $row;
            }
            return $toReturn;
        }
}

