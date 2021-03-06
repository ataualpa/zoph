// This file is part of Zoph.
//
// Zoph is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Zoph is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with Zoph; if not, write to the Free Software
// Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

/**
 * @todo Really should get rid of this global var...
 */

var mapstraction;

var zMaps=function() {
    function createMap(div, provider) {
        // This creates a new map
        mapstraction=new mxn.Mapstraction(div, provider);
        mapstraction.addControls({ pan: true, zoom: 'large', scale: true, map_type: true });

        var center=new mxn.LatLonPoint(0,0);
        if(provider=="openlayers") {
            var osm=mapstraction.getMap();
            osm.baseLayer.attribution="<a href='http://www.openstreetmap.org/copyright'>&copy; OpenStreetMap</a>";
            osm.addControl(new OpenLayers.Control.Attribution());
        }
        mapstraction.setCenterAndZoom(center, 2);
        zMapsCustom.customMap(mapstraction);
    }

    function clickMap(event_name, event_source, event_args) {
        var latfield=document.getElementById('lat');
        var lonfield=document.getElementById('lon');
        var zoomfield=document.getElementById('mapzoom');
        var maptypefield=document.getElementById('maptype');

        latfield.value=event_args.location.lat;
        lonfield.value=event_args.location.lon;
        if(zoomfield) {
            zoomfield.value=mapstraction.getZoom();
        }
        mapstraction.removeMarker(mapstraction.markers[0]);
        marker = new mxn.Marker(event_args.location);
        mapstraction.addMarker(marker);
    }

    function zoomUpdate(event_name, event_source, event_args) {
        var zoomfield=document.getElementById('mapzoom');
        if(zoomfield) {
            zoomfield.value=mapstraction.getZoom();
        }
    }

    function createMarker(lat, lon, icon, title, infoBubble) {
        var point=new mxn.LatLonPoint(lat, lon);
        var marker=new mxn.Marker(point);
        if (title) {
            marker.setLabel(title);
        }
        if(icon) {
            marker.setIcon(icon, [22,22]);
        }
        if (infoBubble) {
            marker.setInfoBubble(infoBubble);
        }
        mapstraction.addMarker(marker);
    }

    function setFieldUpdate() {
        // This makes sure that the map is updated when a user changes the
        // Lat and Lon fields manually.
        var latfield=document.getElementById("lat");
        var lonfield=document.getElementById("lon");
        var zoomfield=document.getElementById("mapzoom");

        latfield.onchange=zMaps.updateMap;
        lonfield.onchange=zMaps.updateMap;
        if(zoomfield) {
            zoomfield.onchange=zMaps.updateMap;
        }
    }

    function updateMap() {
        var latfield=document.getElementById("lat");
        var lonfield=document.getElementById("lon");
        var zoomfield=document.getElementById("mapzoom");
        var distance=document.getElementById("latlon_distance");
        var lat=latfield.value;
        var lon=lonfield.value;
        var zoomlevel=mapstraction.getZoom();
        
        if(zoomfield) {
           zoomlevel=parseInt(zoomfield.value);
        }

        mapstraction.removeMarker(mapstraction.markers[0]);
        createMarker(lat, lon,null,null,null);
        mapstraction.setCenterAndZoom(new mxn.LatLonPoint(lat,lon),zoomlevel);
    }

    function setUpdateHandlers() {
        mapstraction.click.addHandler(zMaps.clickMap);
        mapstraction.changeZoom.addHandler(zMaps.zoomUpdate);
        mapstraction.endPan.addHandler(zMaps.zoomUpdate);
        setFieldUpdate();
    }

    return {
        createMap:createMap,
        clickMap:clickMap,
        zoomUpdate:zoomUpdate,
        createMarker:createMarker,
        updateMap:updateMap,
        setUpdateHandlers:setUpdateHandlers
    };
}();
