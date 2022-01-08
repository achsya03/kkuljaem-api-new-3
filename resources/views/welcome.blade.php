<!DOCTYPE html>
<html>
<head>
  <link type="text/css" rel="stylesheet" href="/video.js/dist/video-js.min.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>
<div style="background-color:black;opacity:0.5;height:240px;width:1200px;position:absolute;display: block;z-index: 5;">
  
</div>
<video
    id="vid1"
    class="video-js vjs-default-skin"
    controls
    autoplay
    width="640" height="264"
    data-setup='{ "techOrder": ["youtube"], "sources": [{ "type": "video/youtube", "src": "https://www.google.com/watch?aaa=qwertyui&v=ggblIcjpuN4"}]}'
  >
  </video>
  <script src="/video.js/dist/video.min.js"></script>
  <script src="/videojs-youtube/dist/Youtube.min.js"></script>
</body>
</html>