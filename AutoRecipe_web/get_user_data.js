function getUserData() {
    var user_data_div = document.getElementById("user_data");   // Reference the HTML div element where the data will be shown
    let nickname = getCookie("nickname");       // Get the currently logged in user from a cookie
    let xmlhttp = new XMLHttpRequest();     // Create an empty HTTP request
    let url = "/AutoRecipe/user_data?nickname=" + nickname;     // Create a URL that'll be sent to the server

    xmlhttp.open("GET", url, true);     // Set method, URL of the request
    xmlhttp.setRequestHeader("Accept", "application/json");
    xmlhttp.setRequestHeader("Authorization", "Bearer " + getCookie("token"));

    xmlhttp.onreadystatechange = function ()
    {
        if (this.readyState == 4 && this.status == 200) {       // OK
            try{
				var response = JSON.parse(this.responseText);   // Parse the received text as JSON
			}
			catch(e){
				return;
			}

            var fragment = document.createDocumentFragment();		// Create a new fragment to put into the div element.

            // Go through the JSON formatted data
            for(var user_data_element in response)		
            {
                // Save the piece of data into div.
                let div = document.createElement("div");
                div.innerHTML = user_data_element + ": " + response[user_data_element];
                // Create the button that allows the user to change the data.
                let button = document.createElement("button");
                button.innerHTML = "Change";
                button.id = user_data_element;
                button.onclick = function () {
                    Array.prototype.slice.call(document.getElementsByTagName('form')).forEach(
                        function(item) {
                          item.remove();
                      });
                    let form = document.createElement("form");
                    form.id = "form";
                    form.onsubmit = function(){
                        changeUserData();
                        return false;
                    };
                    let input = document.createElement("input");
                    input.type = "text";
                    input.name = this.id;
                    input.required;
                    let input_button = document.createElement("button");
                    input_button.innerHTML = "Submit";
                    input_button.type = "submit";
                    form.appendChild(input);
                    form.appendChild(input_button);
                    div.appendChild(form);
                  };
                div.appendChild(button);
                // Add the completed div to fragment
                fragment.appendChild(div);
            }
            // Add the fragment to the main HTML's div where the data will be shown
            document.getElementById("user_data").innerHTML = "";
            document.getElementById("user_data").appendChild(fragment);
        }
        if (this.readyState == 4 && this.status != 200) {       // not OK
        console.log(this.status);
        }
    };

    xmlhttp.send();
}