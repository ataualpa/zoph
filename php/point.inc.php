<?php
/**
 * A point is a GPS position + time, used for Geotagging
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
 * @author Jeroen Roos
 * @package Zoph
 */

/**
 * This class describes a point, which is a GPS position + timestamp
 *
 * @author Jeroen Roos
 * @package Zoph
 */
class point extends zophTable {
    /** @var string The name of the database table */
    protected static $table_name="point";
    /** @var array List of primary keys */
    protected static $primary_keys=array("point_id");
    /** @var array Fields that may not be empty */
    protected static $not_null=array();
    /** @var bool keep keys with insert. In most cases the keys are set by 
                  the db with auto_increment */
    protected static $keepKeys = false;
    /** @var string URL for this class */
    protected static $url;

    /**
     * Create object from XML-snippet
     * @param string snippet of XML-code
     */
    public static function readFromXML($xmldata) {
        $point=new point();
        $xml=new XMLReader();
        $xml->xml($xmldata);
        $xml->read();
        $point->set("lat", $xml->getAttribute("lat"));
        $point->set("lon", $xml->getAttribute("lon"));
        while ($xml->read()) {
            if($xml->nodeType==XMLReader::ELEMENT) {
                switch ($xml->name) {
                case "name":
                    $xml->read();
                    $name=$xml->value;
                    $point->set("name", $name);
                    break;
                case "ele":
                    $xml->read();
                    $point->set("ele", $xml->value);
                    break;
                case "speed":
                    $xml->read();
                    $point->set("speed", $xml->value);
                    break;
                case "time":
                    date_default_timezone_set("UTC");
                    $xml->read();
                    $datetime=strtotime($xml->value);
                    $mysqldate=date("Y-m-d H:i:s", $datetime);
                    $point->set("datetime", $mysqldate);
                    break;
                }
            }
        }
        return $point;
    }

    /**
     * Get the next (in time) point from a track
     */
    public function getNext() {
        $sql="SELECT * FROM " . DB_PREFIX . "point WHERE" .
            " track_id = " . escape_string($this->get("track_id")) . " AND " .
            " datetime>\"" . escape_string($this->get("datetime")) . "\"" .
            " ORDER BY datetime LIMIT 1";
        $points=self::getRecordsFromQuery($sql);
        if(is_array($points) && sizeof($points) > 0) {
            return $points[0];
        } else {
            return null;
        }
    }
    
    /**
     * Get the  previous (in time) point from a track
     */
    public function getPrev() {
        $sql="SELECT * FROM " . DB_PREFIX . "point WHERE" .
            " track_id = " . escape_string($this->get("track_id")) . " AND " .
            " datetime<\"" . escape_string($this->get("datetime")) . "\"" .
            " ORDER BY datetime DESC LIMIT 1";
        $points=self::getRecordsFromQuery($sql);
        if(is_array($points) && sizeof($points) > 0) {
            return $points[0];
        } else {
            return null;
        }
        return $points[0];
    }

    /**
     * Get an array of all points
     *
     * @param array array of contraints
     * @param array "and" or "or"
     * @param array "=", ">" etc.
     * @param string sort order
     *
     * @return array of points
     * @todo useless wrapper around getRecords, should be removed
     */
    public static function getAll($constraints = null, $conj = "and", 
        $ops = null, $order = "name") {
    
        return self::getRecords($order, $constraints, $conj, $ops);
    }

    /**
     * Calculate the distance to another point
     *
     * @param point Point to calculate distance to
     * @param string "km" or "miles"
     * @return int distance
     */
    function getDistanceTo(point $p2, $entity="km") {
        $p1=$this;
        $lat1=$p1->get("lat");
        $lon1=$p1->get("lon");
        $lat2=$p2->get("lat");
        $lon2=$p2->get("lon");

        $distance=(6371 * acos(
            cos(deg2rad($lat1)) *  
            cos(deg2rad($lat2)) * 
            cos(deg2rad($lon2) - deg2rad($lon1)) +
            sin(deg2rad($lat1)) *
            sin(deg2rad($lat2))));

        if($entity=="miles") {
            $distance=$distance / 1.609344;
        }

        return $distance;
    }

    /**
     * Interpolate between points to find out the location on a
     * certain moment
     *
     * This is an approximate calculation
     * and could be very inaccurate if the distance between the
     * points is large, therefore you can give a max distance in km
     *
     * The longer time there is between 2 point the smaller the
     * chance is you actually travelled in a straight line between
     * the points, so you can also give a max time, in seconds.
     * @param point is the point where you are at t1
     * @param point is the point where you are at t2
     * @param int t3 is the time you want to calculate the position for
     * @param int maximum distance to to calculation for
     * @param string entity of distances ("km" or "miles")
     * @param int maximum time between two points
     * @return point this function will return where you are at t3
     */
    public static function interpolate(point $p1, point $p2, 
        $t3, $maxdist=null, $entity="km", $maxtime=null) {
        
        $t1 = strtotime($p1->get("datetime"));
        $t2 = strtotime($p2->get("datetime"));
        
        if (!($t2 >= $t3 && $t3 >= $t1)) {
            return false;
        }
        if($maxtime) {
            if(abs($t1 - $t2) > $maxtime) {
                return false; 
            }
        }

        if($maxdist) {
            $dist=$p1->getDistanceTo($p2, $entity);
            if ($dist > $maxdist) {
                return false;
            }
        }    
        $lat1=$p1->get("lat");
        $lon1=$p1->get("lon");

        $lat2=$p2->get("lat");
        $lon2=$p2->get("lon");

        // Calculate the deltas
        $dlat=$lat2-$lat1;
        $dlon=$lon2-$lon1;
        $dt=$t2-$t1;
        $dt3=$t3-$t1;

        $lat3=$lat1 + (($dlat/$dt) * $dt3);
        $lon3=$lon1 + (($dlon/$dt) * $dt3);
        
        $p3 = new point();
        $p3->set("lat", $lat3);
        $p3->set("lon", $lon3);

        return $p3;
    }
}
?>
