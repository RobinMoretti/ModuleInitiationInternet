<!DOCTYPE html>
<html>
  <head>

    <meta charset="UTF-8">
     <link rel="stylesheet" href="normalize.css">
     <link rel="stylesheet" href="skeleton.css">
     <link rel="stylesheet" href="style.css">
  </head>
  
  <body class="container">

  <div class="row">
  <img src="server1.jpg" class="six columns" alt="">
  <img src="server2.jpg" class="six columns" alt="">
  </div>

  <div class="row center">
    <a class="button" href="9.php">Suivant</a>
  </div>


  <div id="data" class="hide">
    <h2>DATA:</h2>
    <div id="id"></div>
  </div>

  <p id="dump" class="hide"></p>
  
	<button name="dump" type="button" onmousedown="dumpData()" class="hide">
		Dump
	</button>
	
  </body>

  <script>

    var dataElem;
    var interval;
    window.onload = function()
    {
      dumpData();


      dataElem = document.getElementById("data");

      //get all informtion from the user
      getJSONP('//freegeoip.net/json/?callback=?', function(data){
        for(elem in data){
            var div = document.createElement("div"); 
            div.id = elem;
            div.textContent = data[elem];
            dataElem.appendChild(div);
        }
      });

      // corrige un bug
      interval = setInterval(function(){
        console.log('wait for dumping')
        try {
          if(document.getElementById("dump").textContent != "" && document.getElementById("ip").innerHTML != ""){
            clearInterval(interval);
            setUser();
            document.getElementById("adresseIp").textContent = document.getElementById("ip").innerHTML;
          }
        } catch(e) {
          // console.log(e);
        } 

      },100);
    }


    // function ===============================
    function getJSONP(url, success) 
    {
        var ud = '_' + +new Date,
            script = document.createElement('script'),
            head = document.getElementsByTagName('head')[0] 
                   || document.documentElement;

        window[ud] = function(data) {
            head.removeChild(script);
            success && success(data);
        };

        script.src = url.replace('callback=?', 'callback=' + ud);
        head.appendChild(script);
    }

    function setUser()
    {
      if (isset(document.getElementById("ip"))) 
      {
         var getDumpData = getJsonDumpData();

         //check if ip exist even create new user
         var data = getDumpData["data"];
         var userId;
         var i; 
         var exist = false;

         for(i = 0, length1 = data.length; i < length1; i++){
           // console.log('data[i].ip = '+data[i].ip);
           // console.log('ip = '+document.getElementById("ip").textContent);
           if(document.getElementById("ip").textContent == data[i].ip){
            exist = true;
            break;
           }
         }
         console.log(exist);
         if(exist){
          document.getElementById("id").textContent = data[i].id;
         }
         else {
          document.getElementById("id").textContent = i + 1;
          createUser();
          dumpData();
         }

      }
    };

    function isset(elem)
    {
      if (typeof elem !== 'undefined') 
        return true;
      else return false;
    }

    function getJsonDumpData()
    {
      return JSON.parse(document.getElementById("dump").textContent);
    }



    // function AJAX  ===============================
    function dumpData() {
      var xmlHttp = null;

      xmlHttp = new XMLHttpRequest();
      xmlHttp.open("GET",'dataCollege.php?dump=true', true);
      xmlHttp.setRequestHeader("Content-type", "application/json"); // json header
      xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT"); // IE Cache Hack
      xmlHttp.setRequestHeader("Cache-Control", "no-cache"); // idem
      xmlHttp.send();

      xmlHttp.onreadystatechange=function() {
        if(xmlHttp.readyState == 4){
          var json = null;

          try {
            json = JSON.parse(xmlHttp.responseText);
          } catch (err) {
            console.log("error json parse "+ err);
            console.log(xmlHttp.responseText);
            document.getElementById("dump").innerHTML = "JSON parse Error";
            
            return;
          }

          // console.log(json);
          
          if (json.error == "ok") {
            // console.log("ok");
            document.getElementById("dump").innerHTML = xmlHttp.responseText;
          
          } else {
            console.log("bad");
            document.getElementById("dump").innerHTML = "Error";
          
          }
        }
      }
    }

    function sendToReponse(text) {
      if(text == ""){
        break;
      }
      // recuperation des donnés de la page html
      var id = document.getElementById('id').textContent;
      var reponse = " " +  text;

      var dataTemp = getJsonDumpData();

      if(dataTemp["data"][parseInt(id)-1].reponse != null){
        reponse = dataTemp["data"][parseInt(id)-1].reponse + reponse;
      }


      var JSONMarker = {
        id:id,
        reponse:reponse
      };

      var JSONString = JSON.stringify(JSONMarker)

      console.log("Sending : " + JSONString)

      var xmlHttp = null

      xmlHttp = new XMLHttpRequest()
      xmlHttp.open("POST",'dataCollege.php?reponse=true', true)
      xmlHttp.setRequestHeader("Content-type", "application/json") // json header
      xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT") // IE Cache Hack
      xmlHttp.setRequestHeader("Cache-Control", "no-cache") // idem
      xmlHttp.send(JSONString)

      xmlHttp.onreadystatechange=function() {
        if(xmlHttp.readyState == 4){
          var json = null
          try {
            json = JSON.parse(xmlHttp.responseText)
          } catch (err) {
            console.log("error json parse "+ err)
            console.log(xmlHttp.responseText)
            return
          }

          console.log(json)
          
          if (json.error == "ok") {
            console.log("ok")
            
          } else {
            console.log("bad = " + json["data"])
          }
        }
      }
      setTimeout(dumpData, 200);
    }

    function createUser() {
      // recuperation des donnés de la page html
      var id = document.getElementById('id').textContent;
      var click = 0;
      var ip = document.getElementById('ip').textContent;

      var JSONMarker = {
        id:id,
        click:click,
        ip:ip
        //soundVolume:soundVolume,
      };

      var JSONString = JSON.stringify(JSONMarker)

      //console.log("Sending : " + JSONString)

      var xmlHttp = null

      xmlHttp = new XMLHttpRequest()
      xmlHttp.open("POST",'dataCollege.php?feed=true', true)
      xmlHttp.setRequestHeader("Content-type", "application/json") // json header
      xmlHttp.setRequestHeader("If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT") // IE Cache Hack
      xmlHttp.setRequestHeader("Cache-Control", "no-cache") // idem
      xmlHttp.send(JSONString)

      xmlHttp.onreadystatechange=function() {
        if(xmlHttp.readyState == 4){
          var json = null
          try {
            json = JSON.parse(xmlHttp.responseText)
          } catch (err) {
            console.log("error json parse "+ err)
            console.log(xmlHttp.responseText)
            return
          }

          console.log(json)
          
          if (json.error == "ok") {
            console.log("ok")
            
          } else {
            console.log("bad = " + json["data"])
          }
        }
      }
    }
  </script>
</html>