<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Training module
 *
 * @author  Greg London
 * 
 * ---version 1.1 additions: (...) -> DONE
 * -Fix multiple delete bug --again :-( -> DONE
 * -Update page titles & navigation = Course List | User Search | User Training -> DONE (urls the same though)
 * -Add # of completed trainings to search results = First | Last | Courses Completed count(*) -> DONE
 * -Fix table bug on user search (refactored code to be cleaner) -> DONE
 * -Fix date update (make date manual) on new dataTable in user controller -> DONE
 * -Update view files to shorter naming convention so easier to work with -> DONE
 * -Modify sql date column x 2 on install to get rid of not needed datetime() -> DONE
 * -Changed model names in admin controller __Construct (made shorter) for easier access and shorter code -> DONE
 * -Stored $cleaned_id into $id variable for shorter code -> DONE
 * -Added breadcrumbs -> DONE
 * -Added sub navigation menus for admin_menu -> DONE
 * -Added active navigation for better look -> DONE
 * -Add date to search with calendar feature & add title = Course: | Date Completed: -> DONE
 * -Add regex_match[] to date field in case date is manually inputted -> DONE 
 * -Add front-end my account widget/plugin... probably don't need -> DONE
 * -Front-End Add - if user -> show alpha list of courses with complete date -> DONE
 * -Front-End Add - if manager -> show list of users -> hyperlink to view training for each user -> DONE
 * -Edit index training controller to be main - user & list if manager -> DONE
 * -Add user controller to training - move code over -> DONE
 * -Fixed css/js load on front-end training controller -> DONE
 * -Add front-end menu item..(Added manually via Structure/Navigattion but can be done dynamically here if needed) in Admin (not in details.php) -> DONE
 * -Fixed dateCompleted bug in back-end & front-end -> DONE
 * Front-End Add - if manager -> show drop down for shop (multi-shop)->??????? (Not sure if needed anymore)
 * -Delete stopped working AGAIN in admin/user, troubleshoot again.... fixed -> DONE
 * 
 */
class Module_Training extends Module {

    public $version = '1.1';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Training'
            ),
            'description' => array(
                'en' => 'Training for *****.com'
            ),
            'frontend' => true,
            'backend' => true,
            'skip_xss' => true,
            'menu' => 'content',
            'roles' => array(
				'put_live', 'edit_live', 'delete_live'
			),
            'sections' => array(
				'list' => array(
					'name' => 'Course List',
					'uri' => 'admin/training',
					'shortcuts' => array(
						array(
							'name' => 'course list',
							'uri' => 'admin/training',
							'class' => 'add',
						),
					),
				),
                'edit' => array(
					'name' => 'User Search',
					'uri' => 'admin/training/edit',
					'shortcuts' => array(
						array(
							'name' => 'user search',
							'uri' => 'admin/training/edit',
							'class' => 'add',
						),
					),
				),
                'user' => array(
					'name' => 'User Training',
					'uri' => 'admin/training/user',
					'shortcuts' => array(
						array(
							'name' => 'user training',
							'uri' => 'admin/training/user',
							'class' => 'add',
						),
					),
				),
			), 
        );
    }

    public function install()
    {
        $this->dbforge->drop_table('training');
        $this->dbforge->drop_table('userTraining');
        $this->db->delete('settings', array('module' => 'training'));

        $training = array(
            'id' => array(
            'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'slug' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'date' => array(
                'type' => 'date'
            )
        );
        
        // create the 2nd table manually so no conflicts occur...
        $this->db->query('
            CREATE TABLE IF NOT EXISTS `default_userTraining` (
            `id`          int(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id`        int(100)          DEFAULT NULL,
            `training_id` int(100)                           DEFAULT NULL,
            `dateCompleted` date           DEFAULT NULL,
            PRIMARY KEY(id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;'
        );
        
        // we can dynamically install(uninstall) training navigation in the default_navigation_links table (if needed for front-end)
        // hold off on this unless requested...
     
        $training_setting = array(
            'slug' => 'training_setting',
            'title' => 'Training Setting',
            'description' => 'Settings for training module',
            'default' => '1',
            'value' => '1',
            'type' => 'select',
            'options' => '1=Yes|0=No',
            'is_required' => 1,
            'is_gui' => 1,
            'module' => 'training'
        );

        $this->dbforge->add_field($training);
        $this->dbforge->add_key('id', true);
        

        // Let's try running our DB Forge Table and inserting some settings
        if ( ! $this->dbforge->create_table('training') OR ! $this->db->insert('settings', $training_setting))
        {
            return false;
        }

        // No upload path for our module? If we can't make it then fail
        if ( ! is_dir($this->upload_path.'training') AND ! @mkdir($this->upload_path.'training',0777,true))
        {
            return false;
        }
        
        $query = $this->db->query("SELECT * FROM default_blog WHERE category_id=2");

        foreach ($query->result_array() as $row)
        {
            $data = array(
               'name' => $row['title'],
               'slug' => $row['slug'],
               'date' => $row['created']
            );

        $this->db->insert('default_training', $data); 
           
        }

        // It worked
        return true;
    }

    public function uninstall()
    {
        $this->dbforge->drop_table('training');
        $this->dbforge->drop_table('userTraining');

        $this->db->delete('settings', array('module' => 'training'));

        // Put a check in to see if something failed, otherwise it worked
        return true;
    }


    public function upgrade($old_version )
    {
        // Upgrading manually for now, unless this is needed in the future...
        $version = 1.1;
        return true;
    }
    
    public function help()
    {
        // Return a string containing help info
        return 'This module is used to add, update, and delete required training courses for Employees. For Admin use only.';
         
    }
    
    public function admin_menu(&$menu) 
    {
        $menu['Training'] = array(
            'Training' => 'admin/training',
             'User Search' => 'admin/training/edit',
             'User Training' => 'admin/training/user'           
        );
        
        add_admin_menu_place('Training', 2);
    }
}

