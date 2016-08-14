<?php
include "mysqli.php";
$db = new databasetool();
$content_type = 'text/plain';
$dirs = explode('/', strtok($_SERVER['REQUEST_URI'], '?'));
if ($dirs[2] == 'get') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('var' . $dirs[3]) . '\'');
    if ($db->num_rows($result)) {
        $var_info = $db->fetch_assoc($result);
        echo htmlspecialchars($db->unescape($var_info['data']));
    } else {
        echo '0';
    }


} else if ($dirs[2] == 'set') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('var' . $dirs[3]) . '\'');
    if ($db->num_rows($result)) {
        $db->query('UPDATE cloudvars
        SET data=\'' . $db->escape($dirs[4]) . '\'
        WHERE name=\'' . $db->escape('var' . $dirs[3]) . '\'');
    } else {
        $db->query('INSERT INTO cloudvars(name,data)
        VALUES(\'' . $db->escape('var' . $dirs[3]) . '\',\'' . $db->escape($dirs[4]) . '\')');
    }


} else if ($dirs[2] == 'change') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('var' . $dirs[3]) . '\'');
    if ($db->num_rows($result)) {
        $var_info = $db->fetch_assoc($result);
        $orig_num = $var_info['data'];
    } else {
        $orig_num = '0';
    }

    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('var' . $dirs[3]) . '\'');
    if ($db->num_rows($result)) {
        $db->query('UPDATE cloudvars
        SET data=\'' . $db->escape((strpos($dirs[4], '.') === false ? intval($dirs[4]) : floatval($dirs[4])) + $orig_num) . '\'
        WHERE name=\'' . $db->escape('var' . $dirs[3]) . '\'');
    } else {
        $db->query('INSERT INTO cloudvars(name,data)
        VALUES(\'' . $db->escape('var' . $dirs[3]) . '\',\'' . $db->escape($dirs[4]) . '\')');
    }


} else if ($dirs[2] == 'listadd') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    if ($db->num_rows($result)) {
        $list_info = $db->fetch_assoc($result);
        $orig_num = $db->unescape($list_info['data']);
    } else {
        $orig_num = '[]';
    }
    $array = json_decode($orig_num);
    array_push($array, $dirs[3]);
    $array = json_encode($array);

    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    if ($db->num_rows($result)) {
        $db->query('UPDATE cloudvars
        SET data=\'' . $db->escape($array) . '\'
        WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    } else {
        $db->query('INSERT INTO cloudvars(name,data)
        VALUES(\'' . $db->escape('list' . $dirs[4]) . '\',\'' . $db->escape($array) . '\')');
    }


} else if ($dirs[2] == 'listdelete') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    if ($db->num_rows($result)) {
        $list_info = $db->fetch_assoc($result);
        $orig_num = $db->unescape($list_info['data']);
    } else {
        $orig_num = '[]';
    }
    $array = json_decode($orig_num);
    if ($dirs[3] == 'all') {
        $array = array();
    } elseif ($dirs[3] == 'random' || $dirs[3] == 'any') {
        unset($array[rand(0,(count($array) - 1))]);
        $array = array_values($array);
    } elseif (count($array) + 1 >= (int)$dirs[3]) {
        unset($array[(int)$dirs[3] - 1]);
        $array = array_values($array);
    }
    $array = json_encode($array);

    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    if ($db->num_rows($result)) {
        $db->query('UPDATE cloudvars
        SET data=\'' . $db->escape($array) . '\'
        WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    } else {
        $db->query('INSERT INTO cloudvars(name,data)
        VALUES(\'' . $db->escape('list' . $dirs[4]) . '\',\'' . $db->escape($array) . '\')');
    }


} else if ($dirs[2] == 'listinsert') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[5]) . '\'');
    if ($db->num_rows($result)) {
        $list_info = $db->fetch_assoc($result);
        $orig_num = $db->unescape($list_info['data']);
    } else {
        $orig_num = '[]';
    }
    $array = json_decode($orig_num);
    if ($dirs[4] == 'last') {
        array_push($array, $dirs[3]);
    } elseif ($dirs[4] == 'random' || $dirs[4] == 'any') { // I don't know why 'any' would be here, since I'm not communicating with 1.4, but rather 2.0
        array_splice($array, rand(0, count($array)), 0, $dirs[3] );
        $array = array_values($array);
    } elseif ((int)$dirs[4] <= count($array) + 1) {
        array_splice($array, (int)$dirs[4] - 1, 0, $dirs[3] );
        $array = array_values($array);
    }
    $array = json_encode($array);

    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[5]) . '\'');
    if ($db->num_rows($result)) {
        $db->query('UPDATE cloudvars
        SET data=\'' . $db->escape($array) . '\'
        WHERE name=\'' . $db->escape('list' . $dirs[5]) . '\'');
    } else {
        $db->query('INSERT INTO cloudvars(name,data)
        VALUES(\'' . $db->escape('list' . $dirs[5]) . '\',\'' . $db->escape($array) . '\')');
    }

 
} else if ($dirs[2] == 'listreplace') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    if ($db->num_rows($result)) {
        $list_info = $db->fetch_assoc($result);
        $orig_num = $db->unescape($list_info['data']);
    } else {
        $orig_num = '[]';
    }
    $array = json_decode($orig_num);
    if ($dirs[3] == 'last') {
        $array[count($array) - 1] = $dirs[5];
    } elseif ($dirs[3] == 'random' || $dirs[3] == 'any') { // I don't know why 'any' would be here, since I'm not communicating with 1.4, but rather 2.0
        $array[rand(0, count($array) - 1)] = $dirs[5];
    } elseif ((int)$dirs[3] <= count($array) + 1) {
        $array[$dirs[3] - 1] = $dirs[5];
    }
    $array = json_encode($array);

    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    if ($db->num_rows($result)) {
        $db->query('UPDATE cloudvars
        SET data=\'' . $db->escape($array) . '\'
        WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    } else {
        $db->query('INSERT INTO cloudvars(name,data)
        VALUES(\'' . $db->escape('list' . $dirs[4]) . '\',\'' . $db->escape($array) . '\')');
    }


} else if ($dirs[2] == 'listgetitem') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[4]) . '\'');
    if ($db->num_rows($result)) {
        $list_info = $db->fetch_assoc($result);
        $orig_num = $db->unescape($list_info['data']);
    } else {
        $orig_num = '[]';
    }
    $array = json_decode($orig_num);
    if ($dirs[3] == 'last') {
        echo $array[count($array) - 1];
    } elseif ($dirs[3] == 'random' || $dirs[3] == 'any') { // I don't know why 'any' would be here, since I'm not communicating with 1.4, but rather 2.0
        echo $array[rand(0, count($array) - 1)];
    } elseif ((int)$dirs[3] <= count($array) + 1) {
        echo $array[$dirs[3] - 1];
    }


} else if ($dirs[2] == 'listlength') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[3]) . '\'');
    if ($db->num_rows($result)) {
        $list_info = $db->fetch_assoc($result);
        $orig_num = $db->unescape($list_info['data']);
    } else {
        $orig_num = '[]';
    }
    $array = json_decode($orig_num);
    echo count($array);


} else if ($dirs[2] == 'listcontains') {
    $result = $db->query('SELECT data FROM cloudvars
    WHERE name=\'' . $db->escape('list' . $dirs[3]) . '\'');
    if ($db->num_rows($result)) {
        $list_info = $db->fetch_assoc($result);
        $orig_num = $db->unescape($list_info['data']);
    } else {
        $orig_num = '[]';
    }
    $array = json_decode($orig_num);
    echo (int)in_array(strtolower($dirs[4]), array_map('strtolower', $array));


} else {
    header('HTTP/1.1 400 Bad request');
    echo 'Bad request; Path: '. $_SERVER['REQUEST_URI'];
}