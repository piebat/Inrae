const MapboxToken = "<SET your Yoken>";

google.charts.load('current', {'packages':['gauge','line','controls','corechart','timeline','gantt']});
google.charts.setOnLoadCallback(drawChart);

var speeds=[]
var speed = 0
var cm
var lastPoint
let chart2_span = 10;
let count=0;

let chart
var data
var options
var data2
let chart2
let options2
let chartOk=false;
let alerts = 0;

//mapbox satelite
let sat = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox/satellite-v9',
    tileSize: 512,
    zoomOffset: -1,
    accessToken: MapboxToken
});

//OpenStreetMap
let osm = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
});


let baseMaps = {
    'OpenStreetMap': osm,
    'Satelite': sat
};

let map = L.map('mapid', {
  center: [46.335721,3.428525], // lat/lng in EPSG:4326
  zoom: 17,
  layers: [osm, sat],
  contextmenu: true,
    contextmenuWidth: 140,
      contextmenuItems: [{
        text: 'Show coordinates',
        callback: showCoordinates
      }, {
        text: 'Center map here',
        callback: centerMap
      }, '-', {
        text: 'Zoom in',
        icon: './img/zoom-in.png',
        callback: zoomIn
      }, {
        text: 'Zoom out',
        icon: './img/zoom-out.png',
        callback: zoomOut
  },'-',{
    text: 'Show speed',
    icon: './img/speed.png',
    callback: showData
  }]
});

L.control.layers(baseMaps).addTo(map);
L.control.scale().addTo(map);

let track={};
fetch('data/POM_1_trajectoireWGS.dat')
                .then(res => res.text())
                .then(kmltext => {
                    // Create new kml overlay
                    const parser = new DOMParser();
                    const kml = parser.parseFromString(kmltext, 'text/xml');
                    track = new L.KML(kml);
                    track.setStyle({opacity: 0.5, fillOpacity: 0, color: 'yellow'});
                    map.addLayer(track);
                    // Adjust map to show the kml
                    // const bounds = track.getBounds();
                    // map.fitBounds(bounds);
                });
//polyline used to trach the real-time path
var polyline = L.polyline([], {color: 'red'}).addTo(map);
var coo_number = 0;
if(typeof(EventSource) !== "undefined") {
  var source = new EventSource("ssdemo2.php");

  source.addEventListener("kafka" ,function(event) {
    //var msg = JSON.parse(event.data)
    var msg = event.data.split(" ");
    //var ttString = `id: ${msg.id}, lat: ${msg.lat} lng: ${msg.long}`;
    //lastPoint = L.latLng(msg.lat,msg.long,0);
    lastPoint = L.latLng(msg[2],msg[3],msg[1]);
     polyline.addLatLng(lastPoint);
     coo_number = coo_number + 1;
     parcels[0]['per']= Math.round((coo_number/15458)*100);
     //map.panTo(lastPoint);
     
    //ShowMyModal(ttString)


    speed = parseFloat(msg[4]);
    // console.log(speed);
    speeds.push(speed);
    polyline.bindTooltip('speed: ' + speed).openTooltip(lastPoint);
    // document.getElementById("result").innerHTML = "speed: " + speed;
    if (chartOk) {
        //----------GRAPH
      const sum = speeds.reduce((a, b) => a + b, 0);
      const avg = (sum / speeds.length) || 0;
      data.setValue(0, 1, avg);
      chart.draw(data, options);
      count = count + 1
      // if ((count % 10) == 0) {
      //   options2.hAxis.viewWindow.min = count;
      //   options2.hAxis.viewWindow.max = options2.hAxis.viewWindow.min + 10;
      // }
      if (count > chart2_span ) {
        options2.hAxis.viewWindow.min = count-chart2_span;
        options2.hAxis.viewWindow.max = options2.hAxis.viewWindow.min + chart2_span;
        }
      data2.addRow([count, speed]);
      chart2.draw(data2, google.charts.Line.convertOptions(options2));
      //drawGantt();
      // chart3.draw(data2,options3);
        } // if chartok
  })

  source.addEventListener("kafka_alert" ,function(event) {
    alerts=alerts + 1
    $("#nAlerts").html(alerts);
    $("#btn_alerts").css("background-color","red");
    addMsgs(event.data);
  })

  source.addEventListener("pos" ,function(event) {
    var msg = JSON.parse(event.data)
    lastPoint = L.latLng(msg.lat,msg.lng,msg.alt)
    polyline.addLatLng(lastPoint);
    var ttString = `Speed: ${speed} Km/h`;
    polyline.bindTooltip(ttString).openTooltip(lastPoint);
  })
  source.addEventListener("par",function(event) {
    var msg = JSON.parse(event.data);
    speed = msg["speed"];
    speeds.push(speed);
    // document.getElementById("result").innerHTML = "speed: " + speed;
    if (chartOk) {
        //----------GRAPH
      const sum = speeds.reduce((a, b) => a + b, 0);
      const avg = (sum / speeds.length) || 0;
      data.setValue(0, 1, avg);
      chart.draw(data, options);
      count = count + 1
      // if ((count % 10) == 0) {
      //   options2.hAxis.viewWindow.min = count;
      //   options2.hAxis.viewWindow.max = options2.hAxis.viewWindow.min + 10;
      // }
      if (count > chart2_span ) {
        options2.hAxis.viewWindow.min = count-chart2_span;
        options2.hAxis.viewWindow.max = options2.hAxis.viewWindow.min + chart2_span;
        }
      data2.addRow([count, speed]);
      chart2.draw(data2, google.charts.Line.convertOptions(options2));
      // chart3.draw(data2,options3);
        } // if chartok
    })
    // source.onerror = function() {
    //   ShowMyModal(ErrMessages[0]);
    //   $('#errmsg').html("Event Error");
    // };
    source.onopen = function(){
      HideMyModal();
    }

  var source2 = new EventSource("ssdw.php");
  source2.addEventListener("weather" ,function(event) {
    var wmsg = JSON.parse(event.data)
    if (wmsg) {
      $('#Wll').html(wmsg['lat'] + ", " + wmsg['lng'])
      $('#WT').val(wmsg['T'])
      $('#WH').val(wmsg['H'])
      $('#WN').html(wmsg['name'])
      $('#Wbutton').on( "click", function(){WMarker(wmsg['lat'],wmsg['lng']," <span class='text-center'>Weather Station:<br><h5>"+wmsg['name']+"</h5></span>")})
    }
  });
  source2.onerror = function() {
    //ShowMyModal(ErrMessages[0]);
    //  $('#errmsg').html("Event Error");
  };
  source.onopen = (e) => {
    console.log("The connection has been established.");
  };
  

 } else {
  $("#errmsg").html("Sorry, your browser does not support server-sent events...");
}

  function drawChart() {
    drawGantt();
    // drawTimeline();
    data = google.visualization.arrayToDataTable([
      ['Label', 'Value'],
      ['Speed avg', 0],
      //['Speed', 0],
    //   ['Network', 68]
    ]);

    options = {
      width: 300, height: 100,
      redFrom: 90, redTo: 100,
      yellowFrom:75, yellowTo: 90,
      minorTicks: 5
    };

    options2 = {
            title: 'Robots Odometry',
            curveType: 'function',
            legend: { position: 'right' },
            explorer: { axis: 'horizontal' },
            hAxis: {
              viewWindow:{
                min:0,
                max:10
              }
            }
          };

    chart = new google.visualization.Gauge(document.getElementById('chart_div'));
    chart2 = new google.charts.Line(document.getElementById('linechart_material'));
//line chart
    data2 = new google.visualization.DataTable();
    data2.addColumn('number', 'i-th data');
    data2.addColumn('number', 'speed');
    chartOk=true;      
  } // graphs initialization after load

      function showCoordinates (e) {
	      alert(e.latlng);
      }

      function centerMap (e) {
	      map.panTo(e.latlng);
      }

      function zoomIn (e) {
	      map.zoomIn();
      }

      function zoomOut (e) {
	      map.zoomOut();
      }
      function showData (e) {
          let msg= "Speed @ " + e.latlng + "\n" + Date() + "\n" + speed + " Km/h";
          addMsgs(msg);
      }

      var slider = document.getElementById("myRange");
      // slider.onmouseup = function() {
      //   chart2_span = this.value;
      //   options2.hAxis.viewWindow.min=count;
      //   options2.hAxis.viewWindow.max=count + chart2_span;
      //   data2.removeRows(1,data2.getNumberOfRows()-1);
      //   count=0;
      //   chart2.draw(data2, google.charts.Line.convertOptions(options2));
      // }
      // wehater reports
!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='https://weatherwidget.io/js/widget.min.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','weatherwidget-io-js');


// schedule

function addMsgs(msg) {
  var m = document.getElementById("msgs");
  var ms = document.createElement("div");
  ms.className="shadow p-3 mb-5 bg-body rounded";
  var txt = document.createTextNode(msg);
  ms.appendChild(txt);
  m.appendChild(ms)
}

// markers for weather station
function WMarker(lat, lng, msg) {
  var WIcon = L.icon({
    iconUrl: 'img/wIcon.png',
    iconSize: [32, 32],
    iconAnchor: [22, 94],
    popupAnchor: [-3, -76],
    shadowUrl: '',
    shadowSize: [32, 32],
    shadowAnchor: [22, 94]
});
  var mk = L.marker([lat, lng]);
  mk.setIcon(WIcon);
  mk.bindPopup(msg).openPopup();
  mk.title = msg;
  mk.addTo(map);
  map.panTo(L.latLng(lat,lng));
}

var ErrMessages = [
  "SSE Error. Probably a previous page is open.<br>" +
   "If not, try to restart the python script until it gives <br> <span style='color:green;'> Connection refused </span> . <br> Then Reload this page and after restart the python script"
]

function ShowMyModal(msg) {
  $("#modalMessage").html(msg)
  var myModal = $('#myModal');
  myModal.show();
}

function HideMyModal() {
  var myModal = $('#myModal');
  myModal.hide();
}

//---------------------------------
var parcels = [{name:'Robot 1', lat:46.335719, lng:3.428511, per:40, start: new Date(2021, 5, 2), end: new Date(2021, 5, 3)},
    {name:'Robot 2',lat:46.33476, lng:3.434042, per: 0, start: new Date(2021, 5, 3), end: new Date(2021, 5, 4)},
    {name:'Robot 3',lat:45.7628222, lng:3.1103499, per: 0, start: new Date(2021, 5, 4), end: new Date(2021, 5, 5)}
                ]
function drawGantt() {
  
  data = new google.visualization.DataTable();
  data.addColumn('string', 'Task ID');
  data.addColumn('string', 'Task Name');
  data.addColumn('string', 'Resource');
  data.addColumn('date', 'Start Date');
  data.addColumn('date', 'End Date');
  data.addColumn('number', 'Duration');
  data.addColumn('number', 'Percent Complete');
  data.addColumn('string', 'Dependencies');

  var i=0;
  parcels.forEach(element => {
    i = i+1;
    data.addRow([String(i), element['name'], 'Parcel ' + String(i),
       element['start'], element['end'], null, element['per'], null]);
  });
  

  var optionsG = {
    height: 400,
    gantt: {
      trackHeight: 30
    }
  };

  var chartG = new google.visualization.Gantt(document.getElementById('timeline'));

  google.visualization.events.addListener(chartG, 'select', function(){
    var sel = chartG.getSelection();
    // alert('A Parcel was selected ' + dataTableTL.getFormattedValue(sel[0].row,1)+ ' ' + dataTableTL.getFormattedValue(sel[0].row,2)+ ' ' + dataTableTL.getFormattedValue(sel[0].row,3));
    var r = sel[0].row;
  map.panTo(L.latLng(parcels[r].lat,parcels[r].lng));
  });

  chartG.draw(data, optionsG);
}

function resetAlerts() {
  alerts = 0;
  $("#nAlerts").html("none");
  $("#btn_alerts").css("background-color","green");
  $("#msgs").empty();
}