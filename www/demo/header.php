

<script src="https://live.knytify.com/tag/main.js"></script>
<script>

function getScore(session_id) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.responseType = 'json';
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
            console.log("Prediction: ", xmlHttp.response.result)
        }
    }
    xmlHttp.open("GET", "/proxy/knight.php?sid=" + encodeURIComponent(session_id), true);
    xmlHttp.send(null);
}

window.knytify.init({
    on_ready: function (session_id) {
        console.log("Session ID: ", session_id)

        // You can now store or tansfer to the backend the session id for later use,
        // or, in this example, retrieve the score from the public page through a proxy.
        getScore(session_id)
    }
})
</script>