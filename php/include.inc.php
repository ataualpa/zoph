<?php
/**
 * Include necessary files
 *
 * This file is part of Zoph.
 *
 * Zoph is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * Zoph is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Zoph; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 * @package Zoph
 * @author Jason Geiger
 * @author Jeroen Roos
 */

/**
 * Autoload classes
 */

function zophAutoload($file) {  
    if(is_readable(settings::$php_loc . "/" . $file)) {
        require_once $file;
    } else {
        return false;
    }

}

function zophAutoloadClass($class) {
    $file="classes/" . $class . ".inc.php";
    return zophAutoload($file);
}

function zophAutoloadInterface($interface) {
    $file="interfaces/" . $interface . ".inc.php";
    return zophAutoload($file);
}

spl_autoload_register("zophAutoloadClass");
spl_autoload_register("zophAutoloadInterface");

require_once "exception.inc.php";
require_once "variables.inc.php";
require_once "log.inc.php";

require_once "config.inc.php";
require_once "settings.inc.php";
require_once "requirements.inc.php";
require_once "util.inc.php";

require_once "validator.inc.php";

require_once "track.inc.php";
require_once "point.inc.php";


require_once "group_permissions.inc.php";
require_once "color_scheme.inc.php";
require_once "prefs.inc.php";
require_once "user.inc.php";
require_once "group.inc.php";

require_once "database.inc.php";

if(!defined("LOGON")) {
    if(!defined("TEST")) {
        require_once "auth.inc.php";
    }

    require_once "code.inc.php";
    require_once "comment.inc.php";

    require_once "page.inc.php";
    require_once "pageset.inc.php";

    require_once "file.inc.php";

    require_once "photo.inc.php";
    require_once "saved_search.inc.php";
    require_once "photo_search.inc.php";

    require_once "import.inc.php";
    if(defined("CLI") || defined("TEST")) {
        require_once "cli/cli.inc.php";
        require_once "cli/arguments.inc.php";
        require_once "cli/cliimport.inc.php";
    } else {
        require_once "webimport.inc.php";
    }
}
?>
