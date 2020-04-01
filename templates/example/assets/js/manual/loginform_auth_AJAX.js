function send(url) {

    var form = document.getElementById("loginForm");
    var request = new XMLHttpRequest();

    request.open("POST", url);

    request.onreadystatechange = function()
    {
        if(this.readyState === 4 && this.status === 200)
        {
            var text = this.responseText;
            alert(text);
        }
    };

    request.send(new FormData(form));
}
