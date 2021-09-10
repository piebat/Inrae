
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge, chrome=1" />
    <title>INRAE demo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="./css/leaflet.contextmenu.css"/>
<link rel="stylesheet" href="./css/main.css">

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-tilelayer-geojson/1.0.2/TileLayer.GeoJSON.min.js" integrity="sha512-R3sWhPfrUa7FVpXuNnv8+6xyG+/Lmv6UZb9x81qOHddiO4JDhQmzxvIhflBnUaA1jnyVnZIP/8NsSLyE+tKh7w==" crossorigin="anonymous"></script>
<script src="./js/leaflet.contextmenu.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="./js/L.KML.js"></script>
<script src="./js/leaflet-distance-marker.js"></script>

</head>
<body>
   <header id="head">
    <?php include "./navbar.html"?>
   </header>
<main>
<div class="container-fluid" style="height: 100%; width: 100%;"> 
    <div class="d-grid gap-2 d-md-block  sticky-top">
        <button id="btn_alerts" type="button" class=" btn btn-success float-end" onclick="resetAlerts()">
            Alerts <span id="nAlerts" class="badge rounded-pill bg-secondary">none</span>
        </button>
        <div id="errmsg" class="bg-danger" ></div>
    </div>
        <div  id="mapid"> Qui dovrebbe esserci la mappa</div>
        <hr>
</div>
<div class="countainer-fluid" style="margin-left: 30px; margin-right: 30px;">
        <div class="row">
            <div class="col-md-3 card">
                <div class="card-body" style="background-color: rgb(233, 239, 239);">
                    <div class="row">
                        <h5>Meteorological sensors <img src="./img/wIcon.png" alt="weather station icon" width="32px"> </h5>
                        <div class="col">
                            <label for="Wbutton"id="WN" > Position</label>
                            <button type="button" class="btn btn-primary btn-bg" id="Wbutton">
                                <span class="small">Shows on Map</span>
                            <!-- <span class="badge rounded-pill bg-success" id="Wll"></span> -->
                            </button>
                        </div>
                        <div class="col">
                            <label for="WT">Tem. (C°)</label>
                            <input class="form-control" type="text" aria-label="Temperature" id="WT" readonly>
                        </div>
                        <div class="col"> 
                            <label for="WH"> Humidity (%)</label>
                            <input class="form-control" type="text" aria-label="Temperature" id="WH" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="WH"> Wind (Km/h)</label>
                            <input class="form-control" type="text" placeholder="Not Def." aria-label="Temperature" id="WH" readonly>
                        </div>
                        <div class="col">
                            <label for="WH"> Rain (mm))</label>
                            <input class="form-control" type="text" placeholder="Not Def." aria-label="Temperature" id="WH" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1 card">
                <div class="card-body">
                    <!-- <h5 class="card-title">Graph</h5> -->
                    <div class="card-text">
                        <div id="chart_div"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 card">
                <div class="card-body">
                    <!-- <h5 class="card-title">Graph1</h5> -->
                    <div class="card-text" id="msgs">
                        <!-- the elemets are added by main.js-->
                    </div>
                </div>
            </div>
            <div class="col-md-4 card">
                <div class="card-body">
                    <!-- <h5 class="card-title">Graph</h5> -->
                    <div class="card-text">
                        <div id="linechart_material"></div>
                            <div class="graphSlider">
                            <input type="range" min="10" max="100" value="10" class="slider" id="myRange">
                        </div>
                    </div>
                </div>
            </div> 
        </div> <!--ROW 1-->  
</div>
</main>
<!-- Off canvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel" data-bs-backdrop="false" data-bs-scroll="true">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasLabel">More Data</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <h5>Last Schedule</h5>
    <div id="timeline" class="offcanvas-body">
      Content for the offcanvas goes here. You can place just about any Bootstrap component or custom elements here.
    </div>
</div>

<div class="modal" tabindex="-1" id="myModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">KAFKA Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="HideMyModal()"></button>
      </div>
      <div class="modal-body">
        <p id="modalMessage"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="HideMyModal()" >Close</button>
        <button type="button" class="btn btn-primary" onclick="location.reload()">Reload Page</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="./main.js"></script>
</body>
</html>