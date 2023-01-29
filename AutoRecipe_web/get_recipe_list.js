function getRecipeList() {
    let nickname = getCookie("nickname");       // Get the currently logged in user from a cookie
    let xmlhttp = new XMLHttpRequest();     // Create an empty HTTP request
    let url = "/AutoRecipe/recipes?nickname=" + nickname;     // Create a URL that'll be sent to the server
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
            for(var i = 0; i < response.length; i++)	
            {
                // Save the piece of data into div.
                let div = document.createElement("div");
                let p = document.createElement("p");
                p.style = "display:inline";
                p.innerHTML = response[i]["recipe_name"];
                p.id = "recipeName" + response[i]["recipe_ID"];

                // Create the buttons that allow the user to go to the recipe's page and delete the recipe.
                let buttonEdit = document.createElement("button");
                buttonEdit.id = response[i]["recipe_ID"];
                buttonEdit.innerHTML = "Open recipe page";
                buttonEdit.onclick = function () {
                    setCookie("clicked_recipe_ID", this.id, 1);
                    setCookie("clicked_recipe_name", document.getElementById("recipeName" + this.id).innerHTML, 1);
                    window.location.replace("recipe_page.php");
                  };
                let buttonDelete = document.createElement("button");
                buttonDelete.innerHTML = "Delete";
                let deleted_recipes_id = response[i]["recipe_ID"];
                buttonDelete.onclick = function () {
                    deleteRecipe(deleted_recipes_id);   // Part of the change_recipe_data.js file
                  };
                div.appendChild(p);
                div.appendChild(buttonEdit);
                div.appendChild(buttonDelete);

                // Add the completed div to fragment
                fragment.appendChild(div);
            }
            // Add the fragment to the main HTML's div where the data will be shown
            document.getElementById("created_recipes").innerHTML = "";
            document.getElementById("created_recipes").appendChild(fragment);
        }
        if (this.readyState == 4 && this.status != 200) {       // not OK
            console.log(this.status);
        }
    };

    xmlhttp.send();
}