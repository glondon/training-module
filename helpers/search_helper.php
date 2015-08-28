<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Search helper
 *
 * @author Greg London
 * 
 * This helper makes it easier to loop through search results and check database
 * for how many courses a user has completed
 * 
 * 
 */
function search() 
{
    $this->load->model('Usertraining_m');
}
