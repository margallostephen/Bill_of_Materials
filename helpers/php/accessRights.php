<?php
function getModuleAccess($module)
{
    if (empty($_SESSION['ACCESS_RIGHTS'][$module])) {
        return [];
    }

    return array_change_key_case($_SESSION['ACCESS_RIGHTS'][$module], CASE_LOWER);
}
