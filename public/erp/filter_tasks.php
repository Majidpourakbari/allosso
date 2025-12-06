<?php
require_once 'controlls/db/functions.php';

header('Content-Type: application/json');

try {
    // Get filter parameters
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $priority = isset($_GET['priority']) ? $_GET['priority'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $allo_section = isset($_GET['allo_section']) ? $_GET['allo_section'] : '';
    $label = isset($_GET['label']) ? $_GET['label'] : '';
    
    // Build the base query
    $query = "SELECT t.*, u.name as creator_name, u.avatar as creator_avatar, 
              GROUP_CONCAT(DISTINCT ur.name) as receiver_names,
              GROUP_CONCAT(DISTINCT ur.avatar) as receiver_avatars,
              DATE_FORMAT(t.date_create, '%Y-%m-%d %H:%i') as formatted_date,
              COALESCE(
                  CASE 
                      WHEN (SELECT COUNT(*) FROM tasks_checklists tc WHERE tc.task_id = t.id) = 0 THEN 0
                      ELSE ROUND(
                          (SELECT COUNT(*) FROM tasks_checklists tc WHERE tc.task_id = t.id AND tc.status = 1) * 100.0 / 
                          (SELECT COUNT(*) FROM tasks_checklists tc WHERE tc.task_id = t.id), 0
                      )
                  END, 0
              ) as calculated_progress
              FROM tasks t 
              LEFT JOIN users u ON t.user_creator = u.id 
              LEFT JOIN task_users tu ON t.id = tu.task_id
              LEFT JOIN users ur ON tu.user_id = ur.id 
              WHERE (t.user_creator = :my_profile_id OR tu.user_id = :my_profile_id)";
    
    $params = [':my_profile_id' => $my_profile_id];
    
    // Add filter conditions
    if ($status !== '') {
        $query .= " AND t.status = :status";
        $params[':status'] = $status;
    } else {
        // If no status filter is applied, exclude archived tasks by default
        $query .= " AND t.status != 4";
    }
    
    if ($priority !== '') {
        $query .= " AND t.priority = :priority";
        $params[':priority'] = $priority;
    }
    
    if ($category !== '') {
        $query .= " AND t.category = :category";
        $params[':category'] = $category;
    }
    
    if ($allo_section !== '') {
        $query .= " AND t.allo_section = :allo_section";
        $params[':allo_section'] = $allo_section;
    }
    
    if ($label !== '') {
        $query .= " AND t.label = :label";
        $params[':label'] = $label;
    }
    
    // Add group by and order by
    $query .= " GROUP BY t.id ORDER BY t.id DESC";
    
    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Return the filtered tasks
    echo json_encode($tasks);
    
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 