<?php
/*
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
 */
if ($num_photos) {
    $title_bar = sprintf(translate("photo %s of %s"),  ($offset + 1) , $num_photos);
} else {
    $title_bar = translate("photo");
}
?>
  <h1>
<?php
  echo create_actionlinks($actionlinks);
  echo $title_bar;
?>
  </h1>
<?php
  echo check_js($user);
  require_once "selection.inc.php";
?>
      
<div class="main">
<form action="photo.php" method="POST">
<input type="hidden" name="_action" value="<?php echo $action ?>">
<input type="hidden" name="_qs" value="<?php echo $return_qs ?>">
<?php
if ($action == "insert") {
    unset($actionlinks["email"]);
    unset($actionlinks["edit"]);
    unset($actionlinks["add comment"]);
    unset($actionlinks["select"]);
    unset($actionlinks["delete"]);
    ?>
      <label for="filename"><?php echo translate("file name") ?></label>
      <?php echo create_text_input("name", $photo->get("name"), 40, 64) ?>
      <span class="inputhint"><?php echo sprintf(translate("%s chars max"), "64") ?></span><br>
    <?php
} else {
    ?>
    <input type="hidden" name="photo_id" value="<?php echo $photo->get("photo_id") ?>">
    <?php
    if (conf::get("rotate.enable") && ($user->is_admin() || $permissions->get("writable"))) {
        ?>
        <div class="rotate">
        <?php echo translate("rotate", 0) ?>

            <select name="_deg">
                <option>&nbsp;</option>
                <option>90</option>
                <option>180</option>
                <option>270</option>
            </select>

            <br>
        <?php echo translate("recreate thumbnails", 0) ?>

            <input type="radio" name="_thumbnail" value="1">
        <?php echo translate("yes") ?>

            <input type="radio" name="_thumbnail" value="0" checked>
        <?php echo translate("no") ?>
        </div>
        <?php
    }
    ?>

        <div class="prev"><?php echo $prev_link ? "[ $prev_link ]" : "&nbsp;" ?></div>
        <div class="photohdr">
    <?php echo $photo->getFullsizeLink($photo->get("name")) ?> :
    <?php echo $photo->get("width") ?> x <?php echo $photo->get("height") ?>,
    <?php echo $photo->get("size") ?> <?php echo translate("bytes") ?>
        </div>
        <div class="next"><?php echo $next_link ? "[ $next_link ]" : "&nbsp;" ?></div>

    <ul class="tabs">
    <?php
    if(conf::get("share.enable") && ($user->is_admin() || $user->get("allow_share"))) {
        $hash=$photo->getHash();
        $full_hash=sha1(conf::get("share.salt.full") . $hash);
        $mid_hash=sha1(conf::get("share.salt.mid") . $hash);
        $full_link=getZophURL() . "image.php?hash=" . $full_hash;
        $mid_link=getZophURL() . "image.php?hash=" . $mid_hash;

        $tpl_share=new template("photo_share", array(
            "hash" => $hash,
            "full_link" => $full_link,
            "mid_link" => $mid_link
        ));
        echo $tpl_share;
    }
    ?>
    </ul>

    <?php echo $photo->getFullsizeLink($photo->getImageTag(MID_PREFIX)) ?>
    <?php
}
?>
<input class="updatebutton" type="submit" value="<?php echo translate($action, 0) ?>">
<label for="title"><?php echo translate("title") ?></label>
<?php echo create_text_input("title", $photo->get("title"), 40, 64) ?>
<span class="inputhint"><?php echo sprintf(translate("%s chars max"), "64") ?></span><br>
<label for="_location_id"><?php echo translate("location") ?></label>
<?php echo place::createPulldown("location_id", $photo->get("location_id")); ?>
<br>
<fieldset class="map">
    <legend><?php echo translate("map") ?></legend>
    <label for="lat"><?php echo translate("latitude") ?></label>
    <?php echo create_text_input("lat", $photo->get("lat"), 10, 10) ?><br>
    <label for="lat"><?php echo translate("longitude") ?></label>
    <?php echo create_text_input("lon", $photo->get("lon"), 10, 10) ?><br>
    <label for="mapzoom"><?php echo translate("zoom level") ?></label>
    <?php echo place::createZoomPulldown($photo->get("mapzoom")) ?><br>
</fieldset>
<label for="date"><?php echo translate("date") ?></label>
<?php echo create_text_input("date", $photo->get("date"), 12, 10, "date") ?>
<span class="inputhint">YYYY-MM-DD</span><br>
<label for="time"><?php echo translate("time") ?></label>
<?php echo create_text_input("time", $photo->get("time"), 10, 8, "time") ?>
<span class="inputhint">HH:MM:SS</span><br>
<label for="time_corr"><?php echo translate("time correction") ?></label>
<?php echo create_text_input("time_corr", $photo->get("time_corr"), 10, 8) ?>
<span class="inputhint"><?php echo translate("in minutes") ?></span><br>
<label for="view"><?php echo translate("view") ?></label>
<?php echo create_text_input("view", $photo->get("view"), 40, 64) ?>
<span class="inputhint"><?php echo sprintf(translate("%s chars max"), "64") ?></span><br>
<label for="_photographer_id"><?php echo translate("photographer") ?></label>
<?php 
echo photographer::createPulldown("photographer_id", $photo->get("photographer_id"));
?>
<br>
<?php
if ($user->is_admin()) {
    ?>
    <label for="level"><?php echo translate("level") ?></label>
    <?php echo create_text_input("level", $photo->get("level"), 4, 2) ?>
    <span class="inputhint">1 - 10</span><br>
    <?php
}
?>
<label><?php echo translate("description") ?></label>
<textarea name="description" cols="60" rows="4">
  <?php echo $photo->get("description") ?>
</textarea><br>
<?php
if ($action != "insert") {
    ?>
    <label for="person_id[0]"><?php echo translate("people") ?><br>
        <span class="inputhint"><?php echo translate("(left to right, front to back).") ?></span>
    </label>
    <fieldset class="multiple">
    <?php
    $people = $photo->getPeople();
    if ($people) {
        foreach ($people as $person) {
            ?>
            <input class="remove" type="checkbox" name="_remove_person_id[]" 
                value="<?php echo $person->get("person_id")?>">
            <?php
            echo $person->getLink() . "<br>\n";
        }
    } else {
        ?>
        <?php echo translate("No people have been added to this photo.") ?><br>
        <?php
    }
    echo person::createPulldown("_person_id[0]");
    ?>
    </fieldset>
    <label for="albums"><?php echo translate("albums") ?></label>
    <fieldset class="albums multiple">
    <?php
    $albums = $photo->getAlbums($user);
    if ($albums) {
        foreach ($albums as $album) {
            ?>
            <input type="checkbox" name="_remove_album_id[]" 
                value="<?php echo $album->get("album_id")?>">
            <?php echo $album->getLink() ?><br>
            <?php
        }
    } else {
        echo translate("This photo is not in any albums.");
        echo "<br>\n";
    }
    echo album::createPulldown("_album_id[0]");
    ?>
    </fieldset>
    <label for="categories"><?php echo translate("categories") ?></label>
    <fieldset class="categories multiple">
    <?php
    $categories = $photo->getCategories($user);
    if ($categories) {
        foreach ($categories as $category) {
            ?>
            <input type="checkbox" name="_remove_category_id[]" 
                value="<?php echo $category->get("category_id")?>">
            <?php echo $category->getLink() ?><br>
            <?php
        }
    } else {
        ?>
        <?php echo translate("This photo is not in any categories.") ?><br>
        <?php
    }
    echo category::createPulldown("_category_id[0]", "");
    ?>
    </fieldset>
    <br>
    <?php
    $_show = getvar("_show");
    if ($_show) {
        ?>
        <hr>
        <label for="path"><?php echo translate("path") ?></label>
        <?php echo create_text_input("path", $photo->get("path"), 40, 64) ?>
        <span class="inputhint"><?php echo sprintf(translate("%s chars max"), "64") ?></span><br>
        <label for="width"><?php echo translate("width") ?></label>
        <?php echo create_text_input("width", $photo->get("width"), 6, 6) ?><br>
        <label for="height"><?php echo translate("height") ?></label>
        <?php echo create_text_input("height", $photo->get("height"), 6, 6) ?><br>
        <label for="camera_make"><?php echo translate("camera make") ?></label>
        <?php echo create_text_input("camera_make", $photo->get("camera_make"), 32, 32) ?><br>
        <label for="camera_model"><?php echo translate("camera model") ?></label>
        <?php echo create_text_input("camera_model", $photo->get("camera_model"), 32, 32) ?><br>
        <label for="flash_used"><?php echo translate("flash used") ?></label>
        <?php echo template::createPulldown("flash_used", $photo->get("flash_used"), 
            array("" => "", "Y" => translate("Yes",0), "N" => translate("No",0))) ?><br>
        <label for="focal_length"><?php echo translate("focal length") ?></label>
        <?php echo create_text_input("focal_length", $photo->get("focal_length"), 10, 64) ?><br>
        <label for="exposure"><?php echo translate("exposure") ?></label>
        <?php echo create_text_input("exposure", $photo->get("exposure"), 32, 64) ?><br>
        <label for="aperture"><?php echo translate("aperture") ?></label>
        <?php echo create_text_input("aperture", $photo->get("aperture"), 8, 16) ?><br>
        <label for="compression"><?php echo translate("compression") ?></label>
        <?php echo create_text_input("compression", $photo->get("compression"), 32, 64) ?><br>
        <label for="iso_equiv"><?php echo translate("iso equiv") ?></label>
        <?php echo create_text_input("iso_equiv", $photo->get("iso_equiv"), 8, 8) ?><br>
        <label for="metering_mode"><?php echo translate("metering mode") ?></label>
        <?php echo create_text_input("metering_mode", $photo->get("metering_mode"), 16, 16) ?><br>
        <label for="focus_distance"><?php echo translate("focus distance") ?></label>
        <?php echo create_text_input("focus_dist", $photo->get("focus_dist"), 16, 16) ?><br>
        <label for="ccd_width"><?php echo translate("ccd width") ?></label>
        <?php echo create_text_input("ccd_width", $photo->get("ccd_width"), 16, 16) ?><br>
        <label for="comment"><?php echo translate("comment") ?></label>
        <?php echo create_text_input("comment", $photo->get("comment"), 40, 128) ?></br>
        <?php
    } // additional atts
    if (!$_show) {
        ?>
        <a href="photo.php?_action=edit&amp;photo_id=<?php 
            echo $photo->get("photo_id") ?>&amp;_show=all">
          <?php echo translate("show additional attributes") ?>
        </a>
        <?php
    }
    ?>
    <br>
    <input type="submit" value="<?php echo translate($action, 0) ?>">
    <?php
}
?>
</form>

