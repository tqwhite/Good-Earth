<!DOCTYPE html>
<html>
<head>
  <title>gmaps.js &mdash; the easiest way to use Google Maps</title>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
  <script type="text/javascript" src="https://raw.github.com/HPNeo/gmaps/master/gmaps.js"></script>
  <script type="text/javascript" src="http://hpneo.github.com/gmaps/prettify/prettify.js"></script>
  <link href='http://fonts.googleapis.com/css?family=Convergence|Bitter|Droid+Sans|Ubuntu+Mono' rel='stylesheet' type='text/css' />
  <link href='http://hpneo.github.com/gmaps/styles.css' rel='stylesheet' type='text/css' />
  <link href='http://hpneo.github.com/gmaps/prettify/prettify.css' rel='stylesheet' type='text/css' />
  <link rel="stylesheet" type="text/css" href="http://hpneo.github.com/gmaps/examples/examples.css" />
  <script type="text/javascript">
    var map;
    $(document).ready(function(){
      prettyPrint();
      map = new GMaps({
        div: '#map',
        lat: -12.043333,
        lng: -77.028333
      });

      GMaps.geolocate({
        success: function(position){
          map.setCenter(position.coords.latitude, position.coords.longitude);
        },
        error: function(error){
          alert('Geolocation failed: '+error.message);
        },
        not_supported: function(){
          alert("Your browser does not support geolocation");
        },
        always: function(){
          alert("Done!");
        }
      });
    });
  </script>
</head>
<body>
  <div id="header">
    <h1>
      <a href="http://hpneo.github.com/gmaps">gmaps.js</a>
    </h1>
    <h2>Google Maps API with less pain and more fun</h2>
  </div>
  <div id="body">
    <h3>Geolocation</h3>
    <div class="row">
      <div class="span11">
        <div class="popin">
          <div id="map"></div>
        </div>
      </div>
      <div class="span6">
        <p>GMaps.js supports HTML5 Geolocation:</p>
        <pre class="prettyprint">GMaps.geolocate({
  success: function(position) {
    map.setCenter(position.coords.latitude, position.coords.longitude);
  },
  error: function(error) {
    alert('Geolocation failed: '+error.message);
  },
  not_supported: function() {
    alert("Your browser does not support geolocation");
  },
  always: function() {
    alert("Done!");
  }
});</pre>
        <p><code>GMaps.geolocate</code> supports 4 functions:
        <ul>
          <li><code>success</code> (required): fires when geolocation has been successful</li>
          <li><code>error</code> (required): fires when geolocation has not been done</li>
          <li><code>not_supported</code> (required): fires when geolocation is not supported by the browser</li>
          <li><code>always</code> (optional): fires always after every scenario described above.</li>
        </ul></p>
      </div>
    </div>
    </div>
</body>
</html>