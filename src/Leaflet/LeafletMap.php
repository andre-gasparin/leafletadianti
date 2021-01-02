<?php
namespace AndreGasparin\Plugins\Leaflet;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Base\TStyle;

/**
 * Accordion Container
 */
class LeafletMap extends TElement
{
    protected $elements, $javascript;

    private $height = '500px'; 
    private $width = '500px';
    private $return;

    private $lat = 51.505; 
    private $lng = -0.09;
    private $z   = 13;

    private $locations_to_center = array();


    public function __construct($lat, $lng, $z, $tile = 'google')
    {
        parent::__construct('div');
        
        TStyle::importFromFile('vendor/andregasparin/plugins/src/Leaflet/leaflet.css');

        $this->id = 'mapall' . uniqid();

        if(!empty($lat))
            $this->lat = $lat;
        if(!empty($lng))
            $this->lng = $lng;
        if(!empty($z))
            $this->z   = $z;
        if(!empty($tile))
            $this->tileLayer($tile);
    }
    public function addJsonMarker($json)
    {
        $points = json_decode($json);
        foreach($points as $point)
        {
             $description = '';

             if(!empty($point->lng)) $lng = $point->lng;
             if(!empty($point->longitude)) $lng = $point->longitude;

             if(!empty($point->lat)) $lat = $point->lat;
             if(!empty($point->latitude)) $lat = $point->latitude;

             if(!empty($point->description)) $description = $point->description;

             if(!empty($lat) && !empty($lng) ) 
                 $this->addMarker($lat, $lng, $description);
            }
    }

    public function setSize($width, $height)
    {
        $this->width = (is_numeric($width)) ? $width.'px' : $width;
        $this->height = (is_numeric($height)) ? $height.'px' : $height;
    }

    public function __set($atrib, $value)
    {
        $this->$atrib = $value;
    }
    
    public function addMarker($lat, $lng, $poupup, $icon = null)
    {
        if(!empty($lat) && !empty($lng))
        {
           
            $poupup = (!empty($poupup)) ? '.bindPopup("'.$poupup.'")' : '';
            $icon =   (empty($icon))    ?  'vendor/andregasparin/plugins/src/Leaflet/marker-icon.png' : $icon ;

            $this->locations_to_center['point'][] = ['lat'=>$lat, 'lng'=>$lng];
            $this->javascript .= ' L.marker(['.$lat.', '.$lng.'], {icon:  new LeafIcon({iconUrl: \''.$icon.'\'})})'.$poupup.'.addTo(map); ';
        }
    }

    public function center()
    {
        if(!empty($this->locations_to_center['point']))
        {
            $points_to_center = '';
            foreach( $this->locations_to_center['point'] as $point)
                $points_to_center .= '['.$point['lat'].', '.$point['lng'].'],';
    
            $this->javascript .= ' map.fitBounds(['.$points_to_center.']); ';
        }
    }

    public function myLocation($show_precision = false)
    {
        $poupup = ($show_precision == true) ? ".bindPopup('Precisão: ' + radius + '  metros' ).openPopup()" : '';
        $this->javascript .= "
        map.on('locationfound', function(e){
            var radius = e.accuracy / 2;
            L.marker(e.latlng, {icon:  Licon}).addTo(map)".$poupup.";
            L.circle(e.latlng, radius).addTo(map);
        });
        map.locate({setView: true, maxZoom: 16});
        ";
    }

    public function tileLayer($tile = 'google')
    {
        if($tile == 'google')
            $this->javascript .= "L.tileLayer('https://www.google.com/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {  attribution: 'André | Google' }).addTo(map);";
        elseif($tile == 'osm')
            $this->javascript .= "L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; André | OSM' }).addTo(map);";
    }

    public function enableAddPoints($return)
    {
        $this->return = $return;
        $this->javascript .= "
        map.on('click', function (e) {
            var marker = L.marker(e.latlng,{icon:  Licon, draggable:'true'}).addTo(Group".$this->id."); 
            allPointsJson();
            message('Inserido','Alfinete inserido!', 'success');
        });
        ";
    }

    public function enableAddOnePoint($return)
    {
        $this->return = $return;
        $this->javascript .= "
            var point".$this->id.";
            map.on('click', function (e) {               
                if(point".$this->id.")
                    map.removeLayer(point".$this->id.");
                
                point".$this->id." = L.marker(e.latlng,{icon:  Licon, draggable:'true'}).addTo(Group".$this->id.");
                allPointsJson();
                message('Inserido','Alfinete inserido!', 'success');
            });
            ";
    }
    
    public function createMap()
    {
        $javascript = (!empty($this->javascript)) ? $this->javascript : '';
        TScript::create("
        var map = '';
            $(function() {  
                 var map = L.map('".$this->id."').setView([".$this->lat.", ".$this->lng."], ".$this->z."); 
                var LeafIcon = L.Icon.extend({
                    options: { iconSize: [25, 40], iconAnchor: [9, 40], popupAnchor: [4, -37] }
                });  
                var Group".$this->id." = L.featureGroup().addTo(map).on('click', groupClick);
                var Licon = new LeafIcon({iconUrl: 'vendor/andregasparin/plugins/src/Leaflet/marker-icon.png'});
                
                ".$javascript."    

                function allPointsJson()
                { 
                    JsonGeom = '';
                    map.eachLayer((layer) => {
                        if(layer instanceof L.Marker){
                            JsonGeom += JSON.stringify(layer.getLatLng())+', ';  
                        }
                    });
                    $('[name=\"".$this->return."\"]').val('['+JsonGeom+']');
                }
                function groupClick(event) {
                    event.layer.remove();
                    allPointsJson();
                }
                function message(title, message, type){
                   if(type == 'success')
                        iziToast.success({
                            title: title,
                            message: message,
                            position: 'topRight',
                            timeout: 3000
                        });
                }
            });
        ");
    }


    public function show()
    {
        $style = new TElement('style');
        $style->add('#'.$this->id.'{ height:'.$this->height.';  width: '.$this->width.'; }');
        
        $this->createMap();

        $script = new TElement('script');
        $script->type = 'text/javascript';
        $script->src  = 'vendor/andregasparin/plugins/src/Leaflet/leaflet.js';

        $content = new TElement('div');
        $content->id = $this->id;
        $content->class = 'leaflet';
         
        parent::add( $style );
        parent::add( $script );
        parent::show();
        
        return  $content;
    }

}
