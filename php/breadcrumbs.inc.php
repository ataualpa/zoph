<?php
/**
 * Display and modify breadcrumbs
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

// This is needed because this file is sometimes included from 
// global context and sometimes from class context. 
// This is temporary until Zoph has moved to templating entirely
// or the global $user has been completely phased out.
if(!isset($user)) {
    $user=user::getCurrent();
}
global $_qs;

if ($user->prefs->get("show_breadcrumbs")) {

    $_clear_crumbs = getvar("_clear_crumbs");
    $_crumb = getvar("_crumb");

    // construct the link for clearing the crumbs (the 'x' on the right)
    if($_POST) {
        $clear_url=$_SERVER["PHP_SELF"] . "?" . $_qs;
    } else {
        $clear_url = htmlentities($_SERVER["REQUEST_URI"]);
    }

    if(strpos($clear_url, "clear_crumbs") == 0) {
        if (strpos($clear_url, "?") > 0) {
            $clear_url .= "&amp;";
        } else {
            $clear_url .= "?";
        }

        $clear_url .= "_clear_crumbs=1";
    }

    if ($_clear_crumbs) {
        $user->eat_crumb(0);
    } else if ($_crumb) {
        $user->eat_crumb($_crumb);
    }
    if(!empty($tpl_title)) {
        $title=$tpl_title;
    }
    // only add a crumb if a title was set and if there is either no
    // action or a safe action ("edit", "delete", etc would be unsafe)
    $page=array_reverse(explode("/",$_SERVER['PHP_SELF']));
    $page=$page[0];
    if (!isset($skipcrumb) && isset($title) && count($user->crumbs) < 100 &&
        (empty($_action) || ($_action == "display" || 
        $_action == "search" || $_action == translate("search") ||
        $_action == "notify" || $_action == "compose" || 
        ($user->prefs->get("auto_edit") && $_action != "update" &&
        $_action != "select" && $_action != "deselect" &&
        $_action != "delrate" && $page == "photo.php")))) {
        $user->add_crumb($title, htmlentities($_SERVER["REQUEST_URI"]));
    }

    $max_crumbs=$user->prefs->get("num_breadcrumbs");
    if (!$user->crumbs) {
        $crumb_string = "&nbsp;";
    } else if (($num_crumbs = count($user->crumbs)) > $max_crumbs) {
        $crumb_string = "<li class=\"firstdots\">" .  implode(" <li>",
            array_slice($user->crumbs, $num_crumbs - $max_crumbs));
    } else {
        $crumb_string = "<li class=\"first\">" . implode("<li>", $user->crumbs);
    }
    ?>
    <div class="breadcrumb">
        <span class="actionlink"><a href="<?php echo $clear_url ?>">x</a></span>
        <ul>
            <?php echo $crumb_string . "\n" ?>
        </ul>
    </div>
    <?php
}
?>
