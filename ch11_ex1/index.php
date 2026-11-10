<?php
//get tasklist array from POST
$task_list = filter_input(INPUT_POST, 'tasklist', 
        FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if ($task_list === NULL) {
    $task_list = [];
    
//    add some hard-coded starting values to make testing easier
//    $task_list[] = 'Write chapter';
//    $task_list[] = 'Edit chapter';
//    $task_list[] = 'Proofread chapter';
}

//get action variable from POST
$action = filter_input(INPUT_POST, 'action');

//initialize error messages array
$errors = [];

//process
switch( $action ) {
    case 'Add Task':
        //Adds a task to the array once sanitized
        $new_task = filter_input(INPUT_POST, 'newtask');
        if (empty($new_task)) {
            $errors[] = 'The new task cannot be empty.';
        } else {
            //trim before passing
            $new_task = trim($new_task);
            array_push($task_list, $new_task);
        }
        break;
    case 'Delete Task':
        //Deletes the task from the array
        $task_index = filter_input(INPUT_POST, 'taskid', FILTER_VALIDATE_INT);
        if ($task_index === NULL || $task_index === FALSE) {
            $errors[] = 'The task cannot be deleted.';
        } else {
            //Delete
            unset($task_list[$task_index]);
            $task_list = array_values($task_list);
        }
        break;

    case 'Modify Task':
        $task_index = filter_input(INPUT_POST, 'taskid', FILTER_VALIDATE_INT);

        //must be set before breaking out to allow for the modify section to display
        //Passes the value along
        $task_to_modify = $task_list[$task_index];
        break;
    
    case 'Save Changes':
        //get the index of the modified task
        $modified_task_index = filter_input(INPUT_POST, 'modifiedtaskid', FILTER_VALIDATE_INT);

        //get the string from the textbox
        $task_modified = filter_input(INPUT_POST, 'modifiedtask');

        //change the task into the new, modified, one
        $task_list[$modified_task_index] = $task_modified;

        //Wipe this variable and break for the section to stop displaying
        $task_to_modify = "";
        break;
    
    case 'Cancel Changes':
        //sets the variable to empty so the section does not display and breaks out
        $task_to_modify = "";
        break;
    
    case 'Promote Task':
        $task_index = filter_input(INPUT_POST, 'taskid', FILTER_VALIDATE_INT);
        //Most likely not needed but I'm testing for null just in case
        if ($task_index === NULL || $task_index === FALSE) {
            $errors[] = 'The task cannot be modified.';
        }
        //If the index is 0 (if you're picking the first task from the list) display an error message
        else if ($task_index === 0)
        {
            $errors[] = "You can't promote the first task.";
        }
        //Otherwise swap the current value with the previous one(to move it "up")
        else
        {
            [$task_list[$task_index-1], $task_list[$task_index]] = [$task_list[$task_index], $task_list[$task_index-1]];
        }
        break;
        
    case 'Sort Tasks':
        //Sorts the tasks alphabetically
        sort($task_list, SORT_NATURAL | SORT_FLAG_CASE);
        break;
    

}

include('task_list.php');
?>