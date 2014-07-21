<?php
/**
    rest 路由
 *  */

/**
 * 自动生成URL路由
 * array("home/dataModel/:id\d", "HOME/DataModel/read", "", "get", "json"),
array("home/dataModel/:id\d", "HOME/DataModel/update", "", "put", "json"),
array("home/dataModel", "HOME/DataModel/index", "", "get", "json"),
array("home/dataModel", "HOME/DataModel/insert", "", "post", "json"),
array("home/dataModel/:id", "HOME/DataModel/delete", "", "delete", "json"),
 */
if(!function_exists("routeMaker")) {
    function routeMaker($resName, $mapUrl, $methods = array()) {
        if(!$methods) {
            $methods = array("list", "get", "put", "post", "delete");
        }

        $return = array();

        if(in_array("get", $methods)) {
            array_push($return, array(
                $resName."/:id", $mapUrl."/read", "", "get", "json"
            ));
        }
        if(in_array("list", $methods)) {
            array_push($return, array(
                $resName, $mapUrl."/index", "", "get", "json"
            ));
        }
        if(in_array("put", $methods)) {
            array_push($return, array(
                $resName."/:id", $mapUrl."/update", "", "put", "json"
            ));
        }
        if(in_array("post", $methods)) {
            array_push($return, array(
                $resName, $mapUrl."/insert", "", "post", "json"
            ));
        }
        if(in_array("delete", $methods)) {
            array_push($return, array(
                $resName."/:id", $mapUrl."/delete", "", "delete", "json"
            ));
        }

        return $return;

    }
}
$needMake = array(
    array("passport/userLogin", "Passport/Login", "post",),
    array("passport/userLogout", "Passport/Login", "list",),
    array("user", "Passport/User",),
    array("types", "HOME/Types",),
    array("config", "HOME/Config"),
);

$urlRoutes = array();
foreach($needMake as $v) {
    $act = $v[2] ? explode(",", $v[2]) : null;
    $urlRoutes = array_merge($urlRoutes, routeMaker($v[0], $v[1], $act));
}


//自动规则
$groupMap = array(
    "jxc" => "JXC",
    "home" => "HOME",
    "crm" => "CRM"
);

$tmp = explode("/", $_GET["s"]);
//print_r($tmp);exit;
//有ID参数的情况
if(count($tmp) >= 4) {
    list($null, $group, $module, $id) = $tmp;
    list($id, $ext) = explode(".", $id);
} else {
    list($null, $group, $app) = $tmp;
    list($module, $ext) = explode(".", $app);
}

$group = $groupMap[$group] ? $groupMap[$group] : ucfirst($group);
if($ext == "json") {
    $k = sprintf('%s/%s', strtolower($group), $module);
    $action = sprintf('%s/%s', $group, ucfirst($module));
    $hasRule = false;
    foreach($needMake as $route) {
        if($route[0] == $k) {
            $hasRule = true;
            break;
        }
    }
    if(!$hasRule) {
        $urlRoutes = array_merge($urlRoutes, routeMaker($k, $action));
    }


    $_GET["g"] = $group;
    $_GET["m"] = $module ? $module : "index";
    $_GET["id"] = $id;
    define("CURRENT_APP", strtolower($group));
}

unset($null, $tmp, $groupMap, $k, $action, $hasRule, $ext);

return $urlRoutes;